<?php
/**
 * Add Relevanssi support
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2024 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\Integrations\Relevanssi;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle Relevanssi search feature
 *
 * @class WP_Grid_Builder\Includes\Third_Party\Relevanssi
 * @since 1.0.0
 */
class Relevanssi {

	/**
	 * Holds searched keywords
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $keywords = '';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {

		if ( ! function_exists( 'relevanssi_search' ) ) {
			return;
		}

		// Prevent Relevanssi conflicts with nested queries.
		add_filter( 'relevanssi_pre_excerpt_content', 'wpgb_strip_blocks' );
		add_filter( 'relevanssi_post_content', 'wpgb_strip_blocks' );

		add_filter( 'wp_grid_builder/facet/search_query_args', [ $this, 'search_terms' ] );
		add_filter( 'wp_grid_builder/facet/query_objects', [ $this, 'query_objects' ], 10, 2 );
		add_filter( 'wp_grid_builder/async/get_endpoint', [ $this, 'add_search_query' ] );
		add_filter( 'get_search_query', [ $this, 'restore_search_query' ] );
		add_action( 'pre_get_posts', [ $this, 'short_circuit' ], PHP_INT_MAX - 9 );
		add_filter( 'posts_results', [ $this, 'highlight' ], 10, 2 );

	}

	/**
	 * Prevent running Revelanssi if enabled
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param  array $query_args Holds WP query args.
	 * @return array
	 */
	public function search_terms( $query_args ) {

		$query_args['search_columns']   = [];
		$query_args['suppress_filters'] = true;

		return $query_args;

	}

	/**
	 * Query object ids
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param mixed $match Match state.
	 * @param array $facet Holds facet settings.
	 * @return array Holds queried facet object ids.
	 */
	public function query_objects( $match, $facet ) {

		if (
			empty( $facet['type'] ) ||
			'search' !== $facet['type'] ||
			empty( $facet['search_engine'] ) ||
			'relevanssi' !== $facet['search_engine']
		) {
			return $match;
		}

		$object = wpgb_get_queried_object_type();
		$number = (int) $facet['search_number'];

		if ( 'term' === $object ) {
			return $match;
		}

		$this->keywords = wp_unslash( implode( ' ', (array) $facet['selected'] ) );

		return $this->query_object_ids( $this->keywords, $object, $number );

	}

	/**
	 * Query object ids
	 *
	 * @since 1.5.8
	 * @access public
	 *
	 * @param string  $keywords     Searched keywords.
	 * @param string  $object_type  Object type to search.
	 * @param integer $number       Number to search for.
	 * @return array Queried object ids.
	 */
	public function query_object_ids( $keywords = '', $object_type = '', $number = 200 ) {

		$query_vars = [
			's'              => $keywords,
			'posts_per_page' => $number,
			'fields'         => 'ids',
		];

		if ( 'user' === $object_type ) {
			$query_vars['post_types'] = 'user';
		}

		$query = new \WP_Query();
		$query->parse_query( $query_vars );
		$query = apply_filters( 'relevanssi_modify_wp_query', $query );

		relevanssi_do_query( $query );

		if ( 'user' === $object_type ) {
			return preg_filter( '/u_/', '', (array) $query->posts );
		}

		return (array) $query->posts;

	}

	/**
	 * Add search query parameter to endpoint if missing
	 *
	 * @access public
	 * @since 1.5.7
	 *
	 * @param string $endpoint Async endpoint url.
	 * @return string
	 */
	public function add_search_query( $endpoint ) {

		$query = get_search_query();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['s'] ) && ! empty( $query ) ) {
			$endpoint = add_query_arg( 's', $query, $endpoint );
		}

		return $endpoint;

	}

	/**
	 * Override main search query directly
	 *
	 * @since 1.5.7
	 * @access public
	 *
	 * @param \WP_Query $query Holds query instance.
	 */
	public function short_circuit( $query ) {

		if ( ! $query instanceof \WP_Query ) {
			return $short_circuit;
		}

		if ( ! wpgb_doing_ajax() || empty( $query->get( 'wp_grid_builder' ) ) ) {
			return;
		}

		$keywords = trim( $query->get( 's' ) );

		if ( empty( $keywords ) ) {
			return;
		}

		$this->keywords = $keywords;

		$post__in = array_diff(
			$this->query_object_ids( $this->keywords, 'post', -1 ),
			$query->get( 'post__not_in' ) ?: []
		);

		$query->set( 's', '' );
		$query->set( '_s', $keywords );
		$query->set( 'post__not_in', [] );
		$query->set( 'post__in', $post__in );

		if ( empty( $query->get( 'orderby' ) ) ) {
			$query->set( 'orderby', 'post__in' );
		}

		remove_filter( 'the_posts', 'relevanssi_query', 99 );
		remove_filter( 'posts_pre_query', 'relevanssi_query', 99 );
		remove_filter( 'posts_request', 'relevanssi_prevent_default_request', 10 );

	}

	/**
	 * Restore search query
	 *
	 * @since 1.5.7
	 * @access public
	 *
	 * @param mixed $search Contents of the search query variable.
	 * @return mixed
	 */
	public function restore_search_query( $search ) {

		if ( empty( trim( $search ) ) ) {
			$search = get_query_var( '_s' );
		}

		return $search;

	}

	/**
	 * Highlight keywords in filtered content
	 *
	 * @since 1.5.7
	 * @access public
	 *
	 * @param array     $posts Holds posts.
	 * @param \WP_Query $query Holds query instance.
	 * @return array
	 */
	public function highlight( $posts, $query ) {

		if ( empty( $this->keywords ) ) {
			return $posts;
		}

		$keywords       = trim( $this->keywords );
		$make_titles    = get_option( 'relevanssi_hilite_title' );
		$make_excerpts  = get_option( 'relevanssi_excerpts' );
		$excerpt_type   = get_option( 'relevanssi_excerpt_type' );
		$excerpt_length = get_option( 'relevanssi_excerpt_length' );
		$show_matches   = get_option( 'relevanssi_show_matches' );
		$search_params  = relevanssi_compile_search_args( $query, $keywords );
		$return_posts   = empty( $search_params['fields'] );

		foreach ( $posts as $post ) {

			if ( ! $return_posts ) {
				continue;
			}

			if ( 'on' === $make_titles ) {

				$post_title = relevanssi_highlight_terms( $post->post_title, $keywords );

				if ( ! empty( $post_title ) && ! empty( $post->post_title ) ) {
					$post->post_title = $post_title;
				}
			}

			if ( 'on' === $make_excerpts ) {

				$post_excerpt = relevanssi_do_excerpt( $post, $keywords, $excerpt_length, $excerpt_type );
				$post_excerpt = preg_match( '/<span class="excerpt_part">(.*?)<\/span>/s', $post_excerpt, $match );

				if ( ! empty( $match[1] ) ) {
					$post->post_excerpt = $match[1];
				}
			}

			if ( 'on' === $show_matches ) {

				relevanssi_add_matches( $post, true );
				$post->post_excerpt .= relevanssi_show_matches( $post );

			}
		}

		if ( ! wpgb_doing_ajax() ) {
			$this->keywords = '';
		}

		return $posts;

	}
}
