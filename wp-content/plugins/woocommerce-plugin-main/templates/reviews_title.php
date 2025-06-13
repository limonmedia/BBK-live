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

<?php if ( $this->widget_helper->product_attrs( $product_data ) ) { ?>
    <span id="js-lipscore-reviews-tab">
        <?php echo __( 'Reviews', 'woocommerce' ) ?>
        <span id="js-lipscore-reviews-tab-count" style="display: none;">&nbsp;(<span class="lipscore-review-count" <?php echo $this->widget_helper->product_attrs( $product_data ) ?>></span>)</span>
    </span>
<?php } ?>
