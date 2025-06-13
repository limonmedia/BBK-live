<?php
add_shortcode('bottom_text', function () {
    if (is_category() || is_tax()) {
        $term = get_queried_object();
        $text = get_field('bottom_text', $term);
        return '<div class="custom-bottom-text">' . $text . '</div>';
    }
    return '';
});
