<?php

add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Helper: as soon as any <li class="woocommerce-error"> is added, alert + hide .notiny-container
        function onWooErrorNode(node) {
            if (node.nodeType !== 1) return;

            // Case A: A single <li class="woocommerce-error"> was just added
            if (node.matches('.woocommerce-error li')) {
//                 alert('Error detected in WooCommerce!');
                document.querySelectorAll('.notiny-container').forEach(function(el){
                    el.style.display = 'none';
                    el.style.visibility = 'hidden';
                    el.style.opacity = '0';
                });
                return;
            }

            // Case B: An entire <ul class="woocommerce-error"> was added
            if (node.matches('.woocommerce-error')) {
                node.querySelectorAll('li').forEach(function(innerLi){
//                     alert('Error detected in WooCommerce!');
                    document.querySelectorAll('.notiny-container').forEach(function(el){
                        el.style.display = 'none';
                        el.style.visibility = 'hidden';
                        el.style.opacity = '0';
                    });
                });
            }
        }

        // 1) Initial check (in case an error is already on the page when it loads)
        document.querySelectorAll('.woocommerce-error li').forEach(function(li){
            onWooErrorNode(li);
        });

        // 2) Observe the notices wrapper for dynamically added WooCommerce errors (AJAX, etc.)
        const wrapper = document.querySelector('.woocommerce-notices-wrapper');
        if (wrapper) {
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    mutation.addedNodes.forEach(onWooErrorNode);
                });
            });
            observer.observe(wrapper, { childList: true, subtree: true });
        }
    });
    </script>
    <?php
});
