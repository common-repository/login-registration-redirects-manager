<?php

defined( 'ABSPATH' ) || exit;

/**
 * Pages management - get url, create, etc
 *
 * @since      1.00
 * @author     Maxim K <woo.order.review@gmail.com>
 */
class LRR_Pages_Manager {

    /**
     * @param bool $cached
     * @return array
     */
    public static function _get_pages_arr( $cached = true ) {

        if ( $cached && $pages_list = wp_cache_get( 'lrr_pages_list', 'lrr' ) ) {
            return $pages_list;
        }

        $pages_list = array();
        $post_title = '';


        $args = array(
            'post_type' => 'page',
            'suppress_filters' => false,
            'post_status' => 'publish',
            'perm' => 'readable',
            'posts_per_page' => 500,
            //'fields' => 'ids',
        );

        $query = new WP_Query($args);

        foreach ($query->posts as $page) {
            $post_title = $page->post_title;
            if ( 'publish' != $page->post_status ) {
                $post_title .= ' [' . $page->post_status . ']';
            }
            $pages_list[(string)$page->ID] = $post_title . ' [#' . $page->ID . ']';
        }



//        global $wpdb;
//
//        $pages = $wpdb->get_results(
//            "SELECT `ID`,`post_title`,`post_status` FROM `{$wpdb->posts}`  WHERE (`post_type` = 'page' AND `post_status` IN ('publish', 'private', 'draft')) ORDER BY `ID` DESC LIMIT 0, 500;"
//        );
//
//        $pages_list = array();
//        $post_title = '';
//        foreach ( $pages as $page ) {
//            $post_title = $page->post_title;
//            if ( 'publish' != $page->post_status ) {
//                $post_title .= ' [' . $page->post_status . ']';
//            }
//            $pages_list[(string)$page->ID] = $post_title . ' [#' . $page->ID . ']';
//        }

        if ( $cached ) {
            wp_cache_add( 'lrr_pages_list', $pages_list, 'lrr' );
        }

        return $pages_list;
    }

}
