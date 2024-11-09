<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2014-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoMrpFiltersMessages' ) ) {

	class WpssoMrpFiltersMessages {

		private $p;	// Wpsso class object.
		private $a;	// WpssoMrp class object.

		/*
		 * Instantiated by WpssoMrpFilters->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'messages_tooltip_meta' => 3,
				'messages_info_meta'    => 3,
			) );
		}

		public function filter_messages_tooltip_meta( $text, $msg_key, $info ) {

			if ( 0 !== strpos( $msg_key, 'tooltip-meta-mrp_' ) ) return $text;

			/*
			 * See https://developers.google.com/search/docs/appearance/structured-data/merchant-listing#returns.
			 */
			switch ( $msg_key ) {

				case 'tooltip-meta-mrp_name':	// Return Policy Name.

					$text = __( 'A name for this return policy (required).', 'wpsso-merchant-return-policy' ) . ' ';

					$text .= __( 'The return policy name may appear in WordPress editing pages and in the Schema MerchantReturnPolicy "name" property.',
						'wpsso-merchant-return-policy' );

					break;

				case 'tooltip-meta-mrp_is_default':	// Return Policy Is Default.

					$text = __( 'You may choose this return policy as the default.', 'wpsso' ) . ' ';

					break;

				case 'tooltip-meta-mrp_category':	// Return Policy Category.

					$text = __( 'The type of return policy.', 'wpsso-merchant-return-policy' ) . ' ';

					break;

				case 'tooltip-meta-mrp_return_days':	// Return Window Days.

					$text = __( 'The number of days from the delivery date that a product can be returned.', 'wpsso-merchant-return-policy' ) . ' ';

					break;

				case 'tooltip-meta-mrp_methods':	// Return Methods.

					$text = __( 'The type of return methods offered.', 'wpsso-merchant-return-policy' ) . ' ';

					break;

				case 'tooltip-meta-mrp_return_fees':	// Shipping Fees.

					$text = __( 'The type of return fees.', 'wpsso-merchant-return-policy' ) . ' ';

					break;

				case 'tooltip-meta-mrp_shipping_fees':	// Shipping Fees.

					$text = __( 'The cost of shipping for returning a product.', 'wpsso-merchant-return-policy' ) . ' ';

					break;

				case 'tooltip-meta-mrp_countries':	// Applicable Countries.

					$text = __( 'The countries this return policy applies to.', 'wpsso-merchant-return-policy' ) . ' ';

					$text .= sprintf( __( 'Note that Google limits the selection to a maximum of %d countries.',
						'wpsso-merchant-return-policy' ), WPSSOMRP_MRP_COUNTRIES_MAX ) . ' ';

					break;
			}

			return $text;
		}

		public function filter_messages_info_meta( $text, $msg_key, $info ) {

			if ( 0 !== strpos( $msg_key, 'info-meta-mrp-' ) ) return $text;

			switch ( $msg_key ) {

				/*
				 * SSO Returns > Edit Return Policy page.
				 */
				case 'info-meta-mrp-countries':

					$text = '<p class="status-msg top">';

					$text .= sprintf( __( 'Note that Google limits this selection to a maximum of %d countries.',
						'wpsso-merchant-return-policy' ), WPSSOMRP_MRP_COUNTRIES_MAX ) . ' ';


					$text .= '</p>';

					break;
			}

			return $text;
		}
	}
}
