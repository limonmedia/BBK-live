<?php
/**
 * File
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
 * Filesystem helper
 *
 * @class WP_Grid_Builder\Includes\File
 * @since 1.0.0
 */
class File {

	/**
	 * File System instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object
	 */
	private static $filesystem;

	/**
	 * Get WP file system instance
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return WP_Filesystem
	 */
	public static function get_filesystem() {

		global $wp_filesystem;

		if ( ! apply_filters( 'wp_grid_builder/filesystem', true ) ) {
			return false;
		}

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
		}

		if ( ! WP_Filesystem() ) {
			return false;
		}

		self::$filesystem = $wp_filesystem;

		return $wp_filesystem;

	}

	/**
	 * Get WordPress upload directory path
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function get_upload_dir() {

		return trailingslashit( wp_get_upload_dir()['basedir'] );
	}

	/**
	 * Get WordPress upload directory url
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @return string
	 */
	private static function get_upload_url() {

		return trailingslashit( wp_get_upload_dir()['baseurl'] );

	}

	/**
	 * Create directory in wp-content
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $folder Folder name.
	 * @return boolean
	 */
	private static function create_dir( $folder ) {

		$directory = self::get_plugin_dir( $folder );

		if ( empty( $directory ) ) {
			return false;
		}

		if ( self::$filesystem->is_dir( $directory ) ) {
			return true;
		}

		return wp_mkdir_p( $directory );

	}

	/**
	 * Get sub directory in wp-content
	 *
	 * @since 1.2.1 Change default folder name to wpgb (to prevent rare issue on some servers with "wp-" prefix)
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $folder Folder name.
	 * @return string
	 */
	private static function get_plugin_dir( $folder ) {

		if ( ! self::get_filesystem() ) {
			return '';
		}

		$plugin_dir  = self::get_upload_dir();
		$plugin_dir .= WPGB_SLUG;
		$plugin_dir .= self::get_site_dir();

		return trailingslashit( $plugin_dir . '/' . $folder );

	}

	/**
	 * Get WP site/blog directory
	 *
	 * @since 1.2.1
	 * @access private
	 *
	 * @return string
	 */
	private static function get_site_dir() {

		if ( ! is_multisite() ) {
			return '';
		}

		$site = get_site();

		return '/site-' . $site->site_id . '/blog-' . $site->blog_id;

	}

	/**
	 * Get files in directory
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $dir Directory name.
	 * @return array
	 */
	public static function get_files( $dir ) {

		$dir = self::get_plugin_dir( $dir );

		if ( empty( $dir ) ) {
			return [];
		}

		if ( ! self::$filesystem->exists( $dir ) ) {
			return [];
		}

		$files = self::$filesystem->dirlist( $dir );

		if ( false === $files ) {
			return [];
		}

		return $files;

	}

	/**
	 * Generate valid file path
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $folder Folder name.
	 * @param string $name   File name.
	 * @return string
	 */
	public static function generate_path( $folder, $name ) {

		$dir = self::get_plugin_dir( $folder );

		if ( empty( $dir ) ) {
			return '';
		}

		$name = sanitize_file_name( $name );
		$path = wp_normalize_path( $dir . $name );

		return $path;

	}

	/**
	 * Get/check file path
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $folder Folder name.
	 * @param string $name   File name.
	 * @return string
	 */
	public static function get_path( $folder, $name ) {

		$path = self::generate_path( $folder, $name );

		if ( empty( $path ) ) {
			return '';
		}

		if ( ! self::$filesystem->exists( $path ) ) {
			return '';
		}

		return $path;

	}

	/**
	 * Get/check file url
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $folder Folder name.
	 * @param string $name   File name.
	 * @return string
	 */
	public static function get_url( $folder, $name ) {

		$path = self::get_path( $folder, $name );

		if ( empty( $path ) ) {
			return '';
		}

		$dir = wp_normalize_path( self::get_upload_dir() );
		$url = wp_normalize_path( self::get_upload_url() );

		return set_url_scheme( str_replace( $dir, $url, $path ) );

	}

	/**
	 * Get file content
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $folder Folder name.
	 * @param string $name   File name.
	 * @return string|boolean
	 */
	public static function get_contents( $folder, $name ) {

		$path = self::get_path( $folder, $name );

		if ( empty( $path ) ) {
			return false;
		}

		return self::$filesystem->get_contents( $path );

	}

	/**
	 * Put content in file
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $folder  Folder name.
	 * @param string $name    File name.
	 * @param string $content Content to put in file.
	 * @return boolean
	 */
	public static function put_contents( $folder, $name, $content ) {

		self::create_dir( $folder );

		$path = self::generate_path( $folder, $name );

		if ( empty( $path ) ) {
			return false;
		}

		return self::$filesystem->put_contents( $path, $content, 0755 );

	}

	/**
	 * Delete file
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $folder Folder name.
	 * @param string $name   File name.
	 * @return boolean
	 */
	public static function delete( $folder = '', $name = '' ) {

		$path = self::get_path( $folder, $name );

		if ( empty( $path ) ) {
			return false;
		}

		if ( empty( $name ) ) {
			return self::$filesystem->delete( $path, true );
		}

		return self::$filesystem->delete( $path, false, 'f' );

	}
}
