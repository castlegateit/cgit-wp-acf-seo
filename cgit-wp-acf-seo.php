<?php

/*

Plugin Name: Castlegate IT WP ACF SEO
Plugin URI: http://github.com/castlegateit/cgit-wp-acf-seo
Description: Simple SEO fields for titles, headings, and descriptions.
Version: 2.0
Author: Castlegate IT
Author URI: http://www.castlegateit.co.uk/
License: MIT

*/

/**
 * This plugin requires ACF
 */
if (!function_exists('acf_add_local_field_group')) {
    return;
}

/**
 * Add SEO fields
 */
acf_add_local_field_group(array(
    'key' => 'cgit_wp_seo',
    'title' => 'SEO',
    'fields' => array(
        array(
            'key' => 'seo_title',
            'name' => 'seo_title',
            'label' => 'Title',
            'type' => 'text',
        ),
        array(
            'key' => 'seo_heading',
            'name' => 'seo_heading',
            'label' => 'Heading',
            'type' => 'text',
        ),
        array(
            'key' => 'seo_description',
            'name' => 'seo_description',
            'label' => 'Description',
            'type' => 'text',
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'post_type',
                'operator' => '!=',
                'value' => '0', // Show on all posts
            ),
        ),
    ),
    'position' => 'side',
));

/**
 * Add entry in user guide
 */
function cgit_seo_user_guide($sections) {
    $file = dirname(__FILE__) . '/user-guide.php';
    $sections['cgit-wp-acf-seo'] = Cgit\UserGuide::getFile($file);

    return $sections;
}

add_filter('cgit_user_guide_sections', 'cgit_seo_user_guide', 100);

/**
 * Is this a post (or page)?
 */
function cgit_seo_is_post() {
    return is_single() || is_page();
}

/**
 * Edit title
 */
function cgit_seo_title ($title) {
    if (!cgit_seo_is_post()) {
        return $title;
    }

    if (get_field('seo_title')) {
        $title = get_field('seo_title');
    }

    return $title;
}

add_filter('wp_title', 'cgit_seo_title', 999);

/**
 * Add description
 */
function cgit_seo_description () {
    if (!cgit_seo_is_post()) {
        return;
    }

    if (get_field('seo_description')) {
        echo '<meta name="description" content="'
            . get_field('seo_description') . '" />';
    }
}

add_action('wp_head', 'cgit_seo_description', 0);

/**
 * Generate heading
 *
 * This is a utility function for use in your theme. Because the heading could
 * appear anywhere on the page, the heading is not added to the site
 * automatically.
 */
function cgit_seo_heading($sep = ': ') {
    $heading = get_bloginfo('name');

    if (cgit_seo_is_post()) {
        $heading .= $sep . get_the_title();

        if (get_field('seo_heading')) {
            $heading = get_field('seo_heading');
        }
    }

    return $heading;
}
