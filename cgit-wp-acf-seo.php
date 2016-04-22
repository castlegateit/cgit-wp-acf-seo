<?php

/*

Plugin Name: Castlegate IT WP ACF SEO
Plugin URI: http://github.com/castlegateit/cgit-wp-acf-seo
Description: Simple SEO fields for titles, headings, and descriptions.
Version: 3.0
Author: Castlegate IT
Author URI: http://www.castlegateit.co.uk/
License: MIT

*/

use Cgit\AcfSeo;

// Path to this file
define('CGIT_ACF_SEO_FILE', __FILE__);
define('CGIT_ACF_SEO_URL', plugin_dir_url(__FILE__));

// Load plugin
require_once __DIR__ . '/src/autoload.php';
require_once __DIR__ . '/functions.php';

// Initialization
add_action('init', function() {
    AcfSeo::getInstance();
});
