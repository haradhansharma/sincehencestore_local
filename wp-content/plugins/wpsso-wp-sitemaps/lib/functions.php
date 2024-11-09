<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! function_exists( 'wpssowpsm_get_server' ) ) {

	add_action( 'init', 'wpssowpsm_get_server', -100 );

	function wpssowpsm_get_server() {

		global $wp_sitemaps;

		if ( empty( $wp_sitemaps ) ) {

			require_once WPSSOWPSM_PLUGINDIR . 'lib/sitemaps/sitemaps.php';

			if ( class_exists( 'WpssoWpsmSitemaps' ) ) {

				$wp_sitemaps = new WpssoWpsmSitemaps();

				$wp_sitemaps->init();

				do_action( 'wp_sitemaps_init', $wp_sitemaps );
			}
		}

		return $wp_sitemaps;
	}
}

if ( ! function_exists( 'wpssowpsm_wp_query_handle_sitemap' ) ) {

	add_action( 'parse_request', 'wpssowpsm_wp_query_handle_sitemap', -100, 1 );

	function wpssowpsm_wp_query_handle_sitemap( $wp ) {

		global $wp_query;

		$wp_query->is_sitemap            = empty( $wp->query_vars[ 'sitemap' ] ) ? false : true;
		$wp_query->is_sitemap_stylesheet = empty( $wp->query_vars[ 'sitemap-stylesheet' ] ) ? false : true;
	}
}

if ( ! function_exists( 'is_sitemap' ) ) {

	function is_sitemap() {

		global $wp_query;

		if ( ! isset( $wp_query ) ) {

			_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );

			return false;
		}

		return property_exists( $wp_query, 'is_sitemap' ) ? $wp_query->is_sitemap : false;
	}
}

if ( ! function_exists( 'is_sitemap_stylesheet' ) ) {

	function is_sitemap_stylesheet() {

		global $wp_query;

		if ( ! isset( $wp_query ) ) {

			_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );

			return false;
		}

		return property_exists( $wp_query, 'is_sitemap_stylesheet' ) ? $wp_query->is_sitemap_stylesheet : false;
	}
}
