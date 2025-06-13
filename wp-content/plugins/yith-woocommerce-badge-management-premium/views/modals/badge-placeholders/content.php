<?php
/**
 * Badge Placeholders Modal Content
 *
 * @package YITH\BadgeManagement\Views
 * @author  YITH <plugins@yithemes.com>
 */

$placholders = yith_wcbm_get_badges_placeholders();

$tabs = array(
	'simple-products'   => array(
		'title'               => _x( 'Simple products', '[ADMIN] Badge placeholders modal', 'yith-woocommerce-badges-management' ),
		'placeholders_filter' => function ( $options, $placeholder ) {
			return ! empty( $options['desc'] ) && ! preg_match( '/(?:min|max)/mi', $placeholder );
		},
	),
	'variable-products' => array(
		'title'               => _x( 'Variable products', '[ADMIN] Badge placeholders modal', 'yith-woocommerce-badges-management' ),
		'desc'                => _x( '<b>Note:</b> in variable products by default, placeholders show the minimum value. To show the maximum value, you must replace min_ with max_ in the placeholder after pasting it.', '[ADMIN] Badge placeholders modal', 'yith-woocommerce-badges-management' ),
		'placeholders_filter' => function ( $options, $placeholder ) {
			return ! empty( $options['desc'] ) && preg_match( '/(?:min|max)/mi', $placeholder );
		},
	),
);

?>
<div class="yith-wcbm-placeholders-list-wrapper">

	<ul class="yith-plugin-fw__tabs">
		<?php foreach ( $tabs as $tab_key => $tab ) : ?>
			<li class="yith-plugin-fw__tab">
				<a class="yith-plugin-fw__tab__handler" href="#<?php echo esc_attr( $tab_key ); ?>"><?php echo wp_kses_post( $tab['title'] ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php foreach ( $tabs as $tab_key => $tab ) : ?>
		<div class="yith-wcbm-placeholders-list-wrapper yith-plugin-fw__tab-panel" id="<?php echo esc_attr( $tab_key ); ?>">

			<?php if ( ! empty( $tab['desc'] ) ): ?>
				<p class="yith-wcbm-placeholders-list-desc">
					<?php echo wp_kses_post( $tab['desc'] ) ?>
				</p>
			<?php endif; ?>

			<div class="yith-wcbm-placeholders-list">
				<?php foreach ( ( is_callable( $tab['placeholders_filter'] ?? false ) ? array_filter( $placholders, $tab['placeholders_filter'], ARRAY_FILTER_USE_BOTH ) : $placholders ) as $placeholder => $options ) : ?>
					<?php yith_wcbm_get_view( 'modals/badge-placeholders/placeholder.php', compact( 'placeholder', 'options' ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
