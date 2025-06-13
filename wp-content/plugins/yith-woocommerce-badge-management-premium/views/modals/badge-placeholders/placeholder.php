<?php
/**
 * Placeholder
 *
 * @package YITH\BadgeManagement\Views\Modals\BadgePlaceholders
 * @since   3.2.0
 * @var string $placeholder The placeholder string
 * @var array  $options     Addition placeholder options
 *
 */

?>
<div class="yith-wcbm-placeholder-wrapper">
	<div class="yith-wcbm-placeholder-content">
		<span class="yith-wcbm-placeholder-description">
			<?php echo esc_html( $options['desc'] ?? '' ); ?>
		</span>
		<span class="yith-wcbm-placeholder-input">
			<?php
			yith_plugin_fw_get_field(
				array(
					'id'    => 'yith-wcbep-placeholder-' . $placeholder,
					'type'  => 'copy-to-clipboard',
					'value' => '{{' . $placeholder . '}}',
				),
				true,
				false
			);
			?>
		</span>
	</div>
	<?php if ( ! empty( $options['note'] ) ) : ?>
		<span class="yith-wcbm-placeholder-note">
			<?php echo esc_html( $options['note'] ); ?>
		</span>
	<?php endif; ?>
</div>
