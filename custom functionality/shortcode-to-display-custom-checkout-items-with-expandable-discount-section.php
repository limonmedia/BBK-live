<?php

// Shortcode to display custom cart items with expandable discount section
function custom_checkout_display() {
    ob_start();
    $cart_items = WC()->cart->get_cart();
    ?><h1 class="shopping-cart-title">Handlekurv</h1>
    <div class="order-summary-header">
        <div class="order-step">1</div>
        <h2 class="order-title">Ordresammendrag</h2>
    </div>
    <?php
    $total_saved_price = 0;
    foreach ($cart_items as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        $product_id = $cart_item['product_id'];
        $product_name = $product->get_name();
		$parts = array_map('trim', explode('-', $product_name));
        $product_name = implode(' - ', array_slice($parts, 0, 2));
        $product_price = $product->get_price();
        $regular_price = $product->get_regular_price();
        $is_on_sale = $product->is_on_sale();
        
		// Calculate saved price for this product
    $saved_price = $is_on_sale && $regular_price > $product_price ? $regular_price - $product_price : 0;
    
    // Add to total saved price (multiply by quantity if applicable)
    $total_saved_price += $saved_price * $cart_item['quantity'];
		
        $brand = function_exists('get_field') ? get_field('brand', $product_id) : '';
        if (empty($brand)) {
            $categories = get_the_terms($product_id, 'product_cat');
            if (!empty($categories) && !is_wp_error($categories)) {
                $brand = $categories[0]->name;
            }
        }
        
        $attributes = [];
        if ($product->is_type('variation')) {
            $variation_attributes = $product->get_variation_attributes();
            foreach ($variation_attributes as $attribute_name => $attribute_value) {
                $taxonomy = str_replace('attribute_', '', $attribute_name);
                $attribute_label = wc_attribute_label($taxonomy);
                $attributes[$attribute_label] = $attribute_value;
            }
        } else {
            $product_attributes = $product->get_attributes();
            foreach ($product_attributes as $attribute) {
                if ($attribute->get_visible()) {
                    $attribute_label = wc_attribute_label($attribute->get_name());
                    $attribute_values = $attribute->is_taxonomy() ? wc_get_product_terms($product_id, $attribute->get_name(), ['fields' => 'names']) : $attribute->get_options();
                    $attributes[$attribute_label] = implode(', ', $attribute_values);
                }
            }
        }
        
        ?>
        <div class="elementor-container product-item">
            <div class="elementor-col-33 product-image">
				<a href="<?php echo esc_url($product->get_permalink()); ?>"><?php 
				$image_id = $product->get_image_id();
				$url = wp_get_attachment_image_url($image_id,'full');
				?>
					<img src="<?php echo esc_url($url); ?>" alt="" /></a>
			</div>
            <div class="elementor-col-33 product-details">
<!--                 <div class="product-brand"><?php echo esc_html($brand); ?></div> -->
                <div class="product-name"><a style="color:#000;"href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product_name); ?></a></div>
                <div class="product-attributes">
                    <?php foreach ($attributes as $label => $value): ?>
                    <div class="product-attribute"><?php echo esc_html($label); ?>: <?php echo esc_html($value); ?></div>
                    <?php endforeach; ?>
                </div>
<!--                 <div class="product-quantity">
                    <button class="quantity-btn"><span class="cq b y p cf a0 cr cs ct ak cu cv bq br"><svg class="quantity-svg" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ci cw cx cy d8"><line x1="5" y1="12" x2="19" y2="12"></line></svg></span></button>
                    <input type="text" class="quantity-input" name="<?php echo esc_attr($cart_item_key); ?>" value="<?php echo esc_attr($cart_item['quantity']); ?>" readonly>
                    <button class="quantity-btn"><span class="cq b y p cf a0 cr cs ct ak cu cv bq br"><svg class ="quantity-svg" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ci cw cx cy d8"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></span></button>
                </div> -->
            </div>
            <div class="elementor-col-33 price-container">
                <div class="product-price"><?php echo $is_on_sale ? wc_price($product_price) : wc_price($product_price); ?></div>
                <?php if ($is_on_sale && $regular_price > $product_price): ?>
                <div class="product-original-price"><?php echo wc_price($regular_price); ?></div>
                <?php endif; ?>
                <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>" class="product-remove">Fjern</a>
            </div>
        </div>
        <?php
    }
    
    ?>

    <div class="checkout-collapsible">
        <div class="collapsible-header" id="header1">
            <span class="icon">+</span>
            <span>Legg til rabattkode</span>
        </div>
        <div class="collapsible-content" id="content1">
            <div class="discount-code-container">
                <input type="text" class="discount-input" placeholder="Bruk rabattkode">
                <button class="activate-btn">Aktiver</button>
            </div>
        </div>
    </div>
 <div class="checkout-collapsible border-bottom">
        <div class="collapsible-header" id="header2">
            <span class="icon">+</span>
            <span>Legg til gavekort</span>
        </div>
        <div class="collapsible-content" id="content2">
            <div class="discount-code-container">
                <input type="text" class="discount-input" placeholder="Gavekort-kode">
                <button class="activate-btn">Aktiver</button>
            </div>
        </div>
    </div>
    
    <!-- Order Summary -->
    <?php
    $subtotal = WC()->cart->get_subtotal();
    $discount_total = WC()->cart->get_discount_total();
    $shipping_total = WC()->cart->get_shipping_total();
    $tax_total = WC()->cart->get_total_tax();
	$subtotal = $subtotal*1.25;
	$shipping_total = $shipping_total*1.25;
    $total = WC()->cart->get_total( 'edit' ); // Full total including shipping, taxes, discounts
    ?>
    <div class="order-summary">
        <h2 class="summary-title">Oppsummering</h2>
        <div class="summary-row sub-total">
            <div>Totalt</div>
            <div><?php echo wc_price( $subtotal ); ?></div> <!-- Use full total here -->
        </div>
		
  		 
        <?php if ($discount_total > 0): ?>
        <div class="summary-row">
			<div>Rabattkode</div>
			<div class="negative-value" style="font-weight:normal;"> 
				<?php 
				// Get discount including VAT
				$discount_total_with_tax = WC()->cart->get_discount_total() + WC()->cart->get_discount_tax();
				echo round($discount_total_with_tax).",-";
				?>
			</div>
		</div>
		
        <?php endif; ?>
		<div class="summary-row">
            <div>Frakt</div>
            <div><?php echo wc_price( $shipping_total ); ?></div> 
        </div>
        <!-- <div class="summary-row vat">
            <div>Du har spart</div>
            <div class="negative-value"><?php echo wc_price($total_saved_price); ?></div>
        </div>-->
		

<div class="summary-row vat">
            <div>Herav mva</div>
            <div style="font-weight:normal;"><?php 
			$net_amount = (($subtotal - $discount_total_with_tax)+$shipping_total) /1.25;
			$tax_incl_discount = (($subtotal - $discount_total_with_tax)+$shipping_total)-$net_amount;
			
			echo number_format($tax_incl_discount,2).",-"; ?></div>
        </div>
<div class="summary-row ">
    <div style="font-weight:bold;">Totalt å betale</div>
    <div style="font-weight:bold;"><?php 
	$totalpris = ($subtotal- $discount_total_with_tax)+$shipping_total;
	echo round($totalpris).",-";
		?></div>
</div>
		
<!-- 		<div class="summary-row total">
            <div>Totalpris</div>
            <div></div>
        </div> -->
        <?php echo do_shortcode('[wpcfb]'); ?>
        <div class="go-to-payment">
            <button class="payment-btn">VIDERE TIL BETALING</button>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('custom_checkout_display', 'custom_checkout_display');