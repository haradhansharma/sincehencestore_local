<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoCmcfFiltersMessages' ) ) {

	class WpssoCmcfFiltersMessages {

		private $p;	// Wpsso class object.
		private $a;	// WpssoCmcf class object.

		/*
		 * Instantiated by WpssoCmcfFilters->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'messages_info'         => 3,
				'messages_tooltip'      => 3,
				'messages_tooltip_meta' => 2,
			) );
		}

		public function filter_messages_info( $text, $msg_key, $info ) {

			if ( 0 !== strpos( $msg_key, 'info-cmcf-' ) ) {

				return $text;
			}

			switch ( $msg_key ) {

				case 'info-cmcf-img':

					/*
					 * See https://www.facebook.com/business/help/686259348512056?id=725943027795860.
					 */
					$text = '<p class="pro-feature-msg">';

					$text .= __( 'Each image must be in JPEG or PNG format and a maximum of 8 MB.',
						'wpsso-commerce-manager-catalog-feed' ) . ' ';

					$text .= __( 'Images must accurately represent the exact product for sale.',
						'wpsso-commerce-manager-catalog-feed' ) . ' ';

					$text .= __( 'Don\'t include offensive content such as nudity, explicit language or violence.',
						'wpsso-commerce-manager-catalog-feed' ) . ' ';

					$text .= __( 'Don\'t include text that overlays the product, calls to action, promo codes, watermarks or time-sensitive information like temporary price drops.',
						'wpsso-commerce-manager-catalog-feed' ) . ' ';

					if ( ! empty( $this->p->avail[ 'ecom' ][ 'woocommerce' ] ) ) {

						if ( 'product' === $info[ 'mod' ][ 'post_type' ] ) {	// WooCommerce product editing page.

							if ( $this->p->util->wc->is_mod_variable( $info[ 'mod' ] ) ) {

								$text .= __( 'This is a variable product - images from product variations will supersede the main product image selected here.',
									'wpsso-commerce-manager-catalog-feed' ) . ' ';
							}
						}
					}

					$text .= '</p>';

					break;
			}

			return $text;
		}

		public function filter_messages_tooltip( $text, $msg_key, $info ) {

			if ( 0 !== strpos( $msg_key, 'tooltip-cmcf_' ) ) {

				return $text;
			}

			switch ( $msg_key ) {

				case 'tooltip-cmcf_img_size':

					$def_img_dims = $this->p->msgs->get_def_img_dims( 'cmcf' );

					$text = sprintf( __( 'The dimensions used for the Facebook catalog feed XML image (default dimensions are %s).',
						'wpsso-commerce-manager-catalog-feed' ), $def_img_dims ) . ' ';

					break;

				case 'tooltip-cmcf_feed_exp_secs':

					$def_value = $this->p->opt->get_defaults( 'cmcf_feed_exp_secs' );

					$text = sprintf( __( 'The XML file cache expiration time in seconds (default is %s).',
						'wpsso-commerce-manager-catalog-feed' ), $def_value ) . ' ';

					$text .= __( 'When a product is updated, or the cache expires, the XML file cache is automatically refreshed.',
						'wpsso-commerce-manager-catalog-feed' ) . ' ';

					break;

				case 'tooltip-cmcf_feed_format':

					$def_value = $this->p->opt->get_defaults( str_replace( 'tooltip-', '', $msg_key ) );
					$def_label = $this->p->cf[ 'form' ][ 'feed_formats' ][ $def_value ];

					$text = sprintf( __( 'Choose the XML format for URLs shown in the settings page (default is %s).',
						'wpsso-commerce-manager-catalog-feed' ), $def_label ) . ' ';

					break;

				case ( 0 === strpos( $msg_key, 'tooltip-cmcf_feed_xml_' ) ? true : false ):

					$metabox_title =_x( 'Commerce Manager Catalog Feed XML', 'metabox title', 'wpsso-commerce-manager-catalog-feed' );

					$text = sprintf( __( 'The %1$s includes all public WordPress post objects (ie. posts, pages, and custom post types) with an Open Graph type of "product" and a language of %2$s (ie. locale "%3$s").', 'wpsso-commerce-manager-catalog-feed' ), $metabox_title, $info[ 'native_name' ], $info[ 'locale' ] );

					break;

			}	// End of 'tooltip' switch.

			return $text;
		}

		public function filter_messages_tooltip_meta( $text, $msg_key ) {

			if ( 0 !== strpos( $msg_key, 'tooltip-meta-cmcf_' ) ) {

				return $text;
			}

			switch ( $msg_key ) {

				/*
				 * Document SSO > Edit Media tab.
				 */
				case 'tooltip-meta-cmcf_img_id':		// Image ID.

					$text = __( 'A customized image ID for the Facebook catalog feed XML.',
						'wpsso-commerce-manager-catalog-feed' ) . ' ';

					$text .= __( 'The default value is inherited from the priority image.',
						'wpsso-commerce-manager-catalog-feed' ) . ' ';

					$text .= '<em>' . __( 'This option is disabled if a custom image URL is entered.',
						'wpsso-commerce-manager-catalog-feed' ) . '</em>';

				 	break;

				case 'tooltip-meta-cmcf_img_url':	// or an Image URL.

					$text = __( 'A customized image URL (instead of an image ID) for the Facebook catalog feed XML.',
						'wpsso-commerce-manager-catalog-feed' ) . ' ';

					$text .= __( 'The default value is inherited from the priority image.',
						'wpsso-commerce-manager-catalog-feed' ) . ' ';

					$text .= '<em>' . __( 'This option is disabled if a custom image ID is selected.',
						'wpsso-commerce-manager-catalog-feed' ) . '</em>';

				 	break;

			}	// End of 'tooltip-meta' switch.

			return $text;
		}
	}
}
