<?php
/**
 * Paginate
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2024 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle custom pagination
 *
 * @class WP_Grid_Builder\Includes\Paginate
 * @since 1.0.0
 */
final class Paginate {

	/**
	 * Previous page number
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var integer
	 */
	private $prev_page = 1;

	/**
	 * Holds pagination settings
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var object
	 */
	private $settings;

	/**
	 * Initialize
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds pagination arguments.
	 */
	public function __construct( $settings ) {

		$this->extend( $settings );
		$this->normalize();

		if ( $this->total > 1 ) {
			$this->render();
		}
	}

	/**
	 * Magic get method
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $key Key settings to retrieve.
	 * @return mixed.
	 */
	public function __get( $key ) {

		return $this->settings->$key;

	}

	/**
	 * Magic set method
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $key Key to add.
	 * @param string $val Value to set.
	 * @return mixed.
	 */
	public function __set( $key, $val ) {

		$this->settings->$key = $val;

	}

	/**
	 * Magic isset method
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $key Key settings to check against.
	 * @return boolean.
	 */
	public function __isset( $key ) {

		return isset( $this->settings->$key );

	}

	/**
	 * Extend arguments
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $settings Holds pagination settings.
	 */
	public function extend( $settings ) {

		$this->settings = (object) wp_parse_args(
			$settings,
			[
				'base'      => '',
				'format'    => '',
				'total'     => 1,
				'current'   => 1,
				'show_all'  => false,
				'prev_text' => __( '&laquo; Previous', 'wp-grid-builder' ),
				'next_text' => __( 'Next &raquo;', 'wp-grid-builder' ),
				'dots_page' => __( '&hellip;', 'wp-grid-builder' ),
				'end_size'  => 1,
				'mid_size'  => 2,
				'disabled'  => false,
				'classes'   => [
					'page'    => 'wpgb-admin-page',
					'dots'    => 'wpgb-dots-page',
					'current' => 'wpgb-current-page',
					'holder'  => 'wpgb-admin-pagination',
				],
			]
		);
	}

	/**
	 * Normalize numbers
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function normalize() {

		$this->total    = (int) $this->total;
		$this->current  = max( 1, min( $this->total, (int) $this->current ) );
		$this->mid_size = max( 1, (int) $this->mid_size );
		$this->end_size = max( 1, (int) $this->end_size );

	}

	/**
	 * Build and output pagination
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render() {

		$this->start();
		$this->prev();

		if ( $this->show_all || $this->total <= $this->end_size * 2 ) {

			$this->loop(
				[
					'from' => 1,
					'to'   => $this->total,
				]
			);

		} else {
			$this->render_bounds();
		}

		$this->next();
		$this->end();

	}

	/**
	 * Render each bound
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function render_bounds() {

		foreach ( [ 'start', 'middle', 'end' ] as $bound ) {

			$steps = $this->get_steps( $bound );

			// Add dots if there is a gap with previous page.
			if ( $steps['from'] - $this->prev_page > 1 ) {
				$this->dots();
			}

			$this->loop( $steps );

			// Set previous page number.
			$this->prev_page = $steps['to'];

		}
	}

	/**
	 * Get pagination steps
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $bound Steps bound position.
	 * @return array
	 */
	public function get_steps( $bound ) {

		switch ( $bound ) {
			case 'start':
				return [
					'from' => 1,
					'to'   => $this->end_size,
				];
			case 'middle':
				return [
					'from' => max( $this->end_size + 1, $this->current - $this->mid_size ),
					'to'   => min( $this->total - $this->end_size, $this->current + $this->mid_size ),
				];
			default:
				return [
					'from' => $this->total - $this->end_size + 1,
					'to'   => $this->total,
				];
		}
	}

	/**
	 * Loop throught pages
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $steps From -> to steps.
	 */
	public function loop( $steps ) {

		for ( $i = $steps['from']; $i <= $steps['to']; $i++ ) {
			$this->page( $i, $i );
		}
	}

	/**
	 * Pagination start
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function start() {

		echo '<ul class="' . sanitize_html_class( $this->classes['holder'] ) . '">';

	}

	/**
	 * Pagination end
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function end() {

		echo '</ul>';

	}

	/**
	 * Render prev button
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function prev() {

		if ( empty( $this->prev_text ) || 1 === $this->current ) {
			return;
		}

		$this->page( $this->current - 1, $this->prev_text, 'wpgb-page-prev' );

	}

	/**
	 * Render next button
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function next() {

		if ( empty( $this->next_text ) || $this->current === $this->total ) {
			return;
		}

		$this->page( $this->current + 1, $this->next_text, 'wpgb-page-next' );

	}

	/**
	 * Render dots
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function dots() {

		if ( empty( $this->dots_page ) ) {
			return;
		}

		$this->page( false, $this->dots_page );

	}

	/**
	 * Render page
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $number Page number.
	 * @param string  $text   Page text content.
	 * @param string  $class  Page classname.
	 */
	public function page( $number = '', $text = '', $class = '' ) {

		/* translators: %d: Page number */
		$aria_label  = sprintf( __( 'Go to Page %d', 'wp-grid-builder' ), $number );
		$page_format = '<li class="%1$s"%3$s%4$s%5$s>%7$s</li>';
		$is_selected = $this->current === $number;

		// Add link if available.
		if ( $number && ! empty( $this->base ) && ! empty( $this->format ) ) {
			$page_format = $this->disabled ?
				'<li class="%1$s"><a role="link" aria-disabled="true" %3$s%4$s%5$s>%7$s</a></li>' :
				'<li class="%1$s"><a href="%6$s"%3$s%4$s%5$s>%7$s</a></li>';
		} elseif ( false === $number ) {
			$page_format = '<li class="%1$s"><span class="%2$s">%7$s</span></li>';
		}

		if ( $is_selected ) {
			/* translators: %d: Page number */
			$aria_label = sprintf( __( 'Current Page, Page %d', 'wp-grid-builder' ), $number );
		}

		printf(
			$page_format, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			trim( sanitize_html_class( $this->classes['page'] ) . ' ' . sanitize_html_class( $class ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			sanitize_html_class( $this->classes['dots'] ),
			( $is_selected ? ' aria-current="true"' : '' ),
			( ! empty( $number ) ? ' aria-label="' . esc_attr( $aria_label ) . '"' : '' ),
			( ! empty( $number ) ? ' data-page="' . esc_attr( $number ) . '"' : '' ),
			esc_url( $this->base . $this->format . '=' . $number ),
			esc_html( $text )
		);
	}
}
