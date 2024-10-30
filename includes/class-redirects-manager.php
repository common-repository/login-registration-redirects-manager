<?php

defined( 'ABSPATH' ) || exit;

use underDEV\Utils\Settings\CoreFields;
/**
 * Redirects manager
 *
 * @since      1.00
 * @author     Maxim K <woo.order.review@gmail.com>
 */
class LRR_Redirects_Manager {

    public static function init() {

        add_filter( 'login_redirect', __CLASS__.'::login_redirect__filter', 99, 3 );

        add_action('init', function () {
            LRR_Redirects_Manager::maybe_logout();

            add_filter( 'logout_redirect', __CLASS__.'::logout_redirect__filter', 99, 3 );
        });
    }

    /**
     * @param $redirect_to
     * @param $requested_redirect_to
     * @param $user
     * @return string
     */
    public static function login_redirect__filter($redirect_to, $requested_redirect_to, $user) {

        $redirect = LRR_Redirects_Manager::get_redirect('login', $user->ID);
        if ( !$redirect ) {
            return $redirect_to;
        }
        return $redirect;

    }

    /**
     * @param $redirect_to
     * @param $requested_redirect_to
     * @param $user
     * @return string
     */
    public static function logout_redirect__filter($redirect_to, $requested_redirect_to, $user) {

        $redirect = LRR_Redirects_Manager::_logout_redirect_url($user->ID);
        if ( !$redirect ) {
            return $redirect_to;
        }
        return $redirect;

    }

    /**
     * Register settings
     * @param \underDEV\Utils\Settings $settings_class
     * @throws Exception
     */
    public static function register_settings( $settings_class ) {

	    $ACTIONS_SECTION = $settings_class->add_section( __( 'Redirects', 'login-registration-logout-redirects-manager' ), 'redirects' );

        //$wp_pages_arr = self::_get_pages_arr();

        $ACTIONS_SECTION->add_group( __( 'Login redirect', 'login-registration-logout-redirects-manager' ), 'login' )
//            ->add_field( array(
//                'slug'        => 'action',
//                'name'        => __('Action after login', 'login-registration-logout-redirects-manager'),
//                'addons'      => array(
//                    'options'     => array(
//                        'none' => 'No action',
//                        'reload' => 'Reload (refresh) page',
//                        'redirect' => 'Redirect to page [PRO]',
//                    ),
//                ),
//                'default'     => 'none',
//                //'description' => __('Select an action', 'login-registration-logout-redirects-manager' ),
//                'render'      => array( new LRR_Field_Select_W_PRO(), 'input' ),
//                'sanitize'    => array( new LRR_Field_Select_W_PRO(), 'sanitize' ),
//            ) )
            ->add_field( array(
                'slug'        => 'redirect',
                'name'        => __('Redirect to (if "Redirect to page [PRO]" is selected)', 'login-registration-logout-redirects-manager'),
                'addons'      => array(
                    'hide_label' => true,
                ),
                'default'     => [],
                //'description' => __('Select an action', 'login-registration-logout-redirects-manager' ),
                'render'      => array( new LRR_Field_Redirects(), 'input' ),
                'sanitize'    => array( new LRR_Field_Redirects(), 'sanitize' ),
            ) )
        ->description('Actions with a [PRO] label will work only with a PRO version installed.');

        $ACTIONS_SECTION->add_group( __( 'Logout redirect', 'login-registration-logout-redirects-manager' ), 'logout' )
            ->add_field( array(
                'slug'        => 'action',
                'name'        => __('Action after Logout', 'login-registration-logout-redirects-manager'),
                'addons'      => array(
                    'options'     => array(
                        'none' => 'Stay on this page',
                        'home' => 'Redirect to the home page',
                        'redirect' => 'Use Redirects rules below',
                    ),
                ),
                'default'     => 'none',
                //'description' => __('Select an action', 'login-registration-logout-redirects-manager' ),
                'render'      => array( new CoreFields\Select(), 'input' ),
                'sanitize'    => array( new CoreFields\Select(), 'sanitize' ),
            ) )
            ->add_field( array(
                'slug'        => 'redirect',
                'name'        => __('Custom page redirect', 'login-registration-logout-redirects-manager'),
                'addons'      => array(
                    'per_role' => true,
                    'hide_label' => true,
                ),
                'default'     => [],
                //'description' => __('Select an action', 'login-registration-logout-redirects-manager' ),
                'render'      => array( new LRR_Field_Redirects(), 'input' ),
                'sanitize'    => array( new LRR_Field_Redirects(), 'sanitize' ),
            ) );


        $ACTIONS_SECTION->add_group( __( '[PRO - soon] After-Registration redirect', 'login-registration-logout-redirects-manager' ), 'registration' )
//            ->add_field( array(
//                'slug'        => 'action',
//                'name'        => __('Action after registration', 'login-registration-logout-redirects-manager'),
//                'addons'      => array(
//                    'options'     => array(
//                        'none' => 'No action',
//                        'auto-login' => 'Auto-login and stay on the page',
//                        'reload' => 'Reload a page and auto-login',
//                        'redirect' => 'Redirect to a page and auto-login [PRO]',
//                        'email-verification' => 'Email verification (send password to the email)',
//                        'email-verification-pro' => 'Email verification [PRO] (send a verify link)',
//                    ),
//                ),
//                'default'     => 'none',
//                'description' => __('"Email verification (send password to the email)" is not effective if user can set password during registration (in PRO)', 'login-registration-logout-redirects-manager' ),
//                'render'      => array( new LRR_Field_Select_W_PRO(), 'input' ),
//                'sanitize'    => array( new LRR_Field_Select_W_PRO(), 'sanitize' ),
//            ) )
            ->add_field( array(
                'slug'        => 'redirect',
                'name'        => __('Redirect to (only if "Redirect to a page and auto-login [PRO]" selected)', 'login-registration-logout-redirects-manager'),
                'addons'      => array(
                    //'per_role' => false,
                    'hide_label' => true,
                ),
                'default'     => [],
                //'description' => __('Select an action', 'login-registration-logout-redirects-manager' ),
                'render'      => array( new LRR_Field_Redirects(), 'input' ),
                'sanitize'    => array( new LRR_Field_Redirects(), 'sanitize' ),
            ) );

        $ACTIONS_SECTION->add_group( __( '[PRO - soon] First login (after Registration redirect)', 'login-registration-logout-redirects-manager' ), 'first_login' )
//            ->add_field( array(
//                'slug'        => 'action',
//                'name'        => __('Action after registration', 'login-registration-logout-redirects-manager'),
//                'addons'      => array(
//                    'options'     => array(
//                        'none' => 'No action',
//                        'auto-login' => 'Auto-login and stay on the page',
//                        'reload' => 'Reload a page and auto-login',
//                        'redirect' => 'Redirect to a page and auto-login [PRO]',
//                        'email-verification' => 'Email verification (send password to the email)',
//                        'email-verification-pro' => 'Email verification [PRO] (send a verify link)',
//                    ),
//                ),
//                'default'     => 'none',
//                'description' => __('"Email verification (send password to the email)" is not effective if user can set password during registration (in PRO)', 'login-registration-logout-redirects-manager' ),
//                'render'      => array( new LRR_Field_Select_W_PRO(), 'input' ),
//                'sanitize'    => array( new LRR_Field_Select_W_PRO(), 'sanitize' ),
//            ) )
            ->add_field( array(
                'slug'        => 'redirect',
                'name'        => __('Redirect to (only if "Redirect to a page and auto-login [PRO]" selected)', 'login-registration-logout-redirects-manager'),
                'addons'      => array(
                    //'per_role' => false,
                    'hide_label' => true,
                ),
                'default'     => [],
                //'description' => __('Select an action', 'login-registration-logout-redirects-manager' ),
                'render'      => array( new LRR_Field_Redirects(), 'input' ),
                'sanitize'    => array( new LRR_Field_Redirects(), 'sanitize' ),
            ) );

    }


