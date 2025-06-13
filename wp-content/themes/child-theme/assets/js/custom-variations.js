jQuery(document).ready(function($) {
    function fetchVariations(productId, callback) {
        $.ajax({
            url: variationAjax.rest_url + 'products/' + productId + '/variations',
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', variationAjax.nonce);
            },
            success: function(data) {
                var variations = data.map(function(v) {
                    return Object.values(v.attributes).map(function(attr) {
                        return attr.option || '';
                    }).filter(Boolean).join('-');
                }).join(', ');
                callback(variations || 'No variations');
            },
            error: function(xhr) {
                console.log('Error fetching variations for ID ' + productId + ':', xhr.statusText);
                callback('Error fetching variations');
            }
        });
    }

    function addProductVariations() {
        $('.uc_image_carousel_container_holder').each(function() {
            var $item = $(this);
            var $title = $item.find('.uc_post_title');
            var $existingVariations = $item.find('.product-variations');

            if (!$existingVariations.length) {
                var productId = $item.find('.woosq-btn').data('id') || $item.find('.yith-wcwl-add-to-wishlist-button-block').data('product-id');
                if (productId) {
                    fetchVariations(productId, function(variationData) {
                        if (variationData && variationData !== 'No variations' && variationData !== 'Error fetching variations') {
                            var $variationDiv = $('<div class="product-variations" style="font-size: 12px; margin-top: 5px;"></div>');
                            var variationsArray = variationData.split(', ');
                            variationsArray.forEach(function(v, i) {
                                $variationDiv.append('<span>' + v.trim() + '</span>');
                                if (i < variationsArray.length - 1) {
                                    $variationDiv.append(', ');
                                }
                            });
                            $title.after($variationDiv);
                        }
                    });
                }
            }
        });
    }

    // Run on page load
    addProductVariations();

    // Run after common WooCommerce/Elementor AJAX events
    $(document).on('wc_fragments_refreshed', addProductVariations);
    $(document).on('updated_wc_div', addProductVariations);
    $(document).on('yith_wcan_ajax_filtered', addProductVariations);
    $(document).on('owl.carousel.initialized', addProductVariations); // For Owl Carousel
});
