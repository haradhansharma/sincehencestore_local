<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_separate', trailingslashit( get_stylesheet_directory_uri() ) . 'ctc-style.css', array( 'storefront-gutenberg-blocks','storefront-style','storefront-style','storefront-icons','storefront-woocommerce-style' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 30 );

// END ENQUEUE PARENT ACTION


// Your code to enqueue parent theme styles
// function enqueue_parent_styles() {
//    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
// }
// add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );



// Enqueue the custom parallax JavaScript
function enqueue_sincehence_script() {
    wp_enqueue_script(
        'sincehence-script', // Handle for the script
        get_stylesheet_directory_uri() . '/sincehence.js', // Path to the script file
        array(), // Dependencies, leave empty if none
        null, // Version number, leave null for no version
        true // Load in footer
    );
}
add_action( 'wp_enqueue_scripts', 'enqueue_sincehence_script' );

function sincehence_storefront_credit() {
    ?>
    <div class="site-info">
        <!-- Custom footer content goes here -->
        <p>&copy; <?php echo date( 'Y' ); ?> <?php echo get_bloginfo( 'name' ); ?>. All rights reserved.</p>
    </div><!-- .site-info -->
    <?php
}

// Unhook parent theme's footer credit and add a custom one
function replace_storefront_credit() {
    // Remove the original storefront credit
    remove_action( 'storefront_footer', 'storefront_credit', 20 );
    
    // Add the custom footer credit function
    add_action( 'storefront_footer', 'sincehence_storefront_credit', 20 );
}
add_action( 'wp', 'replace_storefront_credit' );




remove_action( 'storefront_loop_post', 'storefront_post_content', 30 );

if ( ! function_exists( 'storefront_post_content' ) ) {
    /**
     * Display the post excerpt with a link to the single post
     */
    function storefront_post_content() {
        ?>
        <div class="entry-content">
        <?php

        /**
         * Functions hooked in to storefront_post_content_before action.
         *
         * @hooked storefront_post_thumbnail - 10
         */
        do_action( 'storefront_post_content_before' );

        if ( is_home() || is_archive() ) {
            the_excerpt();
            echo '<a href="' . get_permalink() . '" class="read-more-link">Read More</a>';
        } else {
            the_content();
        }

        do_action( 'storefront_post_content_after' );

        ?>
        </div><!-- .entry-content -->
        <?php
    }
}

