<?php
  if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
  }

  $widget_width = isset( $this->width ) ? $this->width : "100%";
  $widget_height = isset( $this->height ) ? $this->height : "150px";
  $custom_classes = isset( $this->class ) ? $this->class : "";
?>

<div class="lipscore-service-review-testimonial <?php echo esc_attr( $custom_classes ) ?>"
    ls-widget-height="<?php echo esc_attr( $widget_height ); ?>"
    ls-widget-width="<?php echo esc_attr( $widget_width ); ?>">
</div>
