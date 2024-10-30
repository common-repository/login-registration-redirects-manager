<?php

defined( 'ABSPATH' ) || exit;

use underDEV\Utils\Settings\CoreFields;

/**
 * Class LRR_Settings
 *
 * File is modified a bit:
 * login-registration-modal\vendor\underdev\settings\views\settings-page.php
 */
class LRR_Settings {
    protected static $instance;
    /**
     * @var \underDEV\Utils\Settings
     */
    protected $settings;
    protected $page_id = 'login-register-redirects-manager';

    public function __construct() {

        // init library with your handle
        $this->settings = new underDEV\Utils\Settings( 'lrr' );

//        require_once LRR_PATH .  "/includes/settings/class-settings-field--text.php";
//        require_once LRR_PATH .  "/includes/settings/class-settings-field--textarea.php";
//        require_once LRR_PATH .  "/includes/settings/class-settings-field--textarea-html.php";
//        require_once LRR_PATH .  "/includes/settings/class-settings-field--textarea-html-extended.php";
//        require_once LRR_PATH .  "/includes/settings/class-settings-field--editor.php";

        // register menu as always
        add_action( 'admin_menu', array( $this, 'register_menu' ) );

        // register some settings
        add_action( 'init', array( $this, 'register_settings' ) );

        add_action( 'admin_notices', array( $this, 'beg_for_review' ) );

        lrr_dismissible_notice( 'v2',
            sprintf(
                '<strong>AJAX Login & registration modal notice:</strong> you have installed version 2.0 that contains a lot of updates and tweaks. Please review your settings and reconfigure <a href="%s">after-login/registration actions</a>!',
                admin_url('options-general.php?page=login-and-register-popup&section=redirects')
            )
        );

        add_action( 'underdev/settings/enqueue_scripts', array( $this, 'settings_enqueue_scripts' ) );

        if ( isset($_GET['action']) && $_GET['action'] === 'dismiss_lrr_beg_message' ) {
            $this->dismiss_beg_message();
        }

        if ( isset($_GET['action']) && $_GET['action'] === 'lrr_reset_translations' && current_user_can('manage_options') ) {
            $this->_reset_translations();
        }

    }

    public function register_menu() {

        // pass the page hook to library to load scripts only on settings pages
        $this->settings->page_hook = add_options_page(
            'Login/Register/Logout redirects',
            'Login/Register/Logout redirects',
            'manage_options',
            $this->page_id,
            array( $this->settings, 'settings_page' )
        );

    }

    /**
     * Display notice with review beg
     * @return void
     */
    public function beg_for_review() {

        if ( ! get_option( 'lrr_beg_message' ) ) {
            echo '<div class="notice notice-info notification-notice"><p>';

            printf( __( 'Do you like "Login and Register Modal" plugin? Please consider giving it a %1$sreview%2$s', 'login-registration-logout-redirects-manager' ), '<a href="https://wordpress.org/support/plugin/login-registration-logout-redirects-manager/reviews/#new-post" class="button button-secondary" target="_blank">⭐⭐⭐⭐⭐ ', '</a>' );

            echo '<a href="' . add_query_arg( array('action'=>'dismiss_lrr_beg_message', '_wpnonce' => wp_create_nonce('lrr-beg-dismiss')) ) . '" class="dismiss-beg-message button" type="submit" style="float: right;">';
            _e( 'I already reviewed it', 'login-registration-logout-redirects-manager' );
            echo '</a>';

            echo '</p></div>';

        }
    }

    /**
     * Dismiss beg message
     * @return object       json encoded response
     */
    public function dismiss_beg_message() {

        check_admin_referer( 'lrr-beg-dismiss' );

        update_option( 'lrr_beg_message', 'dismissed' );

    }
    


