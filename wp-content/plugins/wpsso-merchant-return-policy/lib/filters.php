<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoMrpFilters' ) ) {

	class WpssoMrpFilters {

		private $p;	// Wpsso class object.
		private $a;	// WpssoMrp class object.

		/*
		 * Instantiated by WpssoMrp->init_objects().
		 */
		public function __construct( &$plugin, &$addon ) {

			static $do_once = null;

			if ( $do_once ) return;	// Stop here.

			$do_once = true;

			$this->p =& $plugin;
			$this->a =& $addon;

			require_once WPSSOMRP_PLUGINDIR . 'lib/filters-options.php';

			new WpssoMrpFiltersOptions( $plugin, $addon );

			require_once WPSSOMRP_PLUGINDIR . 'lib/filters-upgrade.php';

			new WpssoMrpFiltersUpgrade( $plugin, $addon );

			if ( is_admin() ) {

				require_once WPSSOMRP_PLUGINDIR . 'lib/filters-edit.php';

				new WpssoMrpFiltersEdit( $plugin, $addon );

				require_once WPSSOMRP_PLUGINDIR . 'lib/filters-messages.php';

				new WpssoMrpFiltersMessages( $plugin, $addon );
			}
		}
	}
}
