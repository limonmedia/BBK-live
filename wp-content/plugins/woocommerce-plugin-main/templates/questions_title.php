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
    <span id="js-lipscore-questions-tab">
        <?php echo __( 'Questions', 'lipscore' ) ?>
        <span id="js-lipscore-questions-tab-count" style="display: none;">&nbsp;(<span class="lipscore-question-count" <?php echo $this->widget_helper->product_attrs( $product_data ) ?>></span>)</span>
    </span>
<?php } ?>
