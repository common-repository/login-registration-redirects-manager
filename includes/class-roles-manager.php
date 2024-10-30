<?php

defined( 'ABSPATH' ) || exit;

/**
 * Actions/Redirects manager
 *
 * @since      1.00
 * @author     Maxim K <woo.order.review@gmail.com>
 */
class LRR_Roles_Manager {

    public static function get_wp_roles_flat() {

    	require_once ABSPATH . 'wp-admin/includes/user.php';

        $editable_roles = get_editable_roles();
        $roles = [];
        foreach ($editable_roles as $role => $details) {
            $roles[ $role ] = translate_user_role($details['name']);
        }

        return $roles;

    }

    public static function get_wp_caps_flat() {

    	require_once ABSPATH . 'wp-admin/includes/user.php';

        $editable_roles = get_editable_roles();
        $roles = [];
        foreach ($editable_roles as $role => $details) {
            $roles[ $role ] = translate_user_role($details['name']);
        }

        return $roles;

    }

}
