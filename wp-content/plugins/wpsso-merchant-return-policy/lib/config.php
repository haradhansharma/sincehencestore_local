<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoMrpConfig' ) ) {

	class WpssoMrpConfig {

		public static $cf = array(
			'plugin' => array(
				'wpssomrp' => array(			// Plugin acronym.
					'version'     => '2.6.1',	// Plugin version.
					'opt_version' => '8',		// Increment when changing default option values.
					'short'       => 'WPSSO MRP',	// Short plugin name.
					'name'        => 'WPSSO Merchant Return Policy Manager',
					'desc'        => 'Manage any number of Merchant Return Policies for Google Merchant listings.',
					'slug'        => 'wpsso-merchant-return-policy',
					'base'        => 'wpsso-merchant-return-policy/wpsso-merchant-return-policy.php',
					'update_auth' => '',		// No premium version.
					'text_domain' => 'wpsso-merchant-return-policy',
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
							'min_version'   => '18.7.3',
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
						'integ' => array(
							'admin' => array(
								'post' => 'Post Edit Page',
							),
						),
					),
				),
			),

			/*
			 * Additional add-on setting options.
			 */
			'opt' => array(
				'mrp_md_defaults' => array(
					'mrp_name'                                 => '',	// Return Policy Name.
					'mrp_desc'                                 => '',
					'mrp_is_schema_def_product_mrp'            => 0,	// Default Product Return Policy.
					'mrp_category'                             => 'https://schema.org/MerchantReturnUnspecified',	// Return Policy Category.
					'mrp_return_days'                          => 30,	// Return Window Days.
					'mrp_method_https_schema_org_ReturnByMail' => 1,	// Return Methods.
					'mrp_return_fees'                          => 'https://schema.org/ReturnFeesCustomerResponsibility',	// Return Fees.
					'mrp_shipping_amount'                      => 0,	// Shipping Fees (Amount).
					'mrp_shipping_currency'                    => 'USD',	// Shipping Fees (Currency).
					'mrp_country_US'                           => 1,	// Applicable Countries.
				),
			),
			'form' => array(
				'mrp_is_defaults' => array(
					'schema_def_product_mrp' => 'Default Product Return Policy',
				),
			),
		);

		public static function get_version( $add_slug = false ) {

			$info =& self::$cf[ 'plugin' ][ 'wpssomrp' ];

			return $add_slug ? $info[ 'slug' ] . '-' . $info[ 'version' ] : $info[ 'version' ];
		}

		public static function set_constants( $plugin_file ) {

			if ( defined( 'WPSSOMRP_VERSION' ) ) {	// Define constants only once.

				return;
			}

			$info =& self::$cf[ 'plugin' ][ 'wpssomrp' ];

			/*
			 * Define fixed constants.
			 */
			define( 'WPSSOMRP_FILEPATH', $plugin_file );
			define( 'WPSSOMRP_PLUGINBASE', $info[ 'base' ] );	// Example: wpsso-merchant-return-policy/wpsso-merchant-return-policy.php
			define( 'WPSSOMRP_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_file ) ) ) );
			define( 'WPSSOMRP_PLUGINSLUG', $info[ 'slug' ] );	// Example: wpsso-merchant-return-policy
			define( 'WPSSOMRP_URLPATH', trailingslashit( plugins_url( '', $plugin_file ) ) );
			define( 'WPSSOMRP_VERSION', $info[ 'version' ] );

			define( 'WPSSOMRP_MRP_POST_TYPE', 'return_policy' );

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

			/*
			 * MENU_ORDER (aka menu_position):
			 *
			 *	null – below Comments
			 *	5 – below Posts
			 *	10 – below Media
			 *	15 – below Links
			 *	20 – below Pages
			 *	25 – below comments
			 *	60 – below first separator
			 *	65 – below Plugins
			 *	70 – below Users
			 *	75 – below Tools
			 *	80 – below Settings
			 *	100 – below second separator
			 */
			$var_const[ 'WPSSOMRP_MRP_MENU_ORDER' ]        = 83;
			$var_const[ 'WPSSOMRP_MRP_CATEGORY_TAXONOMY' ] = false;
			$var_const[ 'WPSSOMRP_MRP_COUNTRIES_MAX' ]     = 50;

			/*
			 * Maybe override the default constant value with a pre-defined constant value.
			 */
			foreach ( $var_const as $name => $value ) {

				if ( defined( $name ) ) {

					$var_const[$name] = constant( $name );
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

			require_once WPSSOMRP_PLUGINDIR . 'lib/filters.php';
			require_once WPSSOMRP_PLUGINDIR . 'lib/mrp.php';
			require_once WPSSOMRP_PLUGINDIR . 'lib/register.php';

			add_filter( 'wpssomrp_load_lib', array( __CLASS__, 'load_lib' ), 10, 3 );
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

				$file_path = WPSSOMRP_PLUGINDIR . 'lib/' . $filespec . '.php';

				if ( file_exists( $file_path ) ) {

					require_once $file_path;

					if ( empty( $classname ) ) {

						return SucomUtil::sanitize_classname( 'wpssomrp' . $filespec, $allow_underscore = false );
					}

					return $classname;
				}
			}

			return $success;
		}
	}
}
