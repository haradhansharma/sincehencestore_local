<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWpsmConflict' ) ) {

	class WpssoWpsmConflict {

		private $p;		// Wpsso class object.
		private $a;		// WpssoWpsm class object.
		private $seo = null;	// WpssoWpsmConflictSeo class object.

		/*
		 * Instantiated by WpssoWpsm->init_objects().
		 */
		public function __construct( &$plugin, &$addon ) {

			static $do_once = null;

			if ( $do_once ) return;	// Stop here.

			$do_once = true;

			$this->p =& $plugin;
			$this->a =& $addon;

			$doing_ajax = SucomUtilWP::doing_ajax();

			if ( ! $doing_ajax ) {

				if ( ! SucomUtilWP::doing_block_editor() ) {

					add_action( 'admin_head', array( $this, 'conflict_checks' ), -1000 );
				}
			}
		}

		public function conflict_checks() {

			require_once WPSSOWPSM_PLUGINDIR . 'lib/conflict-seo.php';

			$this->seo = new WpssoWpsmConflictSeo( $this->p, $this->a );

			$this->seo->conflict_checks();
		}
	}
}
