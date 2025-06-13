<?php
/**
 * Trim zeros in price decimals
 **/
 add_filter( 'woocommerce_price_trim_zeros', '__return_true' );

add_filter('woocommerce_get_price_html', function ($price, $product) {
    return preg_replace('/(\d+)[\.,]\d{2}/', '$1', $price);
}, 100, 2);

add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.woocommerce-Price-amount bdi').forEach(function (el) {
        let currencySpan = el.querySelector('.woocommerce-Price-currencySymbol');
        let priceText = el.cloneNode(true).childNodes[0].nodeValue;

        let cleanedPrice = priceText.replace(/([\.,]\d{2})/, '');

        if (currencySpan) {
          el.innerHTML = cleanedPrice + currencySpan.outerHTML;
        } else {
          el.innerText = cleanedPrice;
        }
      });
    });
    </script>
    <?php
});