    public function register_settings() {

        LRR_Redirects_Manager::register_settings($this->settings);

        $general = $this->settings->add_section( __( 'How to', 'login-registration-logout-redirects-manager' ), 'general' );

        $general->add_group( __( 'Supports', 'login-registration-logout-redirects-manager' ), 'supported_plugins' )
            ->add_field( array(
                'slug'        => 'free_version',
                'name'        => __('Free version are compatible with:', 'login-registration-logout-redirects-manager' ),
                'default'     => true,
                'render'      => array( $this, '_render__text_section' ),
                'sanitize'    => '__return_false',
                'addons' => array('section_file'=>'compatible-with', 'hide_label' => true,),
            ) );

        $general->add_group( __( 'Support', 'login-registration-logout-redirects-manager' ), 'support' )
            ->add_field( array(
                'slug'        => 'support',
                'name'        => __('If you need support:', 'login-registration-logout-redirects-manager' ),
                'default'     => true,
                'render'      => array( $this, '_render__text_section' ),
                'sanitize'    => '__return_false',
                'addons' => array('section_file'=>'support'),
            ) );


        $general->add_group( __( 'Useful plugins from the author of those plugin ☺', 'login-registration-logout-redirects-manager' ), 'useful_plugins' )
            ->add_field( array(
                'slug'        => 'useful_plugins',
                'name'        => __('How to integrate modal on your site:', 'login-registration-logout-redirects-manager' ),
                'default'     => true,
                'render'      => array( $this, '_render__text_section' ),
                'sanitize'    => '__return_false',
                'addons' => array('section_file'=>'useful_plugins','hide_label' => true,),
            ) );


        if ( !lrr_is_pro() ) {

            $MESSAGES_SECTION = $this->settings->add_section( 'GET PRO >>',  'get_a_pro', false );

            $MESSAGES_SECTION->add_group( 'Why get PRO version?', 'main' )
                             ->add_field( array(
                                 'slug'     => 'heading',
                                 'name'     => __('PRO features', 'login-registration-logout-redirects-manager' ),
                                 'render'   => array( $this, '_render__text_section' ),
                                 'sanitize' => '__return_false',
                                 'addons' => array('section_file'=>'go-to-pro'),
                             ) );

        }

        do_action('lrr/register_settings', $this->settings);

    }


    /**
     * @param underDEV\Utils\Settings\Field     $field
     *
     * @since 1.11
     */
    public function _render__text_section( $field ) {
        if ( $section_file = $field->addon('section_file') ) {
            include LRR_PATH . "/views/admin/settings-section/{$section_file}.php";
        }
    }

    public function settings_enqueue_scripts() {
        wp_enqueue_script( 'lrr-admin', LRR_URL . 'assets/lrr-admin.js', array( 'jquery', 'jquery-ui-sortable' ), LRR_VERSION, true );
	    wp_localize_script('lrr-admin', 'LRR_ADMIN', array(
	    	'ajax_url' => admin_url('admin-ajax.php'),
	    ));

        wp_enqueue_style('lrr-admin-css', LRR_URL . '/assets/lrr-core-settings.css', false, LRR_ASSETS_VER);
    }

    /**
     * Get all settings
     * @uses   underDEV\SettingsAPI Settings API class
     * @return array settings
     */
    public function settings() {

        return $this->settings->get_settings();

    }

    /**
     * Get single setting value
     * @uses   SettingsAPI Settings API class
     * @param  string $setting_slug setting section/group/field separated with /
     * @param  bool do_stripslashes
     * @return mixed           field value or null if name not found
     */
    public function setting($setting_slug, $do_stripslashes = false) {

        $setting_path = explode('/', $setting_slug);

//        $value = $this->_get_maybe_wpml_translated_string($setting_slug, $setting_path[0]);
//
//        if ( null !== $value ) {
//            return stripslashes($value);
//        }

        $value = $this->settings->get_setting( $setting_slug );
        // IF Value is empty and it's message string - try to get translated


        if ( $setting_path[0] == 'messages' || $setting_path[0] == 'mails' ) {

            $value = stripslashes($value);
        }

//
//        if ( is_admin() && !$value && $setting_path[0] == 'messages' ){
//
//            // SKIP if we on Default language
//            global $sitepress;
//
//            $current_language           = $sitepress->get_current_language();
//            //var_dump( $current_language );
//            $current_language_code = $sitepress->get_locale_from_language_code( $current_language );
//
//            switch_to_locale( $current_language_code );
//
//            var_dump($current_language_code);
//
//            $fields = $this->get_section_settings_fields('messages');
//
//            $default_value = $fields[$setting_slug]->default_value();
//            if ($default_value) {
//                $__value = __($default_value, 'login-registration-logout-redirects-manager');
//                if ($default_value !== $__value) {
//                    $value = $__value;
//                }
//            }
//
//        }


        if (!$value && $setting_path[0] == 'messages' && defined("LRR/SETTINGS/TRY_GET_TRANSLATED")) {
            $fields = $this->get_section_settings_fields('messages');

            $default_value = $fields[$setting_slug]->default_value();
            if ($default_value) {
                $__value = __($default_value, 'login-registration-logout-redirects-manager');
                if ($default_value !== $__value) {
                    $value = $__value;
                }
            }

        }

        //restore_previous_locale();

        return $do_stripslashes ? stripslashes( $value ) : $value;

    }

    /**
     * Update single setting value
     * @uses   SettingsAPI Settings API class
     *
     * @param  string $setting_slug setting section/group/field separated with /
     * @param  mixed $new_value
     *
     * @return bool
     * @throws Exception
     * @since 1.51
     */
    public function update_setting($setting_slug, $new_value)
    {

        $setting_path = explode('/', $setting_slug);

        if ( count($setting_path) !== 3 ) {
            throw new Exception('Invalid $setting_slug: ' . $setting_slug);
        }

        $res = update_option( 'lrr_' . $setting_path[0] . '[' . $setting_path[1] . ']', $new_value );

//        var_dump( 'lrr_' . $setting_path[0] );
//        var_dump( $new_value );
//        var_dump( get_option('lrr_' . $setting_path[0]  ) );

        return  $res;
    }

