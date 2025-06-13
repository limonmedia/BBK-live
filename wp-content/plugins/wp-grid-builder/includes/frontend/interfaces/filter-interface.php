<?php
/**
 * Query Vars Interface
 *
 * @package   WP Grid Builder
 * @author    Loïc Blascos
 * @copyright 2019-2024 Loïc Blascos
 */

namespace WP_Grid_Builder\Includes\FrontEnd\Interfaces;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface Filter_Interface {

	/**
	 * Update query variables
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $query Holds WP query object.
	 */
	public function update( $query );
}
