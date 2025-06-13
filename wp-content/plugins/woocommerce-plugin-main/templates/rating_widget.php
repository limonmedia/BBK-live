<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( empty ( $product ) ) {
    return;
}
$product_data = $this->product_helper->product_data( $product );
$rs_product_data = $this->product_helper->richsnippet_product_data( $product );
?>

<?php if ( $this->widget_helper->product_attrs( $product_data ) ) { ?>
    <div <?php echo $this->rating_type ?>
         <?php echo $this->widget_helper->product_attrs( $product_data ) ?>
         <?php echo $this->widget_helper->richsnippet_product_attrs( $rs_product_data ) ?>>
    </div>
<?php } else { ?>
    <div style="display: none !important;">Lipscore: empty product data</div>
<?php } ?>
