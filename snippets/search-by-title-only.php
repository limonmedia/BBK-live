<?php

add_action('wp_footer', function() {
    ?>
    <script>
     jQuery(document).ready(function($) {

        jQuery(document).ready(function($) {
        // Target your specific input field
        $('.uc-ajax-search__input').on('input', function () {
            var val = $(this).val();
            val = val.replace(/–|—/g, '-'); // Normalize dash
            $(this).val(val);
            
        });
    });
        $(document).on('ajaxComplete', function(event, xhr, settings) {
            if (settings.url.includes('ucfrontajaxaction=ajaxsearch')) {
                var searchTerm = new URLSearchParams(settings.url).get('ucs').toLowerCase();

				

                if (!searchTerm) return;

                var $htmlItems = $('.uc-search-item');
                var visibleItems = 0;

                $htmlItems.each(function() {
                    var $title = $(this).find('.uc-search-item__link-title');
                    var titleText = $title.text().toLowerCase();

                    if (!titleText.includes(searchTerm)) {
                        $(this).hide();
                    } else {
                        $(this).show();
                        visibleItems++;
                    }
                });

				//set pagination
             
                var itemsPerPage = 5; 
                $('.uc-ajax-search-results').text(visibleItems + '-9/9').text(visibleItems + '-' + visibleItems + '/' + visibleItems); 
                if (visibleItems <= itemsPerPage) {
                    $('.uc-ajax-search-navigation-panel .uc-page-number').hide(); 
                    $('.uc-ajax-arrows').hide(); 
                } else {
                    $('.uc-ajax-search-navigation-panel .uc-page-number').show();
                    $('.uc-ajax-arrows').show();
                }
            }
        });
    });
    </script>
    <?php
});