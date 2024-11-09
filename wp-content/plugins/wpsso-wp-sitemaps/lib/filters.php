<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWpsmFilters' ) ) {

	class WpssoWpsmFilters {

		private $p;	// Wpsso class object.
		private $a;	// WpssoWpsm class object.
		private $msgs;	// WpssoWpsmFiltersMessages class object.
		private $opts;	// WpssoWpsmFiltersOptions class object.
		private $upg;	// WpssoWpsmFiltersUpgrade class object.

		/*
		 * Instantiated by WpssoWpsm->init_objects().
		 */
		public function __construct( &$plugin, &$addon ) {

			static $do_once = null;

			if ( $do_once ) return;	// Stop here.

			$do_once = true;

			$this->p =& $plugin;
			$this->a =& $addon;

			require_once WPSSOWPSM_PLUGINDIR . 'lib/filters-options.php';

			$this->opts = new WpssoWpsmFiltersOptions( $plugin, $addon );

			if ( is_admin() ) {

				require_once WPSSOWPSM_PLUGINDIR . 'lib/filters-messages.php';

				$this->msgs = new WpssoWpsmFiltersMessages( $plugin, $addon );
			}

			if ( $this->p->notice->is_admin_pre_notices() ) {

				if ( SucomUtilWP::sitemaps_disabled() ) {

					if ( $notice_msg = $this->p->msgs->wp_sitemaps_disabled( $is_notice = true ) ) {

						$notice_key = 'wp-sitemaps-disabled';

						$this->p->notice->err( $notice_msg, null, $notice_key );
					}
				}
			}
		}
	}
}
