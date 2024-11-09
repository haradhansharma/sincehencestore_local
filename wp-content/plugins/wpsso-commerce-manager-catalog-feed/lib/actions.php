<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoCmcfActions' ) ) {

	class WpssoCmcfActions {

		private $p;	// Wpsso class object.
		private $a;	// WpssoCmcf class object.

		/*
		 * Instantiated by WpssoCmcf->init_objects().
		 */
		public function __construct( &$plugin, &$addon ) {

			static $do_once = null;

			if ( $do_once ) return;	// Stop here.

			$do_once = true;

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_actions( $this, array(
				'check_head_info'  => 3,
				'clear_post_cache' => 2,
			) );

			if ( is_admin() ) {

				$this->p->util->add_plugin_actions( $this, array(
					'load_settings_page_refresh_feed_xml_cache' => 4,
				) );
			}
		}

		/*
		 * The post, term, or user has an ID, is public, and (in the case of a post) the post status is published.
		 */
		public function action_check_head_info( array $head_info, array $mod, $canonical_url ) {

			$is_product = isset( $head_info[ 'og:type' ] ) && 'product' === $head_info[ 'og:type' ] ? true : false;

			if ( $is_product && ! $mod[ 'is_archive' ] ) {	// Exclude the shop page.

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'getting open graph array for ' . $mod[ 'name' ] . ' id ' . $mod[ 'id' ] );
				}

				$ref_msg_transl = __( 'checking facebook catalog feed', 'wpsso-commerce-manager-catalog-feed' );
				$size_names     = 'wpsso-cmcf';
				$md_pre         = array( 'cmcf', 'og' );
				$ref_url        = $this->p->util->maybe_set_ref( $canonical_url, $mod, $ref_msg_transl );
				$mt_og          = $this->p->og->get_array( $mod, $size_names, $md_pre );

				$this->p->util->maybe_unset_ref( $ref_url );

				if ( ! empty( $mt_og[ 'product:variants' ] ) && is_array( $mt_og[ 'product:variants' ] ) ) {

					foreach ( $mt_og[ 'product:variants' ] as $num => $mt_single ) {

						$this->check_product_image_urls( $mt_single );
					}

				} else $this->check_product_image_urls( $mt_og );
			}
		}

		public function action_clear_post_cache( $post_id, $mod ) {

			$og_type = $this->p->og->get_mod_og_type_id( $mod );

			if ( 'product' === $og_type ) {

				$locale = SucomUtilWP::get_locale( $mod );

				foreach ( array(
					'feed' => _x( 'Commerce Manager Catalog Feed XML', 'metabox title', 'wpsso-commerce-manager-catalog-feed' ),
				) as $request_type => $metabox_title ) {

					WpssoCmcfXml::clear_cache( $locale, $request_type );
				}
			}
		}

		public function action_load_settings_page_refresh_feed_xml_cache( $pagehook, $menu_id, $menu_name, $menu_lib ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			set_time_limit( WPSSOCMCF_CACHE_REFRESH_MAX_TIME );	// 10 mins by default.

			$notice_msg = WpssoCmcfXml::cache_refreshed_notice();

			if ( $notice_msg ) $this->p->notice->upd( $notice_msg );
		}

		private function check_product_image_urls( $mt_single ) {

			if ( ! $mod = $this->p->og->get_product_retailer_item_mod( $mt_single ) ) return;	// Just in case.

			$ref_msg_transl = __( 'checking facebook catalog feed images', 'wpsso-commerce-manager-catalog-feed' );
			$size_names     = 'wpsso-cmcf';
			$md_pre         = array( 'cmcf', 'og' );
			$canonical_url  = $this->p->util->get_canonical_url( $mod );
			$ref_url        = $this->p->util->maybe_set_ref( $canonical_url, $mod, $ref_msg_transl );
			$image_urls     = $this->p->og->get_product_retailer_item_image_urls( $mt_single, $size_names, $md_pre );

			if ( empty( $image_urls ) ) {

				if ( $this->p->notice->is_admin_pre_notices() ) {

					if ( ! empty( $mod[ 'post_type_label_single' ] ) ) {	// Just in case.

						$notice_msg = sprintf( __( 'A Facebook catalog feed XML %1$s attribute could not be generated for %2$s ID %3$s.', 'wpsso-commerce-manager-catalog-feed' ), '<code>image_link</code>', $mod[ 'post_type_label_single' ], $mod[ 'id' ] ) . ' ';

						$notice_msg .= sprintf( __( 'Facebook requires at least one %1$s attribute for each product variation in the Facebook catalog feed XML.', 'wpsso-commerce-manager-catalog-feed' ), '<code>image_link</code>' );

						$notice_key = $mod[ 'name' ] . '-' . $mod[ 'id' ] . '-notice-missing-cmcf-image';

						$this->p->notice->err( $notice_msg, null, $notice_key );
					}
				}
			}

			$this->p->util->maybe_unset_ref( $ref_url );
		}
	}
}
