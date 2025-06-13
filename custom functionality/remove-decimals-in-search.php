<?php
/**
 * 1) Hide all price decimals on initial paint
 * 2) Trim “,00” or “.00” via JavaScript and reveal the cleaned prices
 */

/*-----------------------------------
 * 1) Output CSS in the <head> to hide all <bdi> inside .woocommerce-Price-amount
 *----------------------------------*/
add_action('wp_head', function() {
    echo '<style>
        /* Hide every .woocommerce-Price-amount bdi until JS cleans it */
        .woocommerce-Price-amount bdi {
            visibility: hidden;
        }
    </style>';
});


/*-----------------------------------
 * 2) Output JS in the <footer> to strip decimals and reveal cleaned prices
 *----------------------------------*/
add_action('wp_footer', function() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        function trimPriceDecimals() {
            document.querySelectorAll('.woocommerce-Price-amount bdi').forEach(function (el) {
                // 1) Grab any nested currency symbol
                const currencySpan = el.querySelector('.woocommerce-Price-currencySymbol');
                // 2) Get the “299,00” or “299.00” text
                const rawText = el.cloneNode(true).childNodes[0]?.nodeValue || '';
                // 3) Remove exactly “,00” or “.00” at the end
                const cleaned = rawText.replace(/([.,])\d{2}$/, '');
                // 4) Rewrite the <bdi> to “299” + “<span class='woocommerce-Price-currencySymbol'>kr</span>”
                el.innerHTML = cleaned + (currencySpan ? currencySpan.outerHTML : '');
                // 5) Reveal the cleaned price
                el.style.visibility = 'visible';
            });
        }

        // Run once immediately on page load
        trimPriceDecimals();

        // Re-run every second in case live-search/AJAX injects new prices
        setInterval(trimPriceDecimals, 1000);
    });
    </script>
    <?php
});