    /**
     * @param string $action    One of: 'login', 'registration', 'logout'
     *
     * @return integer
     */
    public static function get_redirect ( $action = 'login', $user_ID )
    {
        $redirect_to = '';;

//        $needed_action = lrr_setting('redirects/' . $action . '/action');
//
//        if ( 'redirect' !== $needed_action && 'email-verification-pro' !== $needed_action ) {
//            return '';
//        }

        if ( lrr_setting('redirects/' . $action . '/redirect') ) {
            $redirect_settings = LRR_Field_Redirects::_corrected_value( lrr_setting('redirects/' . $action . '/redirect') );

            $user = get_user_by('ID', $user_ID );

            if ( is_wp_error($user) ) {
                do_action('plain_logger', 'Wrong $user object!', __FILE__);
            }

            $roles = (array) $user->roles;

            foreach ($redirect_settings['redirect'] as $redirect_key => $redirect_data) {
                if ( 'default' === $redirect_key || empty($redirect_settings['roles'][$redirect_key]) ) {
                    continue;
                }

                // Check USER Roles
                $role_match = !empty($redirect_settings['role_match'][$redirect_key]) ? $redirect_settings['role_match'][$redirect_key] : 'any_of';

                //echo PHP_EOL, PHP_EOL, "===== comapre ", $role_match, ' for roles ', implode("#" ,$redirect_settings['roles'][$redirect_key]);

                if ( 'any_of' == $role_match && array_intersect($redirect_settings['roles'][$redirect_key], $roles) ) {
                    //var_dump( array_intersect($redirect_settings['roles'][$redirect_key], $roles) );
                    $redirect_to = self::_redirect_url_from_setting( $redirect_settings, $redirect_key, $user );
                    break;
                } elseif ( 'all' == $role_match && ! array_diff($redirect_settings['roles'][$redirect_key], $roles) ) {
                    //var_dump( array_diff($redirect_settings['roles'][$redirect_key], $roles) );
                    $redirect_to = self::_redirect_url_from_setting( $redirect_settings, $redirect_key, $user );
                    break;
                }

            }

            if ( ! $redirect_to ) {
                $redirect_to = self::_redirect_url_from_setting( $redirect_settings, 'default', $user );
            }
        }

        return $redirect_to;
    }


    /**
     * @param string $redirect_settings
     * @param string $key
     * @param WP_User $user
     * @return false|string
     */
    public static function _redirect_url_from_setting( $redirect_settings, $key, $user ) {
        $redirect_to = '';

        if ( 'url' === $redirect_settings['redirect'][$key] && !empty($redirect_settings['redirect_url'][$key]) ) {
            return $redirect_settings['redirect_url'][$key];
        } elseif ( 'page' === $redirect_settings['redirect'][$key] && !empty($redirect_settings['redirect_page'][$key]) ) {
            $page_id = absint( $redirect_settings['redirect_page'][$key] );
            if ( !$page_id ) {
                return $redirect_to;
            }
            return get_permalink($page_id);
        } elseif ( 'wc_account' === $redirect_settings['redirect'][$key] && function_exists('wc_get_account_endpoint_url') ) {
            return wc_get_account_endpoint_url( 'dashboard' );
        } elseif ( 'bp_profile' === $redirect_settings['redirect'][$key] && function_exists('bp_core_get_user_domain') ) {
            return bp_core_get_user_domain( $user->ID );
        }

        return $redirect_to;

    }


    /**
     * @since 1.21
     */
    public static function maybe_logout() {
        if ( isset($_GET['lrr_logout']) && is_user_logged_in() ) {

            do_action("lrr/pre_logout");

//            if ( ! LRR_Settings::get()->setting('general_pro/redirects/silent_logout') ) {
//                add_filter('logout_url', [$this, 'logout_url__filter'], 10, 2);
//                check_admin_referer('log-out');
//            }

            $user = wp_get_current_user();

            $redirect_to = self::_logout_redirect_url($user);
            $redirect_to = apply_filters( 'logout_redirect', $redirect_to, $redirect_to, $user );

            wp_logout();

            wp_safe_redirect( $redirect_to );
            exit();
        }
    }

    /**
     * @since 1.50
     * @param $user
     * @return int|string|void
     */
    public static function _logout_redirect_url($user) {
        if ( ! empty( $_REQUEST['redirect_to'] ) ) {
            $redirect_to = $requested_redirect_to = $_REQUEST['redirect_to'];
        }
//        else {
//            $redirect_to = LRR_Settings::get()->setting('general_pro/redirects/url_after_logout');
//            $requested_redirect_to = '';
//        }

        $redirect_to = false;
        $logout_action = lrr_setting('redirects/logout/action');
        if ( 'none' === $logout_action ) {
            $redirect_to = add_query_arg( 'lrr_logout', false );
        } elseif ( 'home' === $logout_action ) {
            $redirect_to = home_url('/');
        } elseif ( 'redirect' === $logout_action ) {
            $redirect_to = LRR_Redirects_Manager::get_redirect('logout', $user->ID);
        }

        $redirect_to = add_query_arg( 'lrr_loggedout', 'true', $redirect_to );

        return $redirect_to;
    }

}
