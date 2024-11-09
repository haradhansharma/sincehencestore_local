<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoMrpMrp' ) ) {

	class WpssoMrpMrp {

		public function __construct() {}

		public static function get_ids() {

			$mrp_names = self::get_names();

			return array_keys( $mrp_names );
		}

		/*
		 * Return an associative array of return policy IDs and names.
		 */
		public static function get_names() {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->mark();
			}

			static $local_cache = null;

			if ( null !== $local_cache ) {

				return $local_cache;
			}

			$local_cache = array();

			$mrp_ids = WpssoPost::get_public_ids( array( 'post_type' => WPSSOMRP_MRP_POST_TYPE ) );

			foreach ( $mrp_ids as $post_id ) {

				$mrp_opts = $wpsso->post->get_options( $post_id );
				$def_name = sprintf( _x( 'Return Policy #%d', 'option value', 'wpsso-merchant-return-policy' ), $post_id );
				$mrp_name = empty( $mrp_opts[ 'mrp_name' ] ) ? $def_name : $mrp_opts[ 'mrp_name' ];

				$local_cache[ 'mrp-' . $post_id ] = $mrp_name;
			}

			return $local_cache;
		}

		/*
		 * Get a specific return policy id.
		 */
		public static function get_id( $mrp_id, $mixed = 'current', $opt_key = false ) {

			$wpsso =& Wpsso::get_instance();

			if ( $wpsso->debug->enabled ) {

				$wpsso->debug->log_args( array(
					'mrp_id' => $mrp_id,
					'mixed'  => $mixed,
				) );
			}

			$mrp_opts = false;	// Return false by default.

			if ( 0 === strpos( $mrp_id, 'mrp-' ) ) {

				$post_id  = substr( $mrp_id, 4 );
				$post_mod = $wpsso->post->get_mod( $post_id );

				if ( 'publish' === $post_mod[ 'post_status' ] ) {

					$mrp_opts = $post_mod[ 'obj' ]->get_options( $post_mod[ 'id' ] );

				} elseif ( ! empty( $post_mod[ 'post_status' ] ) ) {	// 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', or 'trash'.

					$mrp_page_link = get_edit_post_link( $post_id );

					$notice_msg = sprintf( __( 'Unable to provide information for return policy ID #%s.',
						'wpsso-merchant-return-policy' ), $post_id ) . ' ';
					$notice_msg .= $mrp_page_link ? '<a href="' . $mrp_page_link . '">' : '';
					$notice_msg .= sprintf( __( 'Please publish return policy ID #%s or select a different return policy.',
						'wpsso-merchant-return-policy' ), $post_id );
					$notice_msg .= $mrp_page_link ? '</a>' : '';

					$wpsso->notice->err( $notice_msg );

				} else {

					$notice_msg = sprintf( __( 'Unable to provide information for return policy ID #%s.',
						'wpsso-merchant-return-policy' ), $post_id ) . ' ';
					$notice_msg .= sprintf( __( 'Return policy ID #%s does not exist.',
						'wpsso-merchant-return-policy' ), $post_id ) . ' ';
					$notice_msg .= __( 'Please select a different return policy.',
						'wpsso-merchant-return-policy' );

					$wpsso->notice->err( $notice_msg );
				}
			}

			if ( ! empty( $mrp_opts ) ) {

				$mrp_opts[ 'mrp_id' ] = $mrp_id;

				$mrp_opts = array_merge( WpssoMrpConfig::$cf[ 'opt' ][ 'mrp_md_defaults' ], $mrp_opts );	// Complete the array.

				$mrp_opts = SucomUtil::preg_grep_keys( '/^mrp_/', $mrp_opts );
			}

			if ( false !== $opt_key ) {

				if ( isset( $mrp_opts[ $opt_key ] ) ) {

					return $mrp_opts[ $opt_key ];
				}

				return null;
			}

			return $mrp_opts;
		}
	}
}
