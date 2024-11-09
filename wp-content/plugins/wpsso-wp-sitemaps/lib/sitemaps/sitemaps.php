<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWpsmSitemaps' ) && class_exists( 'WP_Sitemaps' ) ) {

	/*
	 * Since WPSSO WPSM v3.0.0.
	 *
	 * WpssoWpsmSitemaps extends WP_Sitemaps to set a different rederer class.
	 *
	 * See wordpress/wp-includes/sitemaps/class-wp-sitemaps.php.
	 */
	class WpssoWpsmSitemaps extends WP_Sitemaps {

		public function __construct() {

			parent::__construct();

			require_once WPSSOWPSM_PLUGINDIR . 'lib/sitemaps/renderer.php';

			if ( class_exists( 'WpssoWpsmSitemapsRenderer' ) ) {

				$this->renderer = new WpssoWpsmSitemapsRenderer();
			}
		}

		/*
		 * The name of the news publication. It must exactly match the name as it appears on your articles on
		 * news.google.com, omitting anything in parentheses.
		 *
		 * See https://developers.google.com/search/docs/crawling-indexing/sitemaps/news-sitemap.
		 */
		public static function get_news_pub_name( $mixed = 'current' ) {

			$wpsso =& Wpsso::get_instance();

			$news_pub_name = SucomUtilOptions::get_key_value( 'wpsm_news_pub_name', $wpsso->options, $mixed );
			$news_pub_name = trim( preg_replace( '/ *\(.*\) */', ' ', $news_pub_name ) );

			if ( empty( $news_pub_name ) ) {

				$news_pub_name = self::get_default_news_pub_name();
			}

			return $news_pub_name;
		}

		public static function get_default_news_pub_name( $mixed = 'current' ) {

			$wpsso =& Wpsso::get_instance();

			$news_pub_name = SucomUtilWP::get_site_name( $wpsso->options, $mixed );
			$news_pub_name = trim( preg_replace( '/ *\(.*\) */', ' ', $news_pub_name ) );

			return $news_pub_name;
		}
	}
}
