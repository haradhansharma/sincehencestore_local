<?php

// enqueue parent styles

function ns_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

add_action( 'wp_enqueue_scripts', 'ns_enqueue_styles' );

function trim_product_titles( $title, $id ) {
    if ( 
        ( is_shop() || 
        is_product_tag() || 
        is_product_category() || 
        is_front_page() || 
        is_home() || 
        is_singular('sincehence-suppliments') || 
        // is_product() ||
        is_tax( 'brand', 'sincehence-suppliments' ) || 
        is_tax('pa_brand') || 
        is_cart() || // Cart page
        is_checkout() ) // Checkout page
        && get_post_type( $id ) === 'product' 
    ) {
        
            $char_limit = 25;
            $trimmed_title = mb_strimwidth( $title, 0, $char_limit, '...' );
            return $trimmed_title;
            
       
        

    } else {
        return $title;
    }
}



add_filter( 'the_title', 'trim_product_titles', 10, 2 );

// function trim_product_titles( $title, $id ) {
//     if ( ( is_shop() || is_product_tag() || is_product_category() || is_front_page() || is_home() || is_singular('sincehence-suppliments') || is_tax( 'brand', 'sincehence-suppliments' ) || is_tax('pa_brand') || is_product() || is_cart() || is_checkout() ) && get_post_type( $id ) === 'product' ) {
//         // $word_limit = 4; // Change this to your desired number of words
//         $char_limit = 25;
//         // $trimmed_title = wp_trim_words( $title, $word_limit, '...' );
//         $trimmed_title = mb_strimwidth( $title, 0, $char_limit, '...' );
//         return $trimmed_title;
//     } else {
//         return $title;
//     }
// }


add_filter( 'get_the_excerpt', 'strip_and_trim_product_excerpt', 10, 2 );

function strip_and_trim_product_excerpt( $excerpt, $post ) {
    // Check if the post type is 'product' and if it's not a single product page
    if ( ( is_shop() || is_product_tag() || is_product_category() || is_front_page() || is_home() || is_singular('sincehence-suppliments') || is_tax( 'brand', 'sincehence-suppliments' ) || is_tax('pa_brand') ) && get_post_type( $post ) === 'product' && !is_singular('product') ) {
        $char_limit = 130; // Change this to your desired number of words
        $stripped_excerpt = strip_tags( $excerpt ); // Strip HTML tags
        // $trimmed_excerpt = wp_trim_words( $stripped_excerpt, $word_limit, '...' ); // Trim the excerpt
        $trimmed_excerpt = mb_strimwidth( $stripped_excerpt, 0, $char_limit, '...' );
        return $trimmed_excerpt;
    } else {
        return $excerpt;
    }
}





