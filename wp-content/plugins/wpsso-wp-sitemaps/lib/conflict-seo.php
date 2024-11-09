<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWpsmConflictSeo' ) ) {

	class WpssoWpsmConflictSeo {

		private $p;	// Wpsso class object.
		private $a;	// WpssoWpsm class object.

		/*
		 * Instantiated by WpssoWpsmConflict->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

		}

		public function conflict_checks() {

			if ( empty( $this->p->avail[ 'seo' ][ 'any' ] ) ) {

				return;
			}

			$this->log_pre    = 'seo plugin conflict detected - ';
			$this->notice_pre =  __( 'Plugin conflict detected:', 'wpsso' ) . ' ';

			$this->conflict_check_rankmath();	// Rank Math.
			$this->conflict_check_wpseo();		// Yoast SEO.
		}

		/*
		 * Rank Math.
		 */
		private function conflict_check_rankmath() {

			if ( empty( $this->p->avail[ 'seo' ][ 'rankmath' ] ) ) {

				return;
			}

			// translators: Please ignore - translation uses a different text domain.
			$plugin_name = __( 'Rank Math', 'rank-math' );

			/*
			 * Check for Sitemap module.
			 */
			if ( \RankMath\Helper::is_module_active( 'sitemap' ) ) {

				// translators: Please ignore - translation uses a different text domain.
				$label_transl  = __( 'Sitemap', 'rank-math' );

				$settings_url  = get_admin_url( $blog_id = null, 'admin.php?page=rank-math' );

				// translators: Please ignore - translation uses a different text domain.
				$settings_link = '<a href="' . $settings_url . '">' . $plugin_name . ' &gt; ' . __( 'Dashboard', 'rank-math' ) . '</a>';

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( $this->log_pre . 'rankmath sitemap module is enabled' );
				}

				$notice_msg = __( 'Please disable the %1$s module in the %2$s settings.', 'wpsso' );
				$notice_msg = sprintf( $notice_msg, $label_transl, $settings_link );
				$notice_key = 'rankmath-sitemap-module-enabled';

				$this->p->notice->err( $this->notice_pre . $notice_msg, null, $notice_key );
			}
		}

		/*
		 * Yoast SEO.
		 */
		private function conflict_check_wpseo() {

			if ( empty( $this->p->avail[ 'seo' ][ 'wpseo' ] ) ) {

				return;
			}

			$plugin_name = __( 'Yoast SEO', 'wpsso' );

			$opts = get_option( 'wpseo' );

			/*
			 * Check for XML sitemaps.
			 */
			if ( ! empty( $opts[ 'enable_xml_sitemap' ] ) ) {

				if ( version_compare( WPSEO_VERSION, 20.0, '>=' ) ) {

					// translators: Please ignore - translation uses a different text domain.
					$label_transl  = '<strong>' . __( 'XML sitemaps', 'wordpress-seo' ) . '</strong>';

					$settings_url  = get_admin_url( $blog_id = null, 'admin.php?page=wpseo_page_settings#/site-features' );

					$settings_link = '<a href="' . $settings_url . '" onclick="window.location.reload();">' . $plugin_name . ' &gt; ' .
						// translators: Please ignore - translation uses a different text domain.
						__( 'Settings', 'wordpress-seo' ) . ' &gt; ' .
						// translators: Please ignore - translation uses a different text domain.
						__( 'Site features', 'wordpress-seo' ) . ' &gt; ' .
						// translators: Please ignore - translation uses a different text domain.
						__( 'APIs', 'wordpress-seo' ) . '</a>';

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( $this->log_pre . 'wpseo xml sitemaps option is enabled' );
					}

					$notice_msg = __( 'Please disable the %1$s option in the %2$s settings.', 'wpsso' );
					$notice_msg = sprintf( $notice_msg, $label_transl, $settings_link );
					$notice_key = 'wpseo-xml-sitemaps-option-enabled';

					$this->p->notice->err( $this->notice_pre . $notice_msg, null, $notice_key );
				}
			}
		}
	}
}
