<?php
// Remove Elementor's default meta description tag
function remove_hello_elementor_description_meta_tag() {
    remove_action('wp_head', 'hello_elementor_add_description_meta_tag');
}
add_action('after_setup_theme', 'remove_hello_elementor_description_meta_tag');
