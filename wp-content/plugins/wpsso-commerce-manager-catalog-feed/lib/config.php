<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoCmcfConfig' ) ) {

	class WpssoCmcfConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssocmcf' => array(			// Plugin acronym.
					'version'     => '4.9.0',	// Plugin version.
					'opt_version' => '5',		// Increment when changing default option values.
					'short'       => 'WPSSO CMCF',	// Short plugin name.
					'name'        => 'WPSSO Commerce Manager Catalog Feed XML',
					'desc'        => 'Facebook and Instagram Commerce Manager Catalog Feed XMLs for WooCommerce and custom product pages.',
					'slug'        => 'wpsso-commerce-manager-catalog-feed',
					'base'        => 'wpsso-commerce-manager-catalog-feed/wpsso-commerce-manager-catalog-feed.php',
					'update_auth' => '',		// No premium version.
					'text_domain' => 'wpsso-commerce-manager-catalog-feed',
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
							'min_version'   => '18.17.0',
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
							'facebook-catalog' => 'Meta Catalog',
						),
					),

					/*
					 * Callbacks for Vitalybaev\GoogleMerchant classes.
					 */
					'callbacks' => array(
						'product' => array(
							'og:title'                      => 'setTitle',
							'og:description'                => 'setDescription',
							'og:updated_time'               => 'setUpdated',
							'og:url'                        => 'setCanonicalLink',
							'product:retailer_item_id'      => 'setId',
							'product:title'                 => 'setTitle',
							'product:description'           => 'setDescription',
							'product:updated_time'          => 'setUpdated',
							'product:availability'          => 'setAvailability',
							'product:condition'             => 'setCondition',
							'product:price'                 => 'setPrice',
							'product:url'                   => 'setLink',
							'product:category'              => 'setGoogleCategory',
							'product:size'                  => 'setSize',
							'product:brand'                 => 'addBrand',	// One or more.
							'product:mfr_part_no'           => 'addBrand',	// One or more.
							'product:isbn'                  => 'addBrand',	// One or more.
							'product:upc'                   => 'addBrand',	// One or more.
							'product:ean'                   => 'addBrand',	// One or more.
							'product:gtin14'                => 'addBrand',	// One or more.
							'product:gtin13'                => 'addBrand',	// One or more.
							'product:gtin12'                => 'addBrand',	// One or more.
							'product:gtin8'                 => 'addBrand',	// One or more.
							'product:gtin'                  => 'addBrand',	// One or more.
							'product:sale_price'            => 'setSalePrice',
							'product:sale_price_dates'      => 'setSalePriceEffectiveDate',
							'product:item_group_id'         => 'setItemGroupId',
							'product:color'                 => 'setColor',
							'product:target_gender'         => 'setGender',
							'product:age_group'             => 'setAgeGroup',
							'product:material'              => 'setMaterial',
							'product:pattern'               => 'setPattern',
							'product:shipping_weight:value' => 'setShippingWeight',
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
					'cmcf_img_width'     => 1200,
					'cmcf_img_height'    => 628,
					'cmcf_img_crop'      => 1,
					'cmcf_img_crop_x'    => 'center',
					'cmcf_img_crop_y'    => 'center',
					'cmcf_feed_exp_secs' => WEEK_IN_SECONDS,
					'cmcf_feed_format'   => 'atom',
				),
			),	// End of 'opt' array.
			'form' => array(
				'feed_formats' => array(
					'atom' => 'Atom 1.0',
					'rss'  => 'RSS 2.0',
				),
			),	// End of 'form' array.
			'head' => array(

				/*
				 * For carousel ads, collection ads and Shops: Product images display in square (1:1) format. The
				 * minimum image size is 500 x 500 px. We recommend 1024 x 1024 px for best quality.
				 *
				 * For single image ads: Product images display at a 1.91:1 aspect ratio. The minimum image size is
				 * 500 x 500 px. We recommend 1200 x 628 px for best quality.
				 *
				 * See https://www.facebook.com/business/help/686259348512056?id=725943027795860.
				 */
				'limit_min' => array(
					'cmcf_img_width'  => 500,
					'cmcf_img_height' => 500,
				),

				/*
				 * See https://www.facebook.com/business/help/120325381656392?id=725943027795860.
				 */
				'cmcf_content_map' => array(
				),
			),	// End of 'head' array.
			'wp' => array(
				'cache' => array(
					'file' => array(
						'wpssocmcf_feed_' => array(
							'label'   => 'Commerce Manager Catalog Feed XML',
							'opt_key' => 'cmcf_feed_exp_secs',
							'filter'  => 'wpsso_cache_expire_cmcf_feed_xml',	// See WpssoUtil->get_cache_exp_secs().
						),
					),
				),
			),
		);

		public static function get_version( $add_slug = false ) {

			$info =& self::$cf[ 'plugin' ][ 'wpssocmcf' ];

			return $add_slug ? $info[ 'slug' ] . '-' . $info[ 'version' ] : $info[ 'version' ];
		}

		public static function get_callbacks( $item_type ) {

			return self::$cf[ 'plugin' ][ 'wpssocmcf' ][ 'callbacks' ][ $item_type ];
		}

		public static function set_constants( $plugin_file ) {

			if ( defined( 'WPSSOCMCF_VERSION' ) ) {	// Define constants only once.

				return;
			}

			$info =& self::$cf[ 'plugin' ][ 'wpssocmcf' ];

			/*
			 * Define fixed constants.
			 */
			define( 'WPSSOCMCF_FILEPATH', $plugin_file );
			define( 'WPSSOCMCF_PLUGINBASE', $info[ 'base' ] );	// Example: wpsso-commerce-manager-catalog-feed/wpsso-commerce-manager-catalog-feed.php.
			define( 'WPSSOCMCF_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_file ) ) ) );
			define( 'WPSSOCMCF_PLUGINSLUG', $info[ 'slug' ] );	// Example: wpsso-commerce-manager-catalog-feed.
			define( 'WPSSOCMCF_URLPATH', trailingslashit( plugins_url( '', $plugin_file ) ) );
			define( 'WPSSOCMCF_VERSION', $info[ 'version' ] );

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

			$var_const[ 'WPSSOCMCF_PAGENAME' ]               = 'commerce-manager-catalog';
			$var_const[ 'WPSSOCMCF_CACHE_REFRESH_MAX_TIME' ] = 600;		// 10 mins by default.

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

			require_once WPSSOCMCF_PLUGINDIR . 'vendor/autoload.php';
			require_once WPSSOCMCF_PLUGINDIR . 'lib/actions.php';
			require_once WPSSOCMCF_PLUGINDIR . 'lib/filters.php';
			require_once WPSSOCMCF_PLUGINDIR . 'lib/register.php';
			require_once WPSSOCMCF_PLUGINDIR . 'lib/rewrite.php';	// Static methods required by WpssoCmcfRegister->activate_plugin().
			require_once WPSSOCMCF_PLUGINDIR . 'lib/xml.php';

			add_filter( 'wpssocmcf_load_lib', array( __CLASS__, 'load_lib' ), 10, 3 );
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

				$file_path = WPSSOCMCF_PLUGINDIR . 'lib/' . $filespec . '.php';

				if ( file_exists( $file_path ) ) {

					require_once $file_path;

					if ( empty( $classname ) ) {

						return SucomUtil::sanitize_classname( 'wpssocmcf' . $filespec, $allow_underscore = false );
					}

					return $classname;
				}
			}

			return $success;
		}
	}
}
