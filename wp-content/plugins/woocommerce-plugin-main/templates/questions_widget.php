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
    <div id="lipscore-question-post" <?php echo $this->widget_helper->product_attrs( $product_data ) ?>></div>
    <div id="lipscore-question-list" <?php echo $this->widget_helper->product_attrs( $product_data ) ?>></div>
<?php } else { ?>
    <div style="display: none !important;">Lipscore: empty product data</div>
<?php } ?>
