<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoMrpFiltersUpgrade' ) ) {

	class WpssoMrpFiltersUpgrade {

		private $p;	// Wpsso class object.
		private $a;	// WpssoMrp class object.

		/*
		 * Instantiated by WpssoMrpFilters->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'rename_options_keys' => 1,
			) );
		}

		public function filter_rename_options_keys( $rename_options ) {

			$rename_options[ 'wpssomrp' ] = array(
				7 => array(	// Less than or equal to.
					'mrp_days'          => 'mrp_return_days',
					'mrp_shipping_fees' => 'mrp_shipping_amount',
				),
			);

			return $rename_options;
		}
	}
}
