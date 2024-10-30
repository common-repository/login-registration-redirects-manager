<?php

defined( 'ABSPATH' ) || exit;

/**
 * Class LRR_Core
 */
class LRR_Core {

    protected static $instance;

    public function __construct()
    {

        $this->load_plugin_textdomain();

        WP_Admin_Dismissible_Notice::get();
        LRR_Settings::get();
        LRR_Redirects_Manager::init();

        //add_action('init', array('LRR_Updater', 'init'));
        //add_action( 'template_redirect', array($this, 'template_redirect'), 99 );


        if ( !defined("LRR_IN_BUILD_FREE") ) {
            add_filter('plugin_action_links_' . LRR_BASENAME, array($this, 'add_settings_link'));
        }

    }


    /**
     * Add settings link to plugin list table
     *
     * @param  array $links Existing links
     * @return array        Modified links
     */
    public function add_settings_link($links)
    {
        $settings_link = sprintf('<a href="admin.php?page=login-register-redirects-manager">%s</a>', __('Settings', 'lrr'));
        array_push($links, $settings_link);
        return $links;
    }

    /**
     * Define the locale for this plugin for internationalization.
     * Do not loaded by default because used https://translate.wordpress.org/
     * https://translate.wordpress.org/projects/wp-plugins/login-registration-logout-redirects-manager
     *
     * @since    1.02
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            //'lrr', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/'
            'login-registration-logout-redirects-manager', false, dirname( LRR_BASENAME ) . '/languages/'
        );
    }

    /**
     * Call PRO function
     *
     * @param string    $function
     * @return mixed
     */
    public function call_pro( $function, $param1 = false ) {
        if ( class_exists('LRR_Pro') ) {
            return LRR_Pro::get()->$function($param1);
        }
    }

    /**
     * @return LRR_Core
     */
    public static function get(){
        if ( ! isset( self::$instance ) ) {
            return self::$instance = new self();
        }

        return self::$instance;
    }
}