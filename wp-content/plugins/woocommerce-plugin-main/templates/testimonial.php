<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( empty ( $product ) ) {
    return;
}
$product_data = $this->product_helper->product_data( $product );
?>

<?php if ( $this->widget_helper->product_attrs( $product_data ) ): ?>
  <div class="lipscore-testimonial" <?php echo $this->widget_helper->product_attrs( $product_data ) ?>></div>
<?php endif; ?>
