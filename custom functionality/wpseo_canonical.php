<?php
add_filter('wpseo_canonical', 'custom_yoast_canonical_for_pagination', 20);

function custom_yoast_canonical_for_pagination($canonical) {
    if (isset($_GET['_page']) && intval($_GET['_page']) > 1) {
        // Remove any existing _page query param to avoid duplication
        $canonical = remove_query_arg('_page', $canonical);
        // Add the current _page param
        $canonical = add_query_arg('_page', intval($_GET['_page']), $canonical);
    }
    return $canonical;
}

	
	
	

// Add custom rel="next" and rel="prev" links
add_action('wp_head', 'custom_rel_next_prev_links', 1);

function custom_rel_next_prev_links() {
    if (isset($_GET['_page'])) {
        global $wp_query;

        $current_page = intval($_GET['_page']);
        $base_url = remove_query_arg('_page');
        $total_pages = $wp_query->max_num_pages; // Total pages available for the query

        // Generate the "next" link only if there's a next page
        if ($current_page < $total_pages) {
            $next_page = $current_page + 1;
            echo '<link rel="next" href="' . esc_url(add_query_arg('_page', $next_page, $base_url)) . '" />' . "\n";
        }

        // Generate the "prev" link only if there's a previous page
        if ($current_page > 1) {
            $prev_page = $current_page - 1;
            echo '<link rel="prev" href="' . esc_url(add_query_arg('_page', $prev_page, $base_url)) . '" />' . "\n";
        }
    }
}
// Disable Yoast SEO next/prev rel links
add_filter('wpseo_next_rel_link', '__return_false');
add_filter('wpseo_prev_rel_link', '__return_false');

// Remove default WooCommerce pagination
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );