<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWpsmFiltersOptions' ) ) {

	class WpssoWpsmFiltersOptions {

		private $p;	// Wpsso class object.
		private $a;	// WpssoWpsm class object.

		/*
		 * Instantiated by WpssoWpsmFilters->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'add_custom_post_type_options' => 1,
				'add_custom_taxonomy_options'  => 1,
				'option_type'                  => 2,
			) );
		}

		public function filter_add_custom_post_type_options( $opt_prefixes ) {

			$opt_prefixes[ 'wpsm_sitemaps_for' ] = 1;

			return $opt_prefixes;
		}

		public function filter_add_custom_taxonomy_options( $opt_prefixes ) {

			$opt_prefixes[ 'wpsm_sitemaps_for_tax' ] = 1;

			return $opt_prefixes;
		}

		/*
		 * Return the sanitation type for a given option key.
		 */
		public function filter_option_type( $type, $base_key ) {

			if ( ! empty( $type ) ) {	// Return early if we already have a type.

				return $type;

			} elseif ( 0 !== strpos( $base_key, 'wpsm_' ) ) {	// Nothing to do.

				return $type;
			}

			switch ( $base_key ) {

				case 'wpsm_schema_images':	// Include Images Sitemaps.
				case 'wpsm_schema_videos':	// Include Videos Sitemaps.
				case ( 0 === strpos( $base_key, 'wpsm_sitemaps_for_' ) ? true : false ):

					return 'checkbox';

				case 'wpsm_news_post_type':	// Post Type for News Sitemaps.

					return 'not_blank';

				case 'wpsm_news_pub_name':	// News Publication Name.

					return 'ok_blank';

				case 'wpsm_max_urls':		// Maximum URLs per Sitemap.

					return 'pos_int';
			}

			return $type;
		}
	}
}
