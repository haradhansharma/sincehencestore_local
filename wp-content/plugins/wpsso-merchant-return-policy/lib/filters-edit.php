<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoMrpFiltersEdit' ) ) {

	class WpssoMrpFiltersEdit {

		private $p;	// Wpsso class object.
		private $a;	// WpssoMrp class object.

		/*
		 * Instantiated by WpssoMrpFilters->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'form_cache_mrp_names' => 1,
				'mb_mrp_rows'          => 4,
			) );
		}

		public function filter_form_cache_mrp_names( $mixed ) {

			$mrp_names = WpssoMrpMrp::get_names();

			return is_array( $mixed ) ? $mixed + $mrp_names : $mrp_names;
		}

		/*
		 * See https://developers.google.com/search/docs/appearance/structured-data/product#merchant-listings_merchant-return-policy.
		 */
		public function filter_mb_mrp_rows( $table_rows, $form, $head_info, $mod ) {

			$countries_msg = $this->p->msgs->get( 'info-meta-mrp-countries' );

			$form_rows = array(
				'mrp_name' => array(
					'th_class' => 'medium',
					'label'    => _x( 'Return Policy Name', 'option label', 'wpsso-merchant-return-policy' ),
					'tooltip'  => 'meta-mrp_name',
					'content'  => $form->get_input( 'mrp_name', $css_class = 'wide' ),
				),
				'mrp_defaults' => array(
					'th_class' => 'medium',
					'label'    => _x( 'Return Policy Is Default', 'option label', 'wpsso-merchant-return-policy' ),
					'tooltip'  => 'meta-mrp_is_default',
					'content'  => $form->get_checklist( 'mrp_is', $this->p->cf[ 'form' ][ 'mrp_is_defaults' ] ),
				),
				'subsection_mrp' => array(
					'td_class' => 'subsection',
					'header'   => 'h5',
					'label'    => _x( 'Return Policy Information', 'metabox title', 'wpsso-merchant-return-policy' ),
				),
				'mrp_category' => array(
					'th_class' => 'medium',
					'label'    => _x( 'Return Policy Category', 'option label', 'wpsso-merchant-return-policy' ),
					'tooltip'  => 'meta-mrp_category',
					'content'  => $form->get_select( 'mrp_category', $this->p->cf[ 'form' ][ 'merchant_return' ],
						$css_class = '', $css_id = '', $is_assoc = true, $is_disabled = false,
							$selected = false, $event_names = 'on_change_unhide_rows' ),
				),
				'mrp_return_days' => array(
					'tr_class' => $form->get_css_class_on_change( $select_id = 'mrp_category',
						$select_value = 'https://schema.org/MerchantReturnFiniteReturnWindow' ),
					'th_class' => 'medium',
					'label'    => _x( 'Return Window Days', 'option label', 'wpsso-merchant-return-policy' ),
					'tooltip'  => 'meta-mrp_return_days',
					'content'  => $form->get_input( 'mrp_return_days', $css_class = 'xshort' ),
				),
				'mrp_methods' => array(
					'tr_class' => $form->get_css_class_on_change( $select_id = 'mrp_category',
						$select_values = array(
							'https://schema.org/MerchantReturnFiniteReturnWindow',
							'https://schema.org/MerchantReturnUnlimitedWindow',
						) ),
					'th_class' => 'medium',
					'label'    => _x( 'Return Methods', 'option label', 'wpsso-merchant-return-policy' ),
					'tooltip'  => 'meta-mrp_methods',
					'content' => $form->get_checklist( 'mrp_method', $this->p->cf[ 'form' ][ 'return_method' ],
						$css_class = 'column-list', $css_id = '', $is_assoc = true, $is_disabled = false,
							$event_names = 'on_change_unhide_rows' ),
				),
				'mrp_return_fees' => array(
					'tr_class' => $form->get_css_class_on_change( $select_id = 'mrp_method_https_schema_org_ReturnByMail', $select_value = 1 ),
					'th_class' => 'medium',
					'label'    => _x( 'Return Fees', 'option label', 'wpsso-merchant-return-policy' ),
					'tooltip'  => 'meta-mrp_return_fees',
					'content'  => $form->get_select( 'mrp_return_fees', $this->p->cf[ 'form' ][ 'return_fees' ],
						$css_class = '', $css_id = '', $is_assoc = true, $is_disabled = false,
							$selected = false, $event_names = 'on_change_unhide_rows' ),
				),
				'mrp_shipping_fees' => array(
					'tr_class' => $form->get_css_class_on_change( $select_id = 'mrp_return_fees',
						$select_values = array(
							'https://schema.org/ReturnShippingFees',
						) ),
					'th_class' => 'medium',
					'label'    => _x( 'Shipping Fees', 'option label', 'wpsso-merchant-return-policy' ),
					'tooltip'  => 'meta-mrp_shipping_fees',
					'content'  => $form->get_amount_currency( 'mrp_shipping_amount', 'mrp_shipping_currency' ),
				),
				'mrp_countries' => array(
					'th_class' => 'medium',
					'label'   => _x( 'Applicable Countries', 'option label', 'wpsso-merchant-return-policy' ),
					'tooltip' => 'meta-mrp_countries',
					'content' => $countries_msg . $form->get_checklist_countries( 'mrp_country', $css_class = 'column-list num-cols-2' ),
				),
			);

			return $form->get_md_form_rows( $table_rows, $form_rows, $head_info, $mod );
		}
	}
}
