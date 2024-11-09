<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoCmcfFiltersAdvanced' ) ) {

	class WpssoCmcfFiltersAdvanced {

		private $p;	// Wpsso class object.
		private $a;	// WpssoCmcf class object.

		/*
		 * Instantiated by WpssoCmcfFilters->__construct().
		 */
		public function __construct( &$plugin, &$addon ) {

			$this->p =& $plugin;
			$this->a =& $addon;

			$this->p->util->add_plugin_filters( $this, array(
				'mb_advanced_plugin_image_sizes_rows' => 3,
			), $prio = 100 );
		}

		/*
		 * SSO > Advanced Settings > Plugin Settings > Image Sizes tab.
		 */
		public function filter_mb_advanced_plugin_image_sizes_rows( $table_rows, $form, $network ) {

			if ( $this->p->debug->enabled ) { 

				$this->p->debug->mark();
			}

			$table_rows[ 'cmcf_img_size' ] = '' .
				$form->get_th_html( _x( 'Commerce Manager Catalog Feed XML', 'option label', 'wpsso-commerce-manager-catalog-feed' ),
					$css_class = '', $css_id = 'cmcf_img_size' ) . ( $this->p->check->pp() ?
				'<td>' . $form->get_input_image_dimensions( 'cmcf_img' ) . '</td>' :
				'<td class="blank">' . $form->get_no_input_image_dimensions( 'cmcf_img' ) . '</td>' );

			return $table_rows;
		}
	}
}
