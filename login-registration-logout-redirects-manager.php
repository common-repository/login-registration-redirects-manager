<?php
/**
	Plugin Name:    Login Registration Redirects Manager
	Plugin URI:     https://maxim-kaminsky.com/shop/product/login-registration-logout-redirects-manager-pro/
	Description:    Easy to set-up Login, Logout and Registration (soon) manager
	Version:        1.00
	Author URI:     http://maxim-kaminsky.com/
	Author:         Maxim K
	Text Domain:    login-registration-redirects-manager
	Domain Path:    /languages
*/

// If this file is called directly, abort.
if (!class_exists('WP')) {
	die();
}

if ( $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) {
	die( 'Access denied.' );
}

// Stop IF Pro version exists > 1.50 with the in-build Free version
if ( class_exists('LRR_Pro') && defined("LRR_URL") && ! defined("LRR_ALWAYS_LOAD_FREE") ) {
    return;
}

if ( !defined("LRR_IN_BUILD_FREE") ) {
    define("LRR_URL", plugin_dir_url(__FILE__));
    define("LRR_ASSETS_URL", LRR_URL . '/assets/');

    define("LRR_PATH", plugin_dir_path(__FILE__));
    define("LRR_BASENAME", plugin_basename(__FILE__));
}

define("LRR_VERSION", '1.00');

define("LRR_ASSETS_VER", 1);

//define("LRR/SETTINGS/TRY_GET_TRANSLATED", 1);

require_once( LRR_PATH . 'includes/helpers.php' );
require_once( LRR_PATH . 'vendor/autoload.php' );

//require_once LRR_PATH . '/includes/class-core.php';

add_action('plugins_loaded', array('LRR_Core', 'get'), 11);