<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2023-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoMrpRegister' ) ) {

	class WpssoMrpRegister {

		public function __construct() {

			register_activation_hook( WPSSOMRP_FILEPATH, array( $this, 'network_activate' ) );

			register_deactivation_hook( WPSSOMRP_FILEPATH, array( $this, 'network_deactivate' ) );

			if ( is_multisite() ) {

				add_action( 'wpmu_new_blog', array( $this, 'wpmu_new_blog' ), 10, 6 );

				add_action( 'wpmu_activate_blog', array( $this, 'wpmu_activate_blog' ), 10, 5 );
			}

			add_action( 'wpsso_init_options', array( __CLASS__, 'register_mrp_post_type' ), WPSSOMRP_MRP_MENU_ORDER, 0 );

			add_action( 'wpsso_init_options', array( __CLASS__, 'register_mrp_category_taxonomy' ), WPSSOMRP_MRP_MENU_ORDER, 0 );
		}

		/*
		 * Fires immediately after a new site is created.
		 */
		public function wpmu_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

			switch_to_blog( $blog_id );

			$this->activate_plugin();

			restore_current_blog();
		}

		/*
		 * Fires immediately after a site is activated (not called when users and sites are created by a Super Admin).
		 */
		public function wpmu_activate_blog( $blog_id, $user_id, $password, $signup_title, $meta ) {

			switch_to_blog( $blog_id );

			$this->activate_plugin();

			restore_current_blog();
		}

		public function network_activate( $sitewide ) {

			self::do_multisite( $sitewide, array( $this, 'activate_plugin' ) );
		}

		public function network_deactivate( $sitewide ) {

			self::do_multisite( $sitewide, array( $this, 'deactivate_plugin' ) );
		}

		/*
		 * uninstall.php defines constants before calling network_uninstall().
		 */
		public static function network_uninstall() {

			$sitewide = true;

			/*
			 * Uninstall from the individual blogs first.
			 */
			self::do_multisite( $sitewide, array( __CLASS__, 'uninstall_plugin' ) );
		}

		private static function do_multisite( $sitewide, $method, $args = array() ) {

			if ( is_multisite() && $sitewide ) {

				global $wpdb;

				$db_query = 'SELECT blog_id FROM '.$wpdb->blogs;

				$blog_ids = $wpdb->get_col( $db_query );

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );

					call_user_func_array( $method, array( $args ) );
				}

				restore_current_blog();

			} else {

				call_user_func_array( $method, array( $args ) );
			}
		}

		private function activate_plugin() {

			if ( class_exists( 'Wpsso' ) ) {

				/*
				 * Register plugin install, activation, update times.
				 */
				if ( class_exists( 'WpssoUtilReg' ) ) {	// Since WPSSO Core v6.13.1.

					$version = WpssoMrpConfig::get_version();

					WpssoUtilReg::update_ext_version( 'wpssoopm', $version );
				}

				self::register_mrp_post_type();

				self::register_mrp_category_taxonomy();

				flush_rewrite_rules( $hard = false );	// Update only the 'rewrite_rules' option, not the .htaccess file.
			}
		}

		private function deactivate_plugin() {

			unregister_post_type( WPSSOMRP_MRP_POST_TYPE );

			if ( defined( 'WPSSOMRP_MRP_CATEGORY_TAXONOMY' ) && WPSSOMRP_MRP_CATEGORY_TAXONOMY ) {

				unregister_taxonomy( WPSSOMRP_MRP_CATEGORY_TAXONOMY );
			}

			flush_rewrite_rules( $hard = false );	// Update only the 'rewrite_rules' option, not the .htaccess file.
		}

		private static function uninstall_plugin() {}

		public static function register_mrp_post_type() {

			$is_public = false;

			$labels = array(
				'name'                     => _x( 'Return Policies', 'post type general name', 'wpsso-merchant-return-policy' ),
				'singular_name'            => _x( 'Return Policy', 'post type singular name', 'wpsso-merchant-return-policy' ),
				'add_new'                  => __( 'Add Return Policy', 'wpsso-merchant-return-policy' ),
				'add_new_item'             => __( 'Add Return Policy', 'wpsso-merchant-return-policy' ),
				'edit_item'                => __( 'Edit Return Policy', 'wpsso-merchant-return-policy' ),
				'new_item'                 => __( 'New Return Policy', 'wpsso-merchant-return-policy' ),
				'view_item'                => __( 'View Return Policy', 'wpsso-merchant-return-policy' ),
				'view_items'               => __( 'View Return Policies', 'wpsso-merchant-return-policy' ),
				'search_items'             => __( 'Search Return Policies', 'wpsso-merchant-return-policy' ),
				'not_found'                => __( 'No return policies found', 'wpsso-merchant-return-policy' ),
				'not_found_in_trash'       => __( 'No return policies found in Trash', 'wpsso-merchant-return-policy' ),
				'parent_item_colon'        => __( 'Parent Return Policy:', 'wpsso-merchant-return-policy' ),
				'all_items'                => __( 'All Return Policies', 'wpsso-merchant-return-policy' ),
				'archives'                 => __( 'Return Policy Archives', 'wpsso-merchant-return-policy' ),
				'attributes'               => __( 'Return Policy Attributes', 'wpsso-merchant-return-policy' ),
				'insert_into_item'         => __( 'Insert into return policy', 'wpsso-merchant-return-policy' ),
				'uploaded_to_this_item'    => __( 'Uploaded to this return policy', 'wpsso-merchant-return-policy' ),
				'featured_image'           => __( 'Return Policy Image', 'wpsso-merchant-return-policy' ),
				'set_featured_image'       => __( 'Set return policy image', 'wpsso-merchant-return-policy' ),
				'remove_featured_image'    => __( 'Remove return policy image', 'wpsso-merchant-return-policy' ),
				'use_featured_image'       => __( 'Use as return policy image', 'wpsso-merchant-return-policy' ),
				'menu_name'                => _x( 'SSO Returns', 'admin menu name', 'wpsso-merchant-return-policy' ),
				'filter_items_list'        => __( 'Filter return policies', 'wpsso-merchant-return-policy' ),
				'items_list_navigation'    => __( 'Return Policies list navigation', 'wpsso-merchant-return-policy' ),
				'items_list'               => __( 'Return Policies list', 'wpsso-merchant-return-policy' ),
				'name_admin_bar'           => _x( 'Return Policy', 'admin bar name', 'wpsso-merchant-return-policy' ),
				'item_published'	   => __( 'Return Policy published.', 'wpsso-merchant-return-policy' ),
				'item_published_privately' => __( 'Return Policy published privately.', 'wpsso-merchant-return-policy' ),
				'item_reverted_to_draft'   => __( 'Return Policy reverted to draft.', 'wpsso-merchant-return-policy' ),
				'item_scheduled'           => __( 'Return Policy scheduled.', 'wpsso-merchant-return-policy' ),
				'item_updated'             => __( 'Return Policy updated.', 'wpsso-merchant-return-policy' ),
			);

			$supports = false;

			if ( defined( 'WPSSOMRP_MRP_CATEGORY_TAXONOMY' ) && WPSSOMRP_MRP_CATEGORY_TAXONOMY ) {

				$taxonomies = array( WPSSOMRP_MRP_CATEGORY_TAXONOMY );

			} else {

				$taxonomies = array();
			}

			$args = array(
				'label'               => _x( 'Return Policy', 'post type label', 'wpsso-merchant-return-policy' ),
				'labels'              => $labels,
				'description'         => _x( 'Return Policy', 'post type description', 'wpsso-merchant-return-policy' ),
				'exclude_from_search' => false,	// Must be false for get_posts() queries.
				'public'              => $is_public,
				'publicly_queryable'  => $is_public,
				'show_ui'             => true,
				'show_in_nav_menus'   => true,
				'show_in_menu'        => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => WPSSOMRP_MRP_MENU_ORDER,
				//'menu_icon'         => 'dashicons-cart',
				//'menu_icon'         => 'dashicons-store',
				'menu_icon'           => 'dashicons-tag',
				'capability_type'     => 'page',
				'hierarchical'        => false,
				'supports'            => $supports,
				'taxonomies'          => $taxonomies,
				'has_archive'         => 'orgs',
				'can_export'          => true,
				'show_in_rest'        => true,
			);

			register_post_type( WPSSOMRP_MRP_POST_TYPE, $args );
		}

		public static function register_mrp_category_taxonomy() {

			if ( ! defined( 'WPSSOMRP_MRP_CATEGORY_TAXONOMY' ) || ! WPSSOMRP_MRP_CATEGORY_TAXONOMY ) {

				return;
			}

			$is_public = false;

			$labels = array(
				'name'                       => __( 'Categories', 'wpsso-merchant-return-policy' ),
				'singular_name'              => __( 'Category', 'wpsso-merchant-return-policy' ),
				'menu_name'                  => _x( 'Categories', 'admin menu name', 'wpsso-merchant-return-policy' ),
				'all_items'                  => __( 'All Categories', 'wpsso-merchant-return-policy' ),
				'edit_item'                  => __( 'Edit Category', 'wpsso-merchant-return-policy' ),
				'view_item'                  => __( 'View Category', 'wpsso-merchant-return-policy' ),
				'update_item'                => __( 'Update Category', 'wpsso-merchant-return-policy' ),
				'add_new_item'               => __( 'Add New Category', 'wpsso-merchant-return-policy' ),
				'new_item_name'              => __( 'New Category Name', 'wpsso-merchant-return-policy' ),
				'parent_item'                => __( 'Parent Category', 'wpsso-merchant-return-policy' ),
				'parent_item_colon'          => __( 'Parent Category:', 'wpsso-merchant-return-policy' ),
				'search_items'               => __( 'Search Categories', 'wpsso-merchant-return-policy' ),
				'popular_items'              => __( 'Popular Categories', 'wpsso-merchant-return-policy' ),
				'separate_items_with_commas' => __( 'Separate categories with commas', 'wpsso-merchant-return-policy' ),
				'add_or_remove_items'        => __( 'Add or remove categories', 'wpsso-merchant-return-policy' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'wpsso-merchant-return-policy' ),
				'not_found'                  => __( 'No categories found.', 'wpsso-merchant-return-policy' ),
				'back_to_items'              => __( '← Back to categories', 'wpsso-merchant-return-policy' ),
			);

			$args = array(
				'label'              => _x( 'Categories', 'taxonomy label', 'wpsso-merchant-return-policy' ),
				'labels'             => $labels,
				'public'             => $is_public,
				'publicly_queryable' => $is_public,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'show_in_rest'       => true,
				'show_tagcloud'      => false,
				'show_in_quick_edit' => true,
				'show_admin_column'  => true,
				'description'        => _x( 'Categories for Return Policies', 'taxonomy description', 'wpsso-merchant-return-policy' ),
				'hierarchical'       => true,
			);

			register_taxonomy( WPSSOMRP_MRP_CATEGORY_TAXONOMY, array( WPSSOMRP_MRP_POST_TYPE ), $args );
		}
	}
}
