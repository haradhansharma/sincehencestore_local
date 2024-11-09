<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWpsmConfig' ) ) {

	class WpssoWpsmConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssowpsm' => array(			// Plugin acronym.
					'version'     => '8.4.0',	// Plugin version.
					'opt_version' => '9',		// Increment when changing default option values.
					'short'       => 'WPSSO WPSM',	// Short plugin name.
					'name'        => 'WPSSO WP Sitemaps XML',
					'desc'        => 'Improves the WordPress sitemaps XML with article modification times, alternate language URLs, images sitemaps, news sitemaps and more.',
					'slug'        => 'wpsso-wp-sitemaps',
					'base'        => 'wpsso-wp-sitemaps/wpsso-wp-sitemaps.php',
					'update_auth' => '',		// No premium version.
					'text_domain' => 'wpsso-wp-sitemaps',
					'domain_path' => '/languages',

					/*
					 * Required plugin and its version.
					 */
					'req' => array(
						'wpsso' => array(
							'name'          => 'WPSSO Core',
							'home'          => 'https://wordpress.org/plugins/wpsso/',
							'plugin_class'  => 'Wpsso',
							'version_const' => 'WPSSO_VERSION',
							'min_version'   => '18.5.0',
						),
					),

					/*
					 * URLs or relative paths to plugin banners and icons.
					 */
					'assets' => array(

						/*
						 * Icon image array keys are '1x' and '2x'.
						 */
						'icons' => array(
							'1x' => 'images/icon-128x128.png',
							'2x' => 'images/icon-256x256.png',
						),
					),

					/*
					 * Library files loaded and instantiated by WPSSO.
					 */
					'lib' => array(
						'submenu' => array(
							'wp-sitemaps' => 'WP Sitemaps',
						),
					),

					/*
					 * Declare compatibility with WooCommerce HPOS.
					 *
					 * See https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book.
					 */
					'wc_compat' => array(
						'custom_order_tables',
					),
				),
			),
			'opt' => array(
				'defaults' => array(

					/*
					 * Advanced Settings > WordPress Sitemaps metabox.
					 */
					'wpsm_sitemaps_for_article'                => 1,
					'wpsm_sitemaps_for_attachment'             => 0,
					'wpsm_sitemaps_for_download'               => 1,	// For Easy Digital Downloads.
					'wpsm_sitemaps_for_organization'           => 1,
					'wpsm_sitemaps_for_page'                   => 1,
					'wpsm_sitemaps_for_place'                  => 1,
					'wpsm_sitemaps_for_post'                   => 1,
					'wpsm_sitemaps_for_product'                => 1,	// For WooCommerce, etc.
					'wpsm_sitemaps_for_question'               => 1,
					'wpsm_sitemaps_for_reply'                  => 0,	// For Bbpress
					'wpsm_sitemaps_for_tax_category'           => 1,
					'wpsm_sitemaps_for_tax_faq_category'       => 1,
					'wpsm_sitemaps_for_tax_link_category'      => 1,
					'wpsm_sitemaps_for_tax_post_tag'           => 1,
					'wpsm_sitemaps_for_tax_product_brand'      => 1,	// For WooCommerce Brands.
					'wpsm_sitemaps_for_tax_product_cat'        => 1,	// For WooCommerce.
					'wpsm_sitemaps_for_tax_product_tag'        => 1,	// For WooCommerce.
					'wpsm_sitemaps_for_tax_pwb-brand'          => 1,	// For Perfect Brands for WooCommerce.
					'wpsm_sitemaps_for_tax_tribe_events_cat'   => 0,	// For The Events Calendar.
					'wpsm_sitemaps_for_tax_yith_product_brand' => 1,	// For YITH WooCommerce Brands Add-on.
					'wpsm_sitemaps_for_topic'                  => 0,	// For Bbpress
					'wpsm_sitemaps_for_tribe_events'           => 1,	// For The Events Calendar.
					'wpsm_sitemaps_for_tribe-ea-record'        => 1,	// For The Events Calendar.
					'wpsm_sitemaps_for_user_page'              => 1,
					'wpsm_news_post_type'                      => 'none',	// Post Type for News Sitemaps.
					'wpsm_site_name'                           => '',	// News Publication Name.
					'wpsm_schema_images'                       => 0,	// Include Images Sitemaps.
					'wpsm_schema_videos'                       => 1,	// Include Videos Sitemaps.
					'wpsm_max_urls'                            => 2000,	// Maximum URLs per Sitemap.
				),
			),
		);

		public static function get_version( $add_slug = false ) {

			$info =& self::$cf[ 'plugin' ][ 'wpssowpsm' ];

			return $add_slug ? $info[ 'slug' ] . '-' . $info[ 'version' ] : $info[ 'version' ];
		}

		public static function set_constants( $plugin_file ) {

			if ( defined( 'WPSSOWPSM_VERSION' ) ) {	// Define constants only once.

				return;
			}

			$info =& self::$cf[ 'plugin' ][ 'wpssowpsm' ];

			/*
			 * Define fixed constants.
			 */
			define( 'WPSSOWPSM_FILEPATH', $plugin_file );
			define( 'WPSSOWPSM_PLUGINBASE', $info[ 'base' ] );	// Example: wpsso-wp-sitemaps/wpsso-wp-sitemaps.php.
			define( 'WPSSOWPSM_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_file ) ) ) );
			define( 'WPSSOWPSM_PLUGINSLUG', $info[ 'slug' ] );	// Example: wpsso-wp-sitemaps.
			define( 'WPSSOWPSM_URLPATH', trailingslashit( plugins_url( '', $plugin_file ) ) );
			define( 'WPSSOWPSM_VERSION', $info[ 'version' ] );

			/*
			 * Define variable constants.
			 */
			self::set_variable_constants();
		}

		public static function set_variable_constants( $var_const = null ) {

			if ( ! is_array( $var_const ) ) {

				$var_const = self::get_variable_constants();
			}

			/*
			 * Define the variable constants, if not already defined.
			 */
			foreach ( $var_const as $name => $value ) {

				if ( ! defined( $name ) ) {

					define( $name, $value );
				}
			}
		}

		public static function get_variable_constants() {

			$var_const = array();

			$var_const[ 'WPSSOWPSM_NEWS_PUB_MAX_TIME' ] = 2 * DAY_IN_SECONDS;

			/*
			 * Maybe override the default constant value with a pre-defined constant value.
			 */
			foreach ( $var_const as $name => $value ) {

				if ( defined( $name ) ) {

					$var_const[ $name ] = constant( $name );
				}
			}

			return $var_const;
		}

		/*
		 * Require library files with functions or static methods in require_libs().
		 *
		 * Require and instantiate library files with dynamic methods in init_objects().
		 */
		public static function require_libs( $plugin_file ) {

			require_once WPSSOWPSM_PLUGINDIR . 'lib/conflict.php';
			require_once WPSSOWPSM_PLUGINDIR . 'lib/filters.php';
			require_once WPSSOWPSM_PLUGINDIR . 'lib/register.php';
			require_once WPSSOWPSM_PLUGINDIR . 'lib/functions.php';
			require_once WPSSOWPSM_PLUGINDIR . 'lib/sitemaps/filters.php';

			add_filter( 'wpssowpsm_load_lib', array( __CLASS__, 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $success = false, $filespec = '', $classname = '' ) {

			if ( false !== $success ) {

				return $success;
			}

			if ( ! empty( $classname ) ) {

				if ( class_exists( $classname ) ) {

					return $classname;
				}
			}

			if ( ! empty( $filespec ) ) {

				$file_path = WPSSOWPSM_PLUGINDIR . 'lib/' . $filespec . '.php';

				if ( file_exists( $file_path ) ) {

					require_once $file_path;

					if ( empty( $classname ) ) {

						return SucomUtil::sanitize_classname( 'wpssowpsm' . $filespec, $allow_underscore = false );
					}

					return $classname;
				}
			}

			return $success;
		}
	}
}
