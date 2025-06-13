<?php 
add_action('wp_footer', function() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function insertShortcode() {
            const footerButtons = document.querySelector('.elementor-menu-cart__footer-buttons');
            if (footerButtons && !document.querySelector('.wpcfb-shortcode-before-buttons')) {
                const shortcodeHTML = `<div class="wpcfb-shortcode-before-buttons">
                    <?php echo do_shortcode('[wpcfb]'); ?>
                </div>`;
                const wrapper = document.createElement('div');
                wrapper.innerHTML = shortcodeHTML;
                footerButtons.parentNode.insertBefore(wrapper, footerButtons);
            }
        }

        insertShortcode();

        const observer = new MutationObserver(insertShortcode);
        observer.observe(document.body, { childList: true, subtree: true });
    });
    </script>
    <?php
});