    /**
     * Get translated option value (string)
     * If enabled WPML - then try return translated
     *
     * @param string $setting_slug
     * @param $section_slug
     *
     * @return string
     * @since 1.33
     */
    protected function _get_maybe_wpml_translated_string($setting_slug, $section_slug) {

        // && isset($this->wpml_labels[$key])
        if ( class_exists('SitePress') ) {

            // SKIP if we on Default language
            global $sitepress;

            $current_language = $sitepress->get_current_language();
            $default_language = $sitepress->get_default_language();

            /**
             * Switch Language for AJAX
             * @since 1.33
             */
            if ( defined("LRR_IS_AJAX") ) {
                /**
                 * @var WPML_Language_Resolution $wpml_language_resolution
                 */

                global $wpml_language_resolution;

                if ($current_language != $wpml_language_resolution->get_referrer_language_code()) {
                    $sitepress->switch_lang($wpml_language_resolution->get_referrer_language_code());
                    $current_language = $sitepress->get_current_language();
                }
            }

            if ( $default_language == $current_language ) {
                return null;
            }

            $fields = $this->get_section_settings_fields($section_slug);
            /**
             * @see https://wpml.org/wpml-hook/wpml_translate_single_string/
             * @since 1.29
             */
            return apply_filters( 'wpml_translate_single_string', $fields[$setting_slug]->default_value(), 'AJAX Login & Registration modal', $fields[$setting_slug]->name(). ' [' . $fields[$setting_slug]->group() . '/' .$fields[$setting_slug]->slug() . ']' );
            //return wpml_register_single_string('AJAX Login & Registration modal', $fields[$setting_slug]->name(). ' [' . $fields[$setting_slug]->group() . '/' .$fields[$setting_slug]->slug() . ']', $fields[$setting_slug]->default_value());
        }
        return null;
    }

    /**
     * Add strings to WPML strings translator
     *
     * @since 1.33
     */
    protected function register_wpml_strings() {

        do_action( 'wpml_multilingual_options', 'lrr_messages' );
        do_action( 'wpml_multilingual_options', 'lrr_mails' );
        do_action( 'wpml_multilingual_options', 'lrr_messages_pro' );


        // && function_exists('icl_register_string')
        if ( class_exists('SitePress')  ) {

            //switch_to_locale( 'en_US' );


//            $messages = $this->get_section_settings_fields('messages');
//            $mails = $this->get_section_settings_fields('mails');
//
//            $all = $messages + $mails;
//
//            if ( lrr_is_pro() ) {
//                $messages_pro = $this->get_section_settings_fields('messages_pro');
//                $all = $all + $messages_pro;
//            }
//
//            foreach ($all as $key => $field) {
//                /**
//                 * @see https://wpml.org/wpml-hook/wpml_register_single_string
//                 * @since 1.29
//                 */
//                    do_action( 'wpml_register_single_string', 'AJAX Login & Registration modal', $field->name(). ' [' . $field->group() . '/' .$field->slug() . ']', $field->default_value() );
//                // icl_register_string is deprecated
//                //icl_register_string( 'AJAX Login & Registration modal', $field->name(). ' [' . $field->group() . '/' .$field->slug() . ']', $field->default_value() );
//            }

            //restore_previous_locale();
        }
    }


    /**
     * Get all fields from section
     *
     * @param string $section_slug
     *
     * @since 1.24
     *
     * @return \underDEV\Utils\Settings\Field[]
     */
    public function get_section_settings_fields( $section_slug ) {

        $fields = array();

        $section = $this->settings->get_section( $section_slug );

        foreach ( $section->get_groups() as $group_slug => $group ) {

            foreach ( $group->get_fields() as $field_slug => $field ) {
                $fields[ $section_slug . '/' . $group_slug . '/' . $field_slug ] = $field;
            }
        }

        return $fields;
    }

    /**
     * Get all fields from section
     *
     * @param string $section_slug
     *
     * @since 1.24
     *
     * @return \underDEV\Utils\Settings\Field[]
     */
    public function get_sections(  ) {
        return $this->settings->get_sections(  );
    }

    private function _reset_translations() {
        delete_option( "lrr_messages" );
        echo "Reset done!";
        die();
    }

    /**
     * @return LRR_Settings
     */
    public static function get(){
        if ( !self::$instance ) {
            self::$instance = new LRR_Settings();
        }

        return self::$instance;
    }

}