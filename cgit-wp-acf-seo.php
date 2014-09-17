<?php

/*

Plugin Name: Castlegate IT WP ACF SEO
Plugin URI: http://github.com/castlegateit/cgit-wp-acf-seo
Description: Simple SEO fields for titles, headings, and descriptions.
Version: 1.0
Author: Castlegate IT
Author URI: http://www.castlegateit.co.uk/
License: MIT

*/

/**
 * Check for ACF
 */
if ( ! function_exists('get_field') ) {
    return;
}

/**
 * Include pre-defined fields
 */
require_once dirname( __FILE__ ) . '/acf.php';

/**
 * Edit title
 */
function cgit_seo_title ($title) {

    global $post;

    if ( isset($post) && get_field('seo_title', $post->ID) ) {
        $title = get_field('seo_title', $post->ID);
    }

    return $title;

}

add_filter('wp_title', 'cgit_seo_title', 999);

/**
 * Add description
 */
function cgit_seo_description () {

    global $post;

    if ( isset($post) && get_field('seo_description', $post->ID) ) {
        $description = get_field('seo_description', $post->ID);
        echo "<meta name='description' content='$description' />\n";
    }

}

add_action('wp_head', 'cgit_seo_description', 0);
