<?php 
add_action('woocommerce_before_single_variation', 'custom_variation_selector_with_stock');

function custom_variation_selector_with_stock() {
    global $product;

    if (!is_product() || (wp_doing_ajax() && isset($_REQUEST['action']) && $_REQUEST['action'] === 'woosq_quickview')) {
        return;
    }

    $available_variations = $product->get_available_variations();
    $attributes = $product->get_variation_attributes();
	
    usort($available_variations, function($a, $b) {
        $size_label_a = implode('/', array_values($a['attributes']));
        $size_label_b = implode('/', array_values($b['attributes']));
        return strnatcasecmp($size_label_a, $size_label_b);
    });
	
    echo '<style>
        .woosq-product.variations_form.variations { display: block !important; }

        .custom-variation-button {
            background-color: white !important;
            color: black !important;
            padding: 14px 100px !important;
            border: 1px solid !important;
            border-radius: 25px;
            text-transform: none !important;
            cursor: pointer;
            font-size: 17px !important;
        }
        @media (max-width: 768px) {
            .custom-variation-button {
                width: 88vw !important; /* width on mobile */
            }
        }

        .variation-overlay {
            position: fixed;
            top: 0px; left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.4);
            z-index: 9998;
            display: none;
        }

        .variation-overlay.active {
            display: block;
        }

        .custom-variation-panel {
            position: fixed;
            top: 0px;
            right: -100%;
            width: 340px;
            height: 100%;
            background: #fff;
            box-shadow: -2px 0 5px rgba(0,0,0,0.2);
            transition: right 0.3s ease-in-out;
            padding: 20px 15px;
            z-index: 9999;
            overflow-y: auto;
        }

        .custom-variation-panel.active {
            right: 0;
        }

        .custom-variation-panel .custom-variation-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 5px 5px !important;
            background: #ebebeb;
            border-radius: 5px;
            transition: background 0.2s ease;
            color: black;
            font-weight: 400;
            font-size: 12px;
        }

        .custom-variation-option .variation-left {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 400;
        }

        .custom-variation-option .variation-right {
            white-space: nowrap;
            font-weight: 400;
        }

        .custom-variation-panel .custom-variation-option:hover {
            background: #f0f0f0;
            cursor: pointer;
        }

        .custom-variation-panel .custom-variation-option.selected {
            background: gray;
            color: white;
        }

        .custom-variation-panel .custom-variation-option.disabled {
            background: #f5f5f5;
            color: #999;
            cursor: not-allowed;
        }

        .close-variation-panel {
            position: fixed;
            top: 30px;
            right: 320px;
            background: transparent !important;
            border: none;
            color: white;
            font-size: 40px;
            z-index: 10000;
            cursor: pointer;
            line-height: 1;
            padding: 0;
        }

        .close-variation-panel i {
            font-size: 20px;
            background: transparent;
        }

        .close-variation-panel.hidden {
            display: none;
        }

        .single_add_to_cart_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>';

    echo '<button type="button" class="custom-variation-button" id="open-variation-panel">Velg størrelse</button>';
    echo '<div class="variation-overlay" id="variationOverlay"></div>';
    echo '<button type="button" class="close-variation-panel hidden" id="closeVariationPanel" ><i class="fa-solid fa-x"></i></button>';
    echo '<div class="custom-variation-panel" id="variationPanel">';
	
    echo '<div class="" id="" style="color:black; margin-bottom: 20px !important;">Velg størrelse:</div>';
    echo '<div style="margin-bottom: 25px; border-top: 1px solid #ddd;"></div>';
	
    foreach ($available_variations as $variation) {
        $variation_obj = wc_get_product($variation['variation_id']);
        $stock_quantity = $variation_obj->get_stock_quantity();
        $is_in_stock = $variation_obj->is_in_stock();
        $variation_id = $variation['variation_id'];
        $attributes_data = $variation['attributes'];
        $size_label = implode('/', array_values($attributes_data));

        if ($is_in_stock) {
            $dot = '<span style="color:green; font-size:18px;">●</span>';
            if ($stock_quantity === null || $stock_quantity > 2) {
                $stock_text = 'På lager';
            } elseif ($stock_quantity <= 2 && $stock_quantity > 0) {
                $stock_text = 'Kun ' . $stock_quantity . ' igjen';
            } else {
                $stock_text = 'På lager';
            }
        } else {
            $dot = '<span style="color:red; font-size:18px;">●</span>';
            $stock_text = 'Utsolgt';
        }

        $attr_json = htmlspecialchars(json_encode($attributes_data), ENT_QUOTES, 'UTF-8');

        echo "<div 
            class='custom-variation-option' 
            data-variation-id='{$variation_id}' 
            data-attributes='{$attr_json}'>
            <span class='variation-left'>
                {$dot} <strong>{$size_label}</strong>
            </span>
            <span class='variation-right'>{$stock_text}</span>
          </div>";
    }

    echo '</div>'; // close panel
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const isQuickView = document.querySelector('.woosq-product') !== null;
            if (isQuickView) {
//                 console.log('In WPC Smart Quick View sidebar, skipping custom variation selector');
                return;
            }

            const options = document.querySelectorAll('.custom-variation-option');
            const form = document.querySelector('form.variations_form');
            const openBtn = document.getElementById('open-variation-panel');
            const closeBtn = document.getElementById('closeVariationPanel');
            const overlay = document.getElementById('variationOverlay');
            const panel = document.getElementById('variationPanel');
            const addToCartBtn = document.querySelector('.single_add_to_cart_button');

            // Check for variation styling
            const hasVariationStyling = form.querySelector('div.variation-styling ul.variable-items-wrapper.button-variable-items-wrapper.wvs-style-rounded') !== null;
            if (hasVariationStyling) {
                form.classList.add('has-variation-styling');
            } else {
//                 console.log('Variation-styling with button variations not found, skipping custom variation selector');
                return;
            }

            // Disable Add to Cart button by default
            if (addToCartBtn) {
                addToCartBtn.classList.add('disabled');
                addToCartBtn.disabled = true;
            }

            function openPanel() {
                panel.classList.add('active');
                overlay.classList.add('active');
                closeBtn.classList.remove('hidden');
            }

            function closePanel() {
                panel.classList.remove('active');
                overlay.classList.remove('active');
                closeBtn.classList.add('hidden');
            }

            function resetVariationSelection() {
                // Reset form select elements
                const selects = form.querySelectorAll('select');
                selects.forEach(select => {
                    select.value = '';
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    select.dispatchEvent(new Event('blur', { bubbles: true }));
                });

                // Remove selected class from options
                options.forEach(opt => opt.classList.remove('selected'));

                // Reset button text
                openBtn.textContent = 'Velg størrelse';

                // Disable Add to Cart button
                if (addToCartBtn) {
                    addToCartBtn.classList.add('disabled');
                    addToCartBtn.disabled = true;
                }

                // Trigger WooCommerce variation check
                if (typeof jQuery !== 'undefined') {
                    jQuery(form).trigger('check_variations');
                }
            }

            openBtn.addEventListener('click', function (e) {
                e.preventDefault();
                openPanel();
            });

            closeBtn.addEventListener('click', closePanel);
            overlay.addEventListener('click', closePanel);

            options.forEach(option => {
                if (option.innerHTML.includes('Utsolgt')) {
                    option.classList.add('disabled');
                    option.setAttribute('title', 'Out of stock');
                }
                option.addEventListener('click', function () {
                    if (this.classList.contains('disabled')) return;

                    options.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');

                    const attributes = JSON.parse(this.getAttribute('data-attributes'));
                    const variationLabel = this.querySelector('.variation-left strong')?.textContent?.trim() || 'Velg størrelse';
                    openBtn.textContent = variationLabel;

                    Object.keys(attributes).forEach(attr_name => {
                        const select = form.querySelector(`[name="${attr_name}"]`);
                        if (select) {
                            select.value = attributes[attr_name];
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                            select.dispatchEvent(new Event('blur', { bubbles: true }));
                        }
                    });

                    // Enable Add to Cart button
                    if (addToCartBtn) {
                        addToCartBtn.classList.remove('disabled');
                        addToCartBtn.disabled = false;
                    }

                    if (typeof jQuery !== 'undefined') {
                        jQuery(form).trigger('check_variations');
                    }

                    closePanel();
                });
            });

            // Reset variation after adding to cart
            if (typeof jQuery !== 'undefined') {
                jQuery(document).on('added_to_cart', function () {
                    resetVariationSelection();
                });
            }
        });
    </script>
    <?php
}