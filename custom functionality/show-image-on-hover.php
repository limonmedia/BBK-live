<?php
// Add hover image to product loop
add_action('woocommerce_before_shop_loop_item_title', 'add_hover_image', 11);
function add_hover_image() {
    global $product;
    $attachment_ids = $product->get_gallery_image_ids();
    if ($attachment_ids && isset($attachment_ids[0])) {
        $hover_image = wp_get_attachment_image_src($attachment_ids[0], 'woocommerce_thumbnail');
        if ($hover_image) {
            echo '<img class="hover-image" src="' . esc_url($hover_image[0]) . '" alt="Hover Image" />';
        }
    }
}
