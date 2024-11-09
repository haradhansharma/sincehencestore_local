<?php
/*
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl.txt
 * Copyright 2021-2024 Jean-Sebastien Morisset (https://wpsso.com/)
 */

if ( ! defined( 'ABSPATH' ) ) {

	die( 'These aren\'t the droids you\'re looking for.' );
}

if ( ! class_exists( 'WpssoWpsmSitemapsFilters' ) ) {

	class WpssoWpsmSitemapsFilters {

		private $p;		// Wpsso class object.
		private $a;		// WpssoWpsm class object.
		private $stylesheet;	// WP_Sitemaps_Stylesheet class object.

		/*
		 * Instantiated by WpssoWpsm->init_objects().
		 */
		public function __construct( &$plugin, &$addon ) {

			static $do_once = null;

			if ( $do_once ) return;	// Stop here.

			$do_once = true;

			$this->p =& $plugin;
			$this->a =& $addon;

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( SucomUtilWP::sitemaps_disabled() ) {	// Nothing to do.

				return;
			}

			$this->stylesheet = new WP_Sitemaps_Stylesheet();

			add_filter( 'wp_sitemaps_max_urls', array( $this, 'wp_sitemaps_max_urls' ), 1000, 2 );

			add_filter( 'wp_sitemaps_post_types', array( $this, 'wp_sitemaps_post_types' ), 1000, 1 );
			add_filter( 'wp_sitemaps_posts_query_args', array( $this, 'wp_sitemaps_posts_query_args' ), 1000, 2 );
			add_filter( 'wp_sitemaps_posts_pre_url_list', array( $this, 'wp_sitemaps_posts_pre_url_list' ), 1000, 3 );
			add_filter( 'wp_sitemaps_posts_entry', array( $this, 'wp_sitemaps_posts_entry' ), 1000, 3 );

			add_filter( 'wp_sitemaps_taxonomies', array( $this, 'wp_sitemaps_taxonomies' ), 1000, 1 );
			add_filter( 'wp_sitemaps_taxonomies_query_args', array( $this, 'wp_sitemaps_taxonomies_query_args' ), 1000, 2 );
			add_filter( 'wp_sitemaps_taxonomies_entry', array( $this, 'wp_sitemaps_taxonomies_entry' ), 1000, 3 );

			add_filter( 'wp_sitemaps_users_query_args', array( $this, 'wp_sitemaps_users_query_args' ), 1000, 1 );
			add_filter( 'wp_sitemaps_users_entry', array( $this, 'wp_sitemaps_users_entry' ), 1000, 2 );

			add_filter( 'wp_sitemaps_stylesheet_content', array( $this, 'wp_sitemaps_stylesheet_content'), 1000, 1 );
		}

		/*
		 * $max_urls = The maximum number of URLs included in a sitemap.
		 *
		 * $object_type = Object type for sitemap to be filtered (e.g. 'post', 'term', 'user').
		 *
		 * See https://developer.wordpress.org/reference/hooks/wp_sitemaps_max_urls/.
		 */
		public function wp_sitemaps_max_urls( $max_urls, $object_type = 'post' ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( ! empty( $this->p->options[ 'wpsm_max_urls' ] ) ) {

				if ( is_numeric( $this->p->options[ 'wpsm_max_urls' ] ) ) {

					$max_urls = $this->p->options[ 'wpsm_max_urls' ];

					/*
					 * The WordPress default is 2000 URLs per sitemap, but Google only allows 1000 news tags in sitemaps.
					 *
					 * If news sitemaps are enabled, then limit the post sitemaps to 1000 URLs.
					 */
					if ( 'none' !== $this->p->options[ 'wpsm_news_post_type' ] ) {

						if ( $max_urls > 1000 && 'post' === $object_type ) {

							$max_urls = 1000;
						}
					}
				}
			}

			return $max_urls;
		}

		/*
		 * See wordpress/wp-includes/sitemaps/providers/class-wp-sitemaps-posts.php.
		 */
		public function wp_sitemaps_post_types( $post_types ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$post_types = SucomUtilWP::get_post_types( $output = 'objects' );

			foreach ( $post_types as $name => $obj ) {

				if ( empty( $this->p->options[ 'wpsm_sitemaps_for_' . $name ] ) ) {

					unset( $post_types[ $name ] );
				}
			}

			return $post_types;
		}

		/*
		 * Exclude posts from the sitemap that are noindex or redirected.
		 *
		 * See wordpress/wp-includes/sitemaps/providers/class-wp-sitemaps-posts.php.
		 */
		public function wp_sitemaps_posts_query_args( $args, $post_type ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log_args( array(
					'args'      => $args,
					'post_type' => $post_type,
				) );

				$this->p->debug->mark( 'sitemaps posts (' . $post_type . ') query args' );	// Begin timer.
			}

			/*
			 * The published post status for attachments is 'inherit'.
			 */
			if ( 'attachment' === $post_type ) {

				$args[ 'post_status' ] = array( 'inherit' );
			}

			static $local_cache = array();	// Create the exclusion list only once.

			if ( ! isset( $local_cache[ $post_type ] ) ) {	// Create exclusion list by post type.

				$local_cache[ $post_type ] = array();

				$exclude_args = array_merge( $args, array(	// Avoid variable name conflict with $args.
					'meta_query'     => WpssoAbstractWpMeta::get_column_meta_query_exclude(),
					'fields'         => 'ids',
					'posts_per_page' => -1,		// Get all excluded post ids.
					'nopaging'       => true,	// Get all posts.
					'paged'          => '',
					'no_found_rows'  => true,
				) );

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log_arr( 'WP_Query args', $exclude_args );
				}

				$exclude_query = new WP_Query( $exclude_args );

				$local_cache[ $post_type ] = $exclude_query->posts;

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log_arr( 'excluded post (' . $post_type . ') ids', $local_cache[ $post_type ] );
				}
			}

			if ( ! empty( $local_cache[ $post_type ] ) ) {	// Add the cached exclusion list, if we have one.

				$args[ 'post__not_in' ] = empty( $args[ 'post__not_in' ] ) ? $local_cache[ $post_type ] :
					array_merge( $args[ 'post__not_in' ], $local_cache[ $post_type ] );
			}

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark( 'sitemaps posts (' . $post_type . ') query args' );	// End timer.
			}

			return $args;
		}

		/*
		 * Since WPSSO WPSM v2.0.0.
		 *
		 * Include post type archive pages without a post ID.
		 *
		 * See wordpress/wp-includes/sitemaps/providers/class-wp-sitemaps-posts.php.
		 */
		public function wp_sitemaps_posts_pre_url_list( $url_list, $post_type, $page_num ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$args = $this->get_posts_query_args( $post_type );

			$args[ 'paged' ] = $page_num;

			$query = new WP_Query( $args );

			$url_list = array();

			if ( $post_type_archive_url = get_post_type_archive_link( $post_type ) ) {

				$sitemaps_entry = array( 'loc' => $post_type_archive_url );

				$sitemaps_entry = apply_filters( 'wp_sitemaps_posts_post_type_archive_entry', $sitemaps_entry, $post_type );

				if ( $sitemaps_entry ) {	// Just in case.

					$url_list[] = $sitemaps_entry;
				}
			}

			/*
			 * Add a URL for the homepage in the pages sitemap.
			 *
			 * Shows only on the first page if the reading settings are set to display latest posts.
			 */
			if ( 'page' === $post_type && 1 === $page_num && 'posts' === get_option( 'show_on_front' ) ) {

				/*
				 * Extract the data needed for home URL to add to the array.
				 */
				$sitemaps_entry = array( 'loc' => home_url( '/' ) );

				/*
				 * Filters the sitemaps entry for the home page when the 'show_on_front' option equals 'posts'.
				 */
				$sitemaps_entry = apply_filters( 'wp_sitemaps_posts_show_on_front_entry', $sitemaps_entry );

				if ( $sitemaps_entry ) {	// Just in case.

					$url_list[] = $sitemaps_entry;
				}
			}

			foreach ( $query->posts as $post ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'getting sitemaps entry for post id ' . ( empty( $post->ID ) ? 0 : $post->ID ) );
				}

				$sitemaps_entry = array( 'loc' => get_permalink( $post ) );

				/*
				 * Filters the sitemaps entry for an individual post.
				 */
				$sitemaps_entry = apply_filters( 'wp_sitemaps_posts_entry', $sitemaps_entry, $post, $post_type );

				if ( $sitemaps_entry ) {	// Just in case.

					$url_list[] = $sitemaps_entry;
				}
			}

			return $url_list;
		}

		/*
		 * Since WPSSO WPSM v2.0.0.
		 *
		 * Recreates the functionality of the WP_Sitemaps_Posts->get_posts_query_args() protected method.
		 *
		 * See wordpress/wp-includes/sitemaps/providers/class-wp-sitemaps-posts.php.
		 */
		public function get_posts_query_args( $post_type ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$args = apply_filters(
				'wp_sitemaps_posts_query_args',
				array(
					'orderby'                => 'ID',
					'order'                  => 'ASC',
					'post_type'              => $post_type,
					'posts_per_page'         => wp_sitemaps_get_max_urls( 'post' ),
					'post_status'            => array( 'publish' ),
					'no_found_rows'          => true,
					'update_post_term_cache' => false,
					'update_post_meta_cache' => false,
				),
				$post_type
			);

			return $args;
		}

		/*
		 * Add the modification time for Open Graph type non-website posts (ie. article, book, product, etc.), post
		 * language, and alternate post languages.
		 *
		 * See wordpress/wp-includes/sitemaps/providers/class-wp-sitemaps-posts.php.
		 */
		public function wp_sitemaps_posts_entry( $sitemaps_entry, $post, $post_type ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( empty( $post->ID ) ) {	// Just in case.

				return $sitemaps_entry;
			}

			/*
			 * Begin debug timer.
			 */
			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark( 'getting post id ' . $post->ID . ' sitemaps entry' );	// Begin timer.
			}

			$mod = $this->p->post->get_mod( $post->ID );

			/*
			 * Get modified time.
			 */
			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark( 'getting og type' );	// Begin timer.
			}

			$og_type = $this->p->og->get_mod_og_type_id( $mod );	// Since WPSSO Core v9.13.0.

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark( 'getting og type' );	// End timer.
			}

			if ( 'website' !== $og_type ) {

				if ( $mod[ 'post_modified_time' ] ) {

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'adding post modified time' );
					}

					$sitemaps_entry[ 'lastmod' ] = $mod[ 'post_modified_time' ];

				} elseif ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'no post modified time' );
				}

			} elseif ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'skipping post modified time' );
			}

			/*
			 * Get alternates.
			 */
			if ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'getting sitemaps alternates' );
			}

			$sitemaps_entry[ 'alternates' ] = $this->p->util->get_sitemaps_alternates( $mod );

			/*
			 * Get news tags.
			 *
			 * See https://developers.google.com/search/docs/crawling-indexing/sitemaps/news-sitemap.
			 */
			if ( 'none' !== $this->p->options[ 'wpsm_news_post_type' ] && $mod[ 'post_type' ] === $this->p->options[ 'wpsm_news_post_type' ] ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'getting sitemaps news' );
				}

				$news_pub_time = WPSSOWPSM_NEWS_PUB_MAX_TIME;
				$news_pub_name = WpssoWpsmSitemaps::get_news_pub_name( $mod );

				$sitemaps_entry[ 'news:news' ] = $this->p->util->get_sitemaps_news( $mod, $news_pub_time, $news_pub_name );

			} elseif ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'skipping sitemaps news' );
			}

			/*
			 * Get image tags.
			 *
			 * See https://developers.google.com/search/docs/crawling-indexing/sitemaps/image-sitemaps.
			 */
			if ( ! empty( $this->p->options[ 'wpsm_schema_images' ] ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'getting sitemaps images' );
				}

				if ( $this->p->util->robots->is_noimageindex( $mod ) ) {

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'skipping sitemaps images: noimageindex is true' );
					}

				} else {

					$sitemaps_entry[ 'image:image' ] = $this->p->util->get_sitemaps_images( $mod );
				}

			} elseif ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'skipping sitemaps images' );
			}

			/*
			 * Get video tags.
			 *
			 * See https://developers.google.com/search/docs/crawling-indexing/sitemaps/video-sitemaps.
			 */
			if ( ! empty( $this->p->options[ 'wpsm_schema_videos' ] ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'getting sitemaps videos' );
				}

				$sitemaps_entry[ 'video:video' ] = $this->p->util->get_sitemaps_videos( $mod );

			} elseif ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'skipping sitemaps videos' );
			}

			/*
			 * End debug timer.
			 */
			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark( 'getting post id ' . $post->ID . ' sitemaps entry' );	// End timer.
			}

			return $sitemaps_entry;
		}

		/*
		 * See wordpress/wp-includes/sitemaps/providers/class-wp-sitemaps-taxonomies.php.
		 */
		public function wp_sitemaps_taxonomies( $taxonomies ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$taxonomies = SucomUtilWP::get_taxonomies( $output = 'objects' );

			foreach ( $taxonomies as $name => $obj ) {

				if ( empty( $this->p->options[ 'wpsm_sitemaps_for_tax_' . $name ] ) ) {

					unset( $taxonomies[ $name ] );
				}
			}

			return $taxonomies;
		}

		/*
		 * Exclude terms from the sitemap that are noindex or redirected.
		 *
		 * See wordpress/wp-includes/sitemaps/providers/class-wp-sitemaps-taxonomies.php.
		 */
		public function wp_sitemaps_taxonomies_query_args( $args, $taxonomy ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log_args( array(
					'args'     => $args,
					'taxonomy' => $taxonomy,
				) );

				$this->p->debug->mark( 'sitemaps taxonomies (' . $taxonomy . ') query args' );	// Begin timer.
			}

			static $local_cache = array();	// Create the exclusion list only once.

			if ( ! isset( $local_cache[ $taxonomy ] ) ) {	// Create exclusion list by taxonomy.

				$local_cache[ $taxonomy ] = array();

				$exclude_args = array_merge( $args, array(	// Avoid variable name conflict with $args.
					'meta_query' => WpssoAbstractWpMeta::get_column_meta_query_exclude(),
					'fields'     => 'ids',
					'number'     => '',	// Get all excluded taxonomy ids.
					'offset'     => '',
					'count'      => false,
				) );

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log_arr( 'WP_Term_Query args', $exclude_args );
				}

				$exclude_query = new WP_Term_Query( $exclude_args );

				$local_cache[ $taxonomy ] = $exclude_query->terms;

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log_arr( 'excluded taxonomy (' . $taxonomy . ') term ids', $local_cache[ $taxonomy ] );
				}
			}

			if ( ! empty( $local_cache[ $taxonomy ] ) ) {	// Add the cached exclusion list, if we have one.

				$args[ 'exclude' ] = empty( $args[ 'exclude' ] ) ? $local_cache[ $taxonomy ] :
					array_merge( $args[ 'exclude' ], $local_cache[ $taxonomy ] );
			}

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark( 'sitemaps taxonomies (' . $taxonomy . ') query args' );	// End timer.
			}

			return $args;
		}

		/*
		 * Add the term language and alternate term languages.
		 *
		 * See wordpress/wp-includes/sitemaps/providers/class-wp-sitemaps-taxonomies.php.
		 */
		public function wp_sitemaps_taxonomies_entry( $sitemaps_entry, $term, $taxonomy ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( empty( $term->term_id ) ) {	// Just in case.

				return $sitemaps_entry;
			}

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark( 'getting term id ' . $term->term_id . ' sitemaps entry' );	// Begin timer.
			}

			$mod = $this->p->term->get_mod( $term->term_id );

			/*
			 * Get alternates.
			 */
			if ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'getting sitemaps alternates' );
			}

			$sitemaps_entry[ 'alternates' ] = $this->p->util->get_sitemaps_alternates( $mod );

			/*
			 * Get image tags.
			 *
			 * See https://developers.google.com/search/docs/crawling-indexing/sitemaps/image-sitemaps.
			 */
			if ( ! empty( $this->p->options[ 'wpsm_schema_images' ] ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'getting sitemaps images' );
				}

				if ( $this->p->util->robots->is_noimageindex( $mod ) ) {

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'skipping sitemaps images: noimageindex is true' );
					}

				} else {

					$sitemaps_entry[ 'image:image' ] = $this->p->util->get_sitemaps_images( $mod );
				}

			} elseif ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'skipping sitemaps images' );
			}

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark( 'getting term id ' . $term->term_id . ' sitemaps entry' );	// End timer.
			}

			return $sitemaps_entry;
		}

		/*
		 * Exclude users from the sitemap that are noindex or redirected.
		 *
		 * See wordpress/wp-includes/sitemaps/providers/class-wp-sitemaps-users.php
		 */
		public function wp_sitemaps_users_query_args( $args ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->log_args( array(
					'args' => $args,
				) );

				$this->p->debug->mark( 'sitemaps users query args' );	// Begin timer.
			}

			if ( empty( $this->p->options[ 'wpsm_sitemaps_for_user_page' ] ) ) {

				/*
				 * Exclude all users by including only user ID 0 (which does not exist).
				 */
				$args[ 'include' ] = array( 0 );

			} else {

				static $local_cache = null;	// Create the exclusion list only once.

				if ( null === $local_cache ) {

					$local_cache = array();

					$exclude_args = array_merge( $args, array(	// Avoid variable name conflict with $args.
						'meta_query'  => WpssoAbstractWpMeta::get_column_meta_query_exclude(),
						'fields'      => 'ID',
						'number'      => '',	// Get all excluded user ids.
						'offset'      => '',
						'paged'       => 1,
						'count_total' => false,
					) );

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log_arr( 'WP_User_Query args', $exclude_args );
					}

					$exclude_query = new WP_User_Query( $exclude_args );

					$local_cache = $exclude_query->get_results();	// Returns an array of user ids.

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log_arr( 'excluded user ids', $local_cache );
					}
				}

				if ( ! empty( $local_cache ) ) {	// Add the cached exclusion list, if we have one.

					$args[ 'exclude' ] = empty( $args[ 'exclude' ] ) ? $local_cache :
						array_merge( $args[ 'exclude' ], $local_cache );
				}
			}

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark( 'sitemaps users query args' );	// End timer.
			}

			return $args;
		}

		/*
		 * See wordpress/wp-includes/sitemaps/providers/class-wp-sitemaps-users.php.
		 */
		public function wp_sitemaps_users_entry( $sitemaps_entry, $user ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			if ( empty( $user->ID ) ) {	// Just in case.

				return $sitemaps_entry;
			}

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark( 'getting user id ' . $user->ID . ' sitemaps entry' );	// Begin timer.
			}

			$mod = $this->p->user->get_mod( $user->ID );

			/*
			 * Get image tags.
			 *
			 * See https://developers.google.com/search/docs/crawling-indexing/sitemaps/image-sitemaps.
			 */
			if ( ! empty( $this->p->options[ 'wpsm_schema_images' ] ) ) {

				if ( $this->p->debug->enabled ) {

					$this->p->debug->log( 'getting sitemaps images' );
				}

				if ( $this->p->util->robots->is_noimageindex( $mod ) ) {

					if ( $this->p->debug->enabled ) {

						$this->p->debug->log( 'skipping sitemaps images: noimageindex is true' );
					}

				} else {

					$sitemaps_entry[ 'image:image' ] = $this->p->util->get_sitemaps_images( $mod );
				}

			} elseif ( $this->p->debug->enabled ) {

				$this->p->debug->log( 'skipping sitemaps images' );
			}

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark( 'getting user id ' . $user->ID . ' sitemaps entry' );	// End timer.
			}

			return $sitemaps_entry;
		}

		/*
		 * See wordpress/wp-includes/sitemaps/class-wp-sitemaps-stylesheet.php.
		 */
		public function wp_sitemaps_stylesheet_content( $xsl_content ) {

			if ( $this->p->debug->enabled ) {

				$this->p->debug->mark();
			}

			$css           = $this->stylesheet->get_stylesheet_css();
			$title         = esc_xml( __( 'XML Sitemap' ) );
			$desc          = esc_xml( __( 'This XML Sitemap is generated by WordPress to make your content more visible for search engines.' ) );
			$plugin_name   = $this->p->cf[ 'plugin' ][ 'wpssowpsm' ][ 'name' ];
			$plugin_desc   = esc_xml( sprintf( __( 'The default XML Sitemap generated by WordPress has been extended and customized by the %s plugin.',
				'wpsso-wp-sitemaps' ), $plugin_name ) );
			$learn_more    = sprintf( '<a href="%s">%s</a>', esc_url( __( 'https://www.sitemaps.org/' ) ), esc_xml( __( 'Learn more about XML sitemaps.' ) ) );
			$number_urls   = sprintf( esc_xml( __( 'Number of URLs in this XML Sitemap: %s.' ) ), '<xsl:value-of select="count( sitemap:urlset/sitemap:url )" />' );
			$lang          = get_language_attributes( 'html' );
			$url           = esc_xml( __( 'URL', 'wpsso-wp-sitemaps' ) );
			$lastmod       = esc_xml( __( 'Last Modified', 'wpsso-wp-sitemaps' ) );
			$changefreq    = esc_xml( __( 'Change Frequency', 'wpsso-wp-sitemaps' ) );
			$priority      = esc_xml( __( 'Priority', 'wpsso-wp-sitemaps' ) );
			$alternates    = esc_xml( __( 'Alternates', 'wpsso-wp-sitemaps' ) );
			$news_sitemap  = esc_xml( __( 'News', 'wpsso-wp-sitemaps' ) );
			$image_sitemap = esc_xml( __( 'Images', 'wpsso-wp-sitemaps' ) );
			$video_sitemap = esc_xml( __( 'Videos', 'wpsso-wp-sitemaps' ) );

			$xsl_content = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
		version="1.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
        	xmlns:xhtml="http://www.w3.org/1999/xhtml"
		xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
		xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
		xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"
		exclude-result-prefixes="sitemap"
		>

	<xsl:output method="html" encoding="UTF-8" indent="yes" />

	<!--
	  Set variables for whether lastmod, changefreq or priority occur for any url in the sitemap.
	  We do this up front because it can be expensive in a large sitemap.
	  -->
	<xsl:variable name="has-lastmod"     select="count( /sitemap:urlset/sitemap:url/sitemap:lastmod )" />
	<xsl:variable name="has-changefreq"  select="count( /sitemap:urlset/sitemap:url/sitemap:changefreq )" />
	<xsl:variable name="has-priority"    select="count( /sitemap:urlset/sitemap:url/sitemap:priority )" />

	<xsl:template match="/">
		<html {$lang}>
			<head>
				<title>{$title}</title>
				<style>
					{$css}
					#sitemap__table tr th { font-weight:500; }
					#sitemap__table tr td { vertical-align:top; }
					#sitemap__table tr td ul { margin:10px 0; list-style:none; font-size:0.9em; }
					#sitemap__table tr td ul li.list-title { font-weight:500; }
					#sitemap__table tr td ul ul { margin:5px 0; }
				</style>
			</head>
			<body>
				<div id="sitemap">
					<div id="sitemap__header">
						<h1>{$title}</h1>
						<p>{$desc}</p>
						<p>{$plugin_desc}</p>
						<p>{$learn_more}</p>
					</div>
					<div id="sitemap__content">
						<p class="text">{$number_urls}</p>
                                		<xsl:apply-templates select="sitemap:urlset"/>
					</div>
				</div>
			</body>
		</html>
	</xsl:template>

    	<xsl:template match="sitemap:urlset">
		<table id="sitemap__table">
			<thead>
				<tr>
					<th class="loc">{$url}</th>
					<xsl:if test="\$has-lastmod">
						<th class="lastmod">{$lastmod}</th>
					</xsl:if>
					<xsl:if test="\$has-changefreq">
						<th class="changefreq">{$changefreq}</th>
					</xsl:if>
					<xsl:if test="\$has-priority">
						<th class="priority">{$priority}</th>
					</xsl:if>
				</tr>
			</thead>
			<tbody>
                             	<xsl:apply-templates select="sitemap:url"/>
			</tbody>
		</table>
	</xsl:template>

    	<xsl:template match="sitemap:url">
		<tr>
			<td class="loc">
				<a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc" /></a>
				<xsl:if test="xhtml:link">
					<ul class="alternates">
						<li class="list-title">{$alternates}</li>
						<ul>
                             				<xsl:apply-templates select="xhtml:link"/>
						</ul>
					</ul>
				</xsl:if>
				<xsl:if test="news:news">
					<ul class="news">
						<li class="list-title">{$news_sitemap}</li>
						<ul>
                             				<xsl:apply-templates select="news:news"/>
						</ul>
					</ul>
				</xsl:if>
				<xsl:if test="image:image">
					<ul class="images">
						<li class="list-title">{$image_sitemap}</li>
						<ul>
                             				<xsl:apply-templates select="image:image"/>
						</ul>
					</ul>
				</xsl:if>
				<xsl:if test="video:video">
					<ul class="videos">
						<li class="list-title">{$video_sitemap}</li>
						<ul>
                             				<xsl:apply-templates select="video:video"/>
						</ul>
					</ul>
				</xsl:if>
			</td>
			<xsl:if test="\$has-lastmod">
				<td class="lastmod">
					<xsl:value-of select="sitemap:lastmod" />
				</td>
			</xsl:if>
			<xsl:if test="\$has-changefreq">
				<td class="changefreq">
					<xsl:value-of select="sitemap:changefreq" />
				</td>
			</xsl:if>
			<xsl:if test="\$has-priority">
				<td class="priority">
					<xsl:value-of select="sitemap:priority" />
				</td>
			</xsl:if>
		</tr>
	</xsl:template>

	<xsl:template match="xhtml:link">
		<li>
			<xsl:variable name="altloc">
				<xsl:value-of select="@href"/>
			</xsl:variable>
			<a href="{\$altloc}"><xsl:value-of select="@href"/></a>
			<xsl:if test="@hreflang">
				[<xsl:value-of select="@hreflang"/>]
			</xsl:if>
		</li>
	</xsl:template>

	<xsl:template match="news:news">
		<xsl:apply-templates select="news:publication"/>
		<li>
			<xsl:value-of select="news:title"/> (<xsl:value-of select="news:publication_date"/>)
		</li>
	</xsl:template>

	<xsl:template match="news:publication">
		<li>
			<xsl:value-of select="news:name"/> (<xsl:value-of select="news:language"/>)
		</li>
	</xsl:template>

	<xsl:template match="image:image">
		<li>
			<xsl:variable name="image_loc">
				<xsl:value-of select="image:loc"/>
			</xsl:variable>
			<a href="{\$image_loc}"><xsl:value-of select="image:loc"/></a>
		</li>
	</xsl:template>

	<xsl:template match="video:video">
		<li>
			<xsl:variable name="video_player_loc">
				<xsl:value-of select="video:player_loc"/>
			</xsl:variable>
			<a href="{\$video_player_loc}"><xsl:value-of select="video:title"/></a>
		</li>
	</xsl:template>

</xsl:stylesheet>
EOF;

			return $xsl_content;
		}
	}
}
