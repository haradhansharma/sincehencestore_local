<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoCmcfSubmenuFacebookCatalog' ) && class_exists( 'WpssoAdmin' ) ) {

	class WpssoCmcfSubmenuFacebookCatalog extends WpssoAdmin {

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
				'feed' => _x( 'Commerce Manager Catalog Feed XML', 'metabox title', 'wpsso-commerce-manager-catalog-feed' ),
			);
		}

		protected function add_form_buttons( &$form_button_rows ) {

			/*
			 * Remove all buttons if a cache refresh is running.
			 */
			if ( $this->p->util->cache->is_refresh_running() ) {

				return array();
			}

			/*
			 * Add a "Refresh XML Cache" button.
			 */
			$form_button_rows[ 0 ][ 'refresh_feed_xml_cache' ] = _x( 'Refresh XML Cache', 'submit button', 'wpsso-commerce-manager-catalog-feed' );
		}

		protected function get_table_rows( $page_id, $metabox_id, $tab_key = '', $args = array() ) {

			$table_rows = array();
			$match_rows = trim( $page_id . '-' . $metabox_id . '-' . $tab_key, '-' );
			$is_public  = get_option( 'blog_public' );

			if ( $this->p->util->cache->is_refresh_running() ) {

				$task_name_transl = _x( 'refresh the cache', 'task name', 'wpsso-commerce-manager-catalog-feed' );

				$table_rows[ 'wpssocmcf_disabled' ] = '<tr><td align="center">' .
					'<p class="status-msg">' . sprintf( __( 'A background task to %s is currently running.',
						'wpsso-commerce-manager-catalog-feed' ), $task_name_transl ) . '</p>' .
					'<p class="status-msg">' . sprintf( __( '%s is currently unavailable pending completion of a cache refresh task.',
						'wpsso-commerce-manager-catalog-feed' ), $args[ 'metabox_title' ] ) . '</p>' .
					'</td></tr>';

				return $table_rows;

			} elseif ( ! $is_public ) {

				$settings_url         = get_admin_url( $blog_id = null, 'options-reading.php' );
				$noindex_label_transl = _x( 'No Index', 'option label', 'wpsso' );
				$directives           = WpssoUtilRobots::get_default_directives();

				if ( ! empty( $directives[ 'noindex' ] ) ) {	// Just in case.

					$table_rows[ 'wpssocmcf_disabled' ] = '<tr><td align="center">' .
						'<p class="status-msg">' . sprintf( __( 'The WordPress <a href="%s">Search Engine Visibility</a> option is set to discourage search engines from indexing this site.', 'wpsso-commerce-manager-catalog-feed' ), $settings_url ) . '</p>' .
						'<p class="status-msg">' . sprintf( __( '%1$s is currently unavailable since all products are marked as %2$s by default.',
							'wpsso-commerce-manager-catalog-feed' ), $args[ 'metabox_title' ], $noindex_label_transl ) . '</p>' .
						'</td></tr>';

					return $table_rows;
				}
			}

			switch ( $match_rows ) {

				case 'facebook-catalog-feed':

					$table_rows[ 'cmcf_feed_exp_secs' ] = $this->form->get_tr_hide( $in_view = 'basic', 'cmcf_feed_exp_secs' ) .
						$this->form->get_th_html( _x( 'XML Cache Expiration', 'option label', 'wpsso-commerce-manager-catalog-feed' ),
							$css_class = '', $css_id = 'cmcf_feed_exp_secs' ) .
						'<td>' . $this->form->get_input( 'cmcf_feed_exp_secs', 'medium' ) . ' ' .
							_x( 'seconds', 'option comment', 'wpsso-commerce-manager-catalog-feed' ) . '</td>';

					$table_rows[ 'cmcf_feed_format' ] = $this->form->get_tr_hide( $in_view = 'basic', 'cmcf_feed_format' ) .
						$this->form->get_th_html( _x( 'XML Format', 'option label', 'wpsso-commerce-manager-catalog-feed' ),
							$css_class = '', $css_id = 'cmcf_feed_format' ) .
						'<td>' . $this->form->get_select( 'cmcf_feed_format', $this->p->cf[ 'form' ][ 'feed_formats' ], 'medium' ) . '</td>';

					$locale_names = SucomUtilWP::get_available_feed_locale_names();

					foreach ( $locale_names as $locale => $native_name ) {

						$feed_type   = 'feed';
						$feed_format = $this->p->options[ 'cmcf_' . $feed_type . '_format' ];
						$url         = WpssoCmcfRewrite::get_url( $locale, $feed_type, $feed_format );
						$css_id      = SucomUtil::sanitize_css_id( 'cmcf_feed_xml_' . $locale );
						$xml_info    = array();

						if ( ! SucomUtil::get_const( 'WPSSOCMCF_XML_INFO_DISABLE', false ) ) {

							$xml         = WpssoCmcfXml::get( $locale, $feed_type, $feed_format );
							$item_count  = substr_count( $xml, 'atom' === $feed_format? '<entry>' : '<item>' );
							$img_count   = substr_count( $xml, '<g:image_link>' );
							$addl_count  = substr_count( $xml, '<g:additional_image_link>' );
							$xml_size    = number_format( ( strlen( $xml ) / 1024 ) );	// XML size in KB.

							unset( $xml );

							$xml_info = array(
								sprintf( _x( '%s feed items', 'option comment', 'wpsso-commerce-manager-catalog-feed' ), $item_count ),
								sprintf( _x( '%s image links', 'option comment', 'wpsso-commerce-manager-catalog-feed' ), $img_count ),
								sprintf( _x( '%s additional image links', 'option comment', 'wpsso-commerce-manager-catalog-feed' ), $addl_count ),
								sprintf( _x( '%s KB file size', 'option comment', 'wpsso-commerce-manager-catalog-feed' ), $xml_size ),
							);
						}

						$table_rows[ $css_id ] = '' .
							$this->form->get_th_html( $native_name, $css_class = '', $css_id,
								array( 'locale' => $locale, 'native_name' => $native_name ) ) .
							'<td>' . $this->form->get_no_input_clipboard( $url ) .
							'<p class="status-msg left">' . implode( '; ', $xml_info ) . '</p></td>';
					}

					break;
			}

			return $table_rows;
		}
	}
}
