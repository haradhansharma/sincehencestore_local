<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWpsmFiltersMessages' ) ) {

	class WpssoWpsmFiltersMessages {

		private $p;	// Wpsso class object.
		private $a;	// WpssoWpsm class object.

		/*
		 * Instantiated by WpssoWpsmFilters->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'messages_info'    => 2,
				'messages_tooltip' => 2,
			) );
		}

		public function filter_messages_info( $text, $msg_key ) {

			if ( 0 !== strpos( $msg_key, 'info-wpsm-' ) ) {

				return $text;
			}

			switch ( $msg_key ) {

				case 'info-wpsm-settings':

					$addon_name = $this->p->cf[ 'plugin' ][ 'wpssowpsm' ][ 'name' ];

					$sitemap_url = get_site_url( $blog_id = null, $path = '/wp-sitemap.xml' );

					// translators: Please ignore - translation uses a different text domain.
					$no_index_label = _x( 'No Index', 'option label', 'wpsso' );

					// translators: Please ignore - translation uses a different text domain.
					$metabox_title = _x( $this->p->cf[ 'meta' ][ 'title' ], 'metabox title', 'wpsso' );

					// translators: Please ignore - translation uses a different text domain.
					$visibility = _x( 'Edit Visibility', 'metabox tab', 'wpsso' );

					$about_wp55 = __( 'https://make.wordpress.org/core/2020/07/22/new-xml-sitemaps-functionality-in-wordpress-5-5/', 'wpsso-wp-sitemaps' );

					$text = '<blockquote class="top-info">';

					$text .= '<p>';

					$text .= __( 'These options allow you to customize the post and taxonomy types included in the WordPress sitemaps XML.',
						'wpsso-wp-sitemaps' ) . ' ';

					$text .= sprintf( __( 'To <strong>exclude</strong> individual posts, pages, custom post types, taxonomy terms, or user profile pages from the sitemaps XML, enable the <strong>%1$s</strong> option under the %2$s &gt; %3$s metabox tab.', 'wpsso-wp-sitemaps' ), $no_index_label, $metabox_title, $visibility ) . ' ';

					$text .= '</p>';

					$text .= '</blockquote>';

					break;

			}

			return $text;
		}

		public function filter_messages_tooltip( $text, $msg_key ) {

			if ( 0 !== strpos( $msg_key, 'tooltip-wpsm_' ) ) {

				return $text;
			}

			switch ( $msg_key ) {

				case 'tooltip-wpsm_sitemaps_url':

					$text = __( 'The WordPress sitemaps URL can be submitted in Google\'s search console and viewed in a web browser.',
						'wpsso-wp-sitemaps' );

					break;

				case 'tooltip-wpsm_sitemaps_for':

					$text = __( 'Select the post types and taxonomies to include in the WordPress sitemaps.', 'wpsso-wp-sitemaps' );

					break;

				case 'tooltip-wpsm_news_post_type':	// Post Type for News Sitemaps.

					$def_val           = $this->p->opt->get_defaults( 'wpsm_news_post_type' );
					$news_pub_max_time = human_time_diff( 0, WPSSOWPSM_NEWS_PUB_MAX_TIME );

					$text = sprintf( __( 'If you are a news publisher, you may select a post type for your news articles (default is %s).',
						'wpsso-wp-sitemaps' ), $def_val ) . ' ';

					$text .= sprintf( __( 'When this option is enabled, news tags in sitemaps are added to news articles that are less than %s old.',
						'wpsso-wp-sitemaps' ), $news_pub_max_time );

					break;

				case 'tooltip-wpsm_news_pub_max_time':	// News Publication Cut-Off.

					$news_pub_max_time = human_time_diff( 0, WPSSOWPSM_NEWS_PUB_MAX_TIME );

					$text = sprintf( __( 'Google only allows news tags in sitemaps for articles that were published in the last %s.',
						'wpsso-wp-sitemaps' ), $news_pub_max_time ) . ' ';

					break;

				case 'tooltip-wpsm_news_pub_name':	// News Publication Name.

					$text = __( 'The name of the news publication.', 'wpsso-wp-sitemaps' ) . ' ';

					$text .= sprintf( __( 'It must exactly match the name as it appears on your articles on %s, omitting anything in parentheses.',
						'wpsso-wp-sitemaps' ), '<a href="https://news.google.com/">news.google.com</a>' ) . ' ';

					break;

				case 'tooltip-wpsm_schema_images':	// Include Images Sitemaps.

					$text = __( 'Include images in the WordPress sitemaps from the webpage Schema markup.',
						'wpsso-wp-sitemaps' ) . ' ';

					$text .= __( 'Google already reads image URLs and image information from the webpage Schema markup, so this option is not required.',
						'wpsso-wp-sitemaps' );

					break;

				case 'tooltip-wpsm_schema_videos':	// Include Videos Sitemaps.

					$text = __( 'Include videos in the WordPress sitemaps from the webpage Schema markup.',
						'wpsso-wp-sitemaps' ) . ' ';

					break;

				case 'tooltip-wpsm_max_urls':	// Maximum URLs per Sitemap.

					$def_val = $this->p->opt->get_defaults( 'wpsm_max_urls' );

					$text .= sprintf( __( 'The maximum number of URLs to include in each sitemap (default is %s).',
						'wpsso-wp-sitemaps' ), $def_val ) . ' ';

					$text .= sprintf( __( 'The WordPress default is %1$s URLs per sitemap, but Google only allows %2$s news tags in sitemaps.',
						'wpsso-wp-sitemaps' ), 2000, 1000 ) . ' ';

					$text .= sprintf( __( 'If news sitemaps are enabled, sitemaps for post objects will be limited to %s URLs automatically.',
						'wpsso-wp-sitemaps' ), 1000 ) . ' ';

					break;
			}

			return $text;
		}
	}
}
