<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoMrpFiltersOptions' ) ) {

	class WpssoMrpFiltersOptions {

		private $p;	// Wpsso class object.
		private $a;	// WpssoMrp class object.

		/*
		 * Instantiated by WpssoMrpFilters->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'get_merchant_return_policy_options' => 3,
				'get_post_defaults'                  => 3,
				'get_post_options'                   => 3,
				'save_post_options'                  => 3,
				'option_type'                        => 2,
				'plugin_upgrade_advanced_exclude'    => 1,
			) );
		}

		public function filter_get_merchant_return_policy_options( $mrp_opts, $mod, $mrp_id ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( false === $mrp_opts ) {	// First come, first served.

				if ( 0 === strpos( $mrp_id, 'mrp-' ) ) {

					$mrp_opts = WpssoMrpMrp::get_id( $mrp_id, $mod );
				}
			}

			return $mrp_opts;
		}

		public function filter_get_post_defaults( array $md_defs, $post_id, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$mrp_id = 'mrp-' . $post_id;

			$md_defs = array_merge( $md_defs, $this->p->cf[ 'opt' ][ 'mrp_md_defaults' ] );

			$md_defs[ 'mrp_shipping_currency' ] = $this->p->get_options( 'og_def_currency', 'USD' );

			/*
			 * Check if this return policy ID is in some default options.
			 */
			foreach ( $this->p->cf[ 'form' ][ 'mrp_is_defaults' ] as $opts_key => $opts_label ) {

				if ( isset( $this->p->options[ $opts_key ] ) && $mrp_id === $this->p->options[ $opts_key ] ) {

					$md_defs[ 'mrp_is_' . $opts_key ] = 1;

				} else $md_defs[ 'mrp_is_' . $opts_key ] = 0;
			}

			return $md_defs;
		}

		public function filter_get_post_options( array $md_opts, $post_id, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$md_defs = $this->filter_get_post_defaults( array(), $post_id, $mod );

			$md_opts = array_merge( $md_defs, $md_opts );

			return $md_opts;
		}

		public function filter_save_post_options( array $md_opts, $post_id, array $mod ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( WPSSOMRP_MRP_POST_TYPE === $mod[ 'post_type' ] ) {

				$mrp_id = 'mrp-' . $post_id;

				$md_defs = $this->filter_get_post_defaults( array(), $post_id, $mod );

				$md_opts = array_merge( $md_defs, $md_opts );

				if ( empty( $md_opts[ 'mrp_name' ] ) ) {	// Just in case.

					$md_opts[ 'mrp_name' ] = sprintf( _x( 'Return Policy #%d', 'option value', 'wpsso-merchant-return-policy' ), $post_id );
				}

				/*
				 * Always keep the post title, slug, and content updated.
				 */
				SucomUtilWP::raw_update_post_title_content( $post_id, $md_opts[ 'mrp_name' ], $md_opts[ 'mrp_desc' ] );

				/*
				 * Check if some default options need to be updated.
				 */
				foreach ( $this->p->cf[ 'form' ][ 'mrp_is_defaults' ] as $opts_key => $opts_label ) {

					if ( empty( $md_opts[ 'mrp_is_' . $opts_key ] ) ) {	// Checkbox is unchecked.

						if ( $mrp_id === $this->p->options[ $opts_key ] ) {	// Maybe remove the existing return policy ID.

							$this->p->options[ $opts_key ] = 'none';

							SucomUtilWP::update_options_key( WPSSO_OPTIONS_NAME, $opts_key, 'none' );	// Save changes.
						}

					} elseif ( $mrp_id !== $this->p->options[ $opts_key ] ) {	// Maybe change the existing return policy ID.

						$this->p->options[ $opts_key ] = $mrp_id;

						SucomUtilWP::update_options_key( WPSSO_OPTIONS_NAME, $opts_key, $mrp_id );	// Save changes.
					}

					unset( $md_opts[ 'mrp_is_' . $opts_key ] );
				}

				if ( empty( $md_opts[ 'mrp_method_https_schema_org_ReturnByMail' ] ) ) {

					$md_defs = $this->filter_get_post_defaults( array(), $post_id, $mod );

					$md_opts[ 'mrp_return_fees' ]       = $md_defs[ 'mrp_return_fees' ];
					$md_opts[ 'mrp_shipping_amount' ]   = $md_defs[ 'mrp_shipping_amount' ];
					$md_opts[ 'mrp_shipping_currency' ] = $md_defs[ 'mrp_shipping_currency' ];
				}

				if ( 'https://schema.org/ReturnShippingFees' === $md_opts[ 'mrp_return_fees' ] ) {

					if ( empty( $md_opts[ 'mrp_shipping_amount' ] ) ) {

						$md_opts[ 'mrp_return_fees' ] = 'https://schema.org/FreeReturn';
					}
				}
			}

			return $md_opts;
		}

		/*
		 * Return the sanitation type for a given option key.
		 */
		public function filter_option_type( $type, $base_key ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( ! empty( $type ) ) {	// Return early if we already have a type.

				return $type;

			} elseif ( 0 !== strpos( $base_key, 'mrp_' ) ) {	// Nothing to do.

				return $type;
			}

			switch ( $base_key ) {

				case 'mrp_name':

					return 'ok_blank';

				case 'mrp_category':
				case 'mrp_return_fees':
				case 'mrp_shipping_currency':

					return 'not_blank';

				case 'mrp_return_days':

					return 'zero_pos_int';

				case 'mrp_shipping_amount':

					return 'numeric';

				case ( 0 === strpos( $base_key, 'mrp_is_' ) ? true : false ):
				case ( 0 === strpos( $base_key, 'mrp_country_' ) ? true : false ):
				case ( 0 === strpos( $base_key, 'mrp_method_' ) ? true : false ):

					return 'checkbox';
			}

			return $type;
		}

		public function filter_plugin_upgrade_advanced_exclude( $adv_exclude ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			foreach ( $this->p->cf[ 'form' ][ 'mrp_is_defaults' ] as $opts_key => $opts_label ) {

				$adv_exclude[] = $opts_key;
			}

			return $adv_exclude;
		}
	}
}
