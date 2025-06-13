<?php
/**
 * Facets
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2024 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\FrontEnd;

use WP_Grid_Builder\Includes\FrontEnd\Facets;
use WP_Grid_Builder\Includes\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add facets in layout
 *
 * @class WP_Grid_Builder\FrontEnd\Facets
 * @since 1.0.0
 */
final class Facets extends Objects implements Interfaces\Facets_Interface {

	/**
	 * Holds settings properties
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var WP_Grid_Builder\FrontEnd\Settings instance
	 */
	public $settings;

	/**
	 * Holds selected facets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $selections = [];

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $settings Hold Settings.
	 */
	public function __construct( $settings ) {

		$this->settings = $settings;

	}

	/**
	 * Query and refresh facets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param boolean $render Whether to render facet content.
	 * @return array Holds all facets data.
	 */
	public function refresh( $render = true ) {

		return array_map(
			function( $facet ) use ( $render ) {

				$choices = [];
				$content = '';

				if ( $this->maybe_handle( $facet ) ) {

					$choices = $this->query_facet( $facet );
					$content = $render ? $this->render_facet( $facet, $choices ) : '';

				}

				$this->cache_facet( $facet, $content );
				$this->set_selection( $facet, $choices );

				return apply_filters(
					'wp_grid_builder/facet/response',
					[
						'html'     => $content,
						'name'     => $facet['name'],
						'slug'     => $facet['slug'],
						'type'     => $facet['type'],
						'settings' => $facet['settings'],
						'selected' => $facet['selected'],
						'disabled' => $facet['disabled'] ?? false,
					],
					$facet,
					$choices
				);
			},
			wpgb_get_facet_instances( $this->settings['facets'] )
		);
	}

	/**
	 * Search for facet values/names (async method)
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Holds facet value/name.
	 */
	public function search() {

		if (
			empty( $this->settings['search']['facet'] ) ||
			empty( $this->settings['search']['string'] )
		) {
			return [];
		}

		$facet  = $this->settings['search']['facet'];
		$search = $this->settings['search']['string'];
		$facets = wpgb_get_facet_instances( $facet );

		if ( empty( $facets[ $facet ] ) ) {
			return [];
		}

		$choices = ( new Facets\Async() )->query_facet( $facets[ $facet ], $search );

		return apply_filters( 'wp_grid_builder/facet/choices', $choices, $facets[ $facet ] );

	}

	/**
	 * Maybe handle facet rendering conditionally.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array $facet Hold Facet settings.
	 * @return boolean
	 */
	public function maybe_handle( &$facet ) {

		if ( empty( $facet['actions'] ) ) {
			return true;
		}

		$initial_clauses  = $facet['actions'];
		$filtered_clauses = array_filter(
			(array) $initial_clauses,
			function( $clause ) use ( &$facet ) {

				if ( empty( $clause['action'] ) ) {
					return true;
				}

				$conditions = true;

				if ( ! empty( $clause['conditions'] ) ) {
					$conditions = apply_filters( 'wp_grid_builder/do_conditions', true, $clause['conditions'], 'facet' );
				}

				switch ( $clause['action'] ) {
					case 'hide':
						return ! $conditions;
					case 'reveal':
						return $conditions;
					case 'enable':
						$facet['disabled'] = ! $conditions;
						break;
					case 'disable':
						$facet['disabled'] = $conditions;
				}

				// Ignore hide and reveal actions (always render).
				return true;

			}
		);

		// If at least one clause is met, do not render.
		if ( count( $initial_clauses ) !== count( $filtered_clauses ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Query facet choices
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet Hold Facet settings.
	 * @return array
	 */
	public function query_facet( $facet ) {

		if ( ! method_exists( $facet['instance'], 'query_facet' ) ) {
			return [];
		}

		$choices = $facet['instance']->query_facet( $facet );
		$choices = apply_filters( 'wp_grid_builder/facet/choices', $choices, $facet );

		return $choices;

	}

	/**
	 * Render facet content
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet   Hold Facet settings.
	 * @param array $choices Hold Facet choices.
	 * @return string
	 */
	public function render_facet( $facet, $choices ) {

		if ( ! method_exists( $facet['instance'], 'render_facet' ) ) {
			return '';
		}

		$content = $facet['instance']->render_facet( $facet, $choices );
		$content = apply_filters( 'wp_grid_builder/facet/html', $content, $facet['id'] );

		ob_start();
		Helpers::get_template( 'layout/facet', [ 'html' => $content ] + $facet );
		return ob_get_clean();

	}

	/**
	 * Set selection from selected facets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $facet   Hold Facet settings.
	 * @param array $choices Hold Facet choices.
	 */
	public function set_selection( $facet, $choices ) {

		// Only push selection from filter facets.
		if (
			empty( $facet['selected'] ) ||
			empty( $facet['action'] ) ||
			'filter' !== $facet['action']
		) {
			return;
		}

		$this->selections[ $facet['slug'] ] = [ 'choices' => $choices ] + $facet;

	}

	/**
	 * Cache facet output
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array  $facet   Hold Facet settings.
	 * @param string $content Facet content.
	 */
	public function cache_facet( $facet, $content ) {

		// Only cache unfiltered facets.
		if ( wpgb_has_selected_facets() ) {
			return;
		}

		$transient = WPGB_SLUG . '_G' . $this->settings['id'] . 'F' . $facet['id'] . $this->settings['lang'];

		set_transient( apply_filters( 'wp_grid_builder/facet/transient_name', $transient ), $content );

	}
}
