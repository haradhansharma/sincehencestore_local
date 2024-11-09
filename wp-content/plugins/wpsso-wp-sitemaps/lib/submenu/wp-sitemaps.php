<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWpsmSubmenuWpSitemaps' ) && class_exists( 'WpssoAdmin' ) ) {

	class WpssoWpsmSubmenuWpSitemaps extends WpssoAdmin {

		public function __construct( &$plugin, $id, $name, $lib, $ext ) {

			$this->p =& $plugin;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$this->menu_id   = $id;
			$this->menu_name = $name;
			$this->menu_lib  = $lib;
			$this->menu_ext  = $ext;

			$this->menu_metaboxes = array(
				'settings' => _x( 'WordPress Sitemaps', 'metabox title', 'wpsso-wp-sitemaps' ),
			);
		}

		/*
		 * Remove the "Change to View" button from this settings page.
		 */
		protected function add_form_buttons_change_show_options( &$form_button_rows ) {
		}

		protected function get_table_rows( $page_id, $metabox_id, $tab_key = '', $args = array() ) {

			$table_rows = array();

			$table_rows[] = '<td colspan="2">' . $this->p->msgs->get( 'info-wpsm-settings' ) . '</td>';

			if ( SucomUtilWP::sitemaps_disabled() ) {	// Nothing to do.

				return $this->p->msgs->get_wp_sitemaps_disabled_rows( $table_rows );
			}

			$sitemaps_url      = get_site_url( $blog_id = null, $path = '/wp-sitemap.xml' );
			$post_types        = SucomUtilWP::get_post_type_labels();
			$def_news_pub_name = WpssoWpsmSitemaps::get_default_news_pub_name();
			$news_pub_max_time = human_time_diff( 0, WPSSOWPSM_NEWS_PUB_MAX_TIME );

			$videos_requires = $this->p->check->is_pp() ? '' : sprintf( _x( '(requires %s for video details)',
				'option comment', 'wpsso-wp-sitemaps' ), $this->p->util->get_pkg_info( 'wpsso', 'short_pro' ) );

			$urls_limited = sprintf( _x( '(post sitemaps limited to %d when news sitemaps enabled)',
				'option comment', 'wpsso-wp-sitemaps' ), 1000 );

			$table_rows[ 'wpsm_sitemaps_url' ] = '' .
				$this->form->get_th_html( _x( 'WordPress Sitemaps URL', 'option label', 'wpsso-wp-sitemaps' ),
					$css_class = '', $css_id = 'wpsm_sitemaps_url' ) .
				'<td>' . $this->form->get_no_input_clipboard( $sitemaps_url ) . '</td>';

			$table_rows[ 'wpsm_sitemaps_for' ] = '' .
				$this->form->get_th_html( _x( 'Include in WP Sitemaps', 'option label', 'wpsso-wp-sitemaps' ),
					$css_class = '', $css_id = 'wpsm_sitemaps_for' ) .
				'<td>' . $this->form->get_checklist_post_tax_user( $name_prefix = 'wpsm_sitemaps_for' ) . '</td>';

			$table_rows[ 'wpsm_news_post_type' ] = '' .
				$this->form->get_th_html( _x( 'Post Type for News Sitemaps', 'option label', 'wpsso-wp-sitemaps' ),
					$css_class = '', $css_id = 'wpsm_news_post_type' ) .
				'<td>' . $this->form->get_select_none( 'wpsm_news_post_type', $post_types ) . '</td>';

			$table_rows[ 'wpsm_news_pub_max_time' ] = '' .
				$this->form->get_th_html( _x( 'News Publication Cut-Off', 'option label', 'wpsso-wp-sitemaps' ),
					$css_class = '', $css_id = 'wpsm_news_pub_max_time' ) .
				'<td>' . $this->form->get_no_input_holder( $news_pub_max_time ) . '</td>';

			$table_rows[ 'wpsm_news_pub_name' ] = '' .
				$this->form->get_th_html_locale( _x( 'News Publication Name', 'option label', 'wpsso-wp-sitemaps' ),
					$css_class = '', $css_id = 'wpsm_news_pub_name' ) .
				'<td>' . $this->form->get_input_locale( 'wpsm_news_pub_name', $css_class = 'long_name', $css_id = '',
					$len = 0, $def_news_pub_name ) . '</td>';

			$table_rows[ 'wpsm_schema_images' ] = '' .
				$this->form->get_th_html( _x( 'Include Images Sitemaps', 'option label', 'wpsso-wp-sitemaps' ),
					$css_class = '', $css_id = 'wpsm_schema_images' ) .
				'<td>' . $this->form->get_checkbox( 'wpsm_schema_images' ) . ' ' .
				_x( '(not required)', 'option comment', 'wpsso-wp-sitemaps' ) . '</td>';

			$table_rows[ 'wpsm_schema_videos' ] = '' .
				$this->form->get_th_html( _x( 'Include Videos Sitemaps', 'option label', 'wpsso-wp-sitemaps' ),
					$css_class = '', $css_id = 'wpsm_schema_videos' ) .
				( $this->p->check->is_pp() ? '<td>' . $this->form->get_checkbox( 'wpsm_schema_videos' ) . '</td>' :
					$this->form->get_no_td_checkbox( 'wpsm_schema_videos', $videos_requires ) );

			$table_rows[ 'wpsm_max_urls' ] = '' .
				$this->form->get_th_html( _x( 'Maximum URLs per Sitemap', 'option label', 'wpsso-wp-sitemaps' ),
					$css_class = '', $css_id = 'wpsm_max_urls' ) .
				'<td>' . $this->form->get_input( 'wpsm_max_urls', $css_class = 'xshort' ) . ' ' . $urls_limited . '</td>';

			return $table_rows;
		}
	}
}
