<?php

// Customize WooCommerce pagination to use clean category URL without filter parameters
add_filter('woocommerce_pagination_args', 'custom_woocommerce_pagination_args');
function custom_woocommerce_pagination_args($args) {
    // Get the current category or shop page URL
    $current_term = is_product_category() ? get_queried_object() : null;
    $base_url = $current_term && $current_term instanceof WP_Term ? get_term_link($current_term) : get_permalink(woocommerce_get_page_id('shop'));

    // Normalize base URL
    $category_path = parse_url($base_url, PHP_URL_PATH);
    $category_path = trim($category_path, '/');
    $base_url = trailingslashit(home_url()) . $category_path . '/';

    // Log base URL
    error_log('WooCommerce Base URL: ' . $base_url);

    // Set base URL and format for pagination
    $args['base'] = $base_url . 'page/%#%/';
    $args['format'] = 'page/%#%/';
    $args['add_args'] = []; // Do not include filter parameters

    return $args;
}

// Customize WP Grid Builder pagination to use clean category URL without filter parameters
add_filter('wpgb_pagination_args', 'custom_wpgb_pagination_args', 10, 2);
function custom_wpgb_pagination_args($args, $grid_id) {
    // Get the current category or shop page URL
    $current_term = is_product_category() ? get_queried_object() : null;
    $base_url = $current_term && $current_term instanceof WP_Term ? get_term_link($current_term) : get_permalink(woocommerce_get_page_id('shop'));

    // Normalize base URL
    $category_path = parse_url($base_url, PHP_URL_PATH);
    $category_path = trim($category_path, '/');
    $base_url = trailingslashit(home_url()) . $category_path . '/';

    // Log base URL
    error_log('WPGB Base URL: ' . $base_url);

    // Set base URL and format for pagination
    $args['base'] = $base_url . 'page/%#%/';
    $args['format'] = 'page/%#%/';
    $args['add_args'] = []; // Do not include filter parameters

    return $args;
}

// Add JavaScript to fix pagination URLs after WPGB AJAX filter application
add_action('wp_footer', 'fix_wpgb_pagination_urls');
function fix_wpgb_pagination_urls() {
    if (is_product_category() || is_shop()) {
        ?>
        <script type="text/javascript">
            (function($) {
                // Get dynamic category base URL
                var categoryBase = '<?php echo esc_js(trailingslashit(is_product_category() ? get_term_link(get_queried_object()) : get_permalink(woocommerce_get_page_id('shop')))); ?>';
                console.log('Category Base URL:', categoryBase);

                // Clean duplicate category paths dynamically
                function cleanCategoryPath(url) {
                    var categoryPath = categoryBase.replace(/^https?:\/\/[^\/]+/, '').replace(/\/$/, '');
                    var regex = new RegExp('(' + categoryPath.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&') + '/)+', 'g');
                    return url.replace(regex, categoryPath + '/');
                }

                // Fix pagination links
                function updatePaginationLinks() {
                    $('.wpgb-pagination a, .woocommerce-pagination a').each(function() {
                        var $link = $(this);
                        var href = $link.attr('href');
                        if (!href) return;

                        console.log('Original Link:', href);

                        // Extract page number
                        var pageNum = '1';
                        if (href.includes('page/')) {
                            var match = href.match(/page\/(\d+)/);
                            pageNum = match ? match[1] : '1';
                        } else if (href.includes('page=')) {
                            var match = href.match(/page=(\d+)/);
                            pageNum = match ? match[1] : '1';
                        } else if (href.match(/\d+$/)) {
                            var match = href.match(/\d+$/);
                            pageNum = match ? match[0] : '1';
                        }

                        // Build clean URL
                        var newHref = categoryBase + 'page/' + pageNum + '/';
                        newHref = cleanCategoryPath(newHref);

                        // Remove any query parameters
                        newHref = newHref.split('?')[0];

                        $link.attr('href', newHref);
                        console.log('Cleaned Link:', newHref);
                    });
                    console.log('Pagination links updated');
                }

                // Run on page load
                $(document).ready(function() {
                    updatePaginationLinks();
                });

                // Run after WPGB AJAX filter application
                $(document).on('wpgb.render', function() {
                    updatePaginationLinks();
                });
            })(jQuery);
        </script>
        <?php
    }
}
