
<?php

// Don't access this directly, please
if ( ! defined( 'ABSPATH' ) ) exit;




/* Code displayed before the tabs (outside) Tabs */
?>

<div class="wrap">
<h1>
    <?php



    $plugin_data = get_plugin_data(plugin_dir_path(__FILE__) .'../woo-discount-price.php', true, true);
    $plugin_version = $plugin_data['Version'];

    if ( is_admin() ) {
        echo __(
            'WooCommerce Discount Price '
            .$plugin_version, 'woo-discount-price'
        );

    }
    ?>
</h1>



<form method="POST">
	<label for="woodiscpr_you_save"><?php echo __( 'Enable "You Saved" in cart and checkout page', 'woo-discount-price' );?></label>

	<input type="hidden" name="woodiscpr_you_save" value="0" />
	<input type="checkbox" name="woodiscpr_you_save" id="woodiscpr_you_save" value="1" <?php if ( 1 == get_option('woodiscpr_you_save') ) echo 'checked';

	else echo '';

	?>>

	<input type="submit" value="Save" class="button button-primary button-large">
</form>

<form method="POST">
    <label for="woodiscpr_taxexcl"><?php echo __( 'Disable "Tax Excluded/Included"', 'woo-discount-price' );?></label>

    <input type="hidden" name="woodiscpr_taxexcl" value="0" />
    <input type="checkbox" name="woodiscpr_taxexcl" id="woodiscpr_taxexcl" value="1" <?php if ( 1 == get_option('woodiscpr_taxexcl') ) echo 'checked';

	else echo '';

	?>>

    <input type="submit" value="Save" class="button button-primary button-large">
</form>

<!--
<hr>
<p>

-->
<?php
/*
_e('Buy <strong><a href="https://wpnetwork.it/shop/woocommerce-discount-price-premium/">WooCommerce Discount Price Premium plugin</a>!</strong>
<br>In the Premium Version the <b><strike>original price</strike>/discount price</b> are displayed on these WooCommerce <strong>emails</strong>:<br>
New Order <strong>email</strong> | Processing Order <strong>email</strong> | Completed Order <strong>email</strong> | Customer Invoice <strong>email</strong> | Customer Note <strong>email</strong>', 'woo-discount-price');

*/
?>

<!--
<br>


</p>
<hr>
<div id="woo_discount_email">
<a href="https://wpnetwork.it/shop/woocommerce-discount-price-premium/"><header></header></a>
</div>

</div>

-->