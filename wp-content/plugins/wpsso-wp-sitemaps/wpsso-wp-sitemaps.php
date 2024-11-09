<?php
/*
 * Plugin Name: WPSSO WP Sitemaps XML
 * Plugin Slug: wpsso-wp-sitemaps
 * Text Domain: wpsso-wp-sitemaps
 * Domain Path: /languages
 * Plugin URI: https://wpsso.com/extend/plugins/wpsso-wp-sitemaps/
 * Assets URI: https://surniaulula.github.io/wpsso-wp-sitemaps/assets/
 * Author: JS Morisset
 * Author URI: https://wpsso.com/
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Description: Improves the WordPress sitemaps XML with article modification times, alternate language URLs, images sitemaps, news sitemaps and more.
 * Requires Plugins: wpsso
 * Requires PHP: 7.2.34
 * Requires At Least: 5.8
 * Tested Up To: 6.6.1
 * WC Tested Up To: 9.2.2
 * Version: 8.4.0
 *
 * Version Numbering: {major}.{minor}.{bugfix}[-{stage}.{level}]
 *
 *      {major}         Major structural code changes and/or incompatible API changes (ie. breaking changes).
 *      {minor}         New functionality was added or improved in a backwards-compatible manner.
 *      {bugfix}        Backwards-compatible bug fixes or small improvements.
 *      {stage}.{level} Pre-production release: dev < a (alpha) < b (beta) < rc (release candidate).
 *
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoAbstractAddOn' ) ) {

	require_once dirname( __FILE__ ) . '/lib/abstract/add-on.php';
}

if ( ! class_exists( 'WpssoWpsm' ) ) {

	class WpssoWpsm extends WpssoAbstractAddOn {

		public $conflict;	// WpssoWpsmConflict class object.
		public $filters;	// WpssoWpsmFilters class object.
		public $sm_filters;	// WpssoWpsmSitemapsFilters class object.

		protected $p;		// Wpsso class object.

		private static $instance = null;	// WpssoWpsm class object.

		public function __construct() {

			parent::__construct( __FILE__, __CLASS__ );
		}

		public static function &get_instance() {

			if ( null === self::$instance ) {

				self::$instance = new self;
			}

			return self::$instance;
		}

		public function init_textdomain() {

			load_plugin_textdomain( 'wpsso-wp-sitemaps', false, 'wpsso-wp-sitemaps/languages/' );
		}

		/*
		 * Called by Wpsso->set_objects() which runs at init priority 10.
		 */
		public function init_objects_preloader() {

			$this->p =& Wpsso::get_instance();

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( $this->get_missing_requirements() ) {	// Returns false or an array of missing requirements.

				return;	// Stop here.
			}

			new WpssoWpsmConflict( $this->p, $this );
			new WpssoWpsmFilters( $this->p, $this );
			new WpssoWpsmSitemapsFilters( $this->p, $this );
		}
	}

	WpssoWpsm::get_instance();
}
