<?php
/*
    Plugin Name: bbPress - WPMktgEngine | Genoo Extension
    Description: Genoo, LLC
    Author:  Genoo, LLC
    Author URI: http://www.genoo.com/
    Author Email: info@genoo.com
    Version: 1.0.12
    License: GPLv2
*/
/*
    Copyright 2016  WPMKTENGINE, LLC  (web : http://www.genoo.com/)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * On activation
 */

register_activation_hook(__FILE__, function(){
    // Basic extension data
    $fileFolder = basename(dirname(__FILE__));
    $file = basename(__FILE__);
    $filePlugin = $fileFolder . DIRECTORY_SEPARATOR . $file;
    // Activate?
    $activate = FALSE;
    $isGenoo = FALSE;
    // Get api / repo
    if(class_exists('\WPME\ApiFactory') && class_exists('\WPME\RepositorySettingsFactory')){
        $activate = TRUE;
        $repo = new \WPME\RepositorySettingsFactory();
        $api = new \WPME\ApiFactory($repo);
        if(class_exists('\Genoo\Api')){
            $isGenoo = TRUE;
        }
    } elseif(class_exists('\Genoo\Api') && class_exists('\Genoo\RepositorySettings')){
        $activate = TRUE;
        $repo = new \Genoo\RepositorySettings();
        $api = new \Genoo\Api($repo);
        $isGenoo = TRUE;
    } elseif(class_exists('\WPMKTENGINE\Api') && class_exists('\WPMKTENGINE\RepositorySettings')){
        $activate = TRUE;
        $repo = new \WPMKTENGINE\RepositorySettings();
        $api = new \WPMKTENGINE\Api($repo);
    }
    // 1. First protectoin, no WPME or Genoo plugin
    if($activate == FALSE){
        genoo_wpme_deactivate_plugin(
            $filePlugin,
            'This extension requires Wpmktgengine or Genoo plugin to work with.'
        );
    } else {
        // Right on, let's run the tests etc.
        // 2. Second test, can we activate this extension?
        // Active
        $active = get_option('wpmktengine_extension_forums', NULL);
        if($isGenoo === TRUE){
            $active = TRUE;
            if($active === NULL){
                update_option('wpmktengine_extension_forums', $active, TRUE);
            }
        }
        if($active === NULL){
            // Oh oh, no value, lets add one
            try {
                // Might be older package
                if(method_exists($api, 'getPackageForums')){
                    $active = $api->getPackageForums();
                } else {
                    $active = FALSE;
                }
            } catch (\Exception $e){
                $active = FALSE;
            }
            // Save new value
            update_option('wpmktengine_extension_forums', $active, TRUE);
        }
        // 3. Check if we can activate the plugin after all
        if($active == FALSE){
            genoo_wpme_deactivate_plugin(
                $filePlugin,
                'This extension is not allowed as part of your package.'
            );
        } else {
            // 4. After all we can activate, that's great, lets add those calls
            try {
                $api->setStreamTypes(
                    array(
                        array(
                            'name' => 'started discussion',
                            'description' => ''
                        ),
                        array(
                            'name' => 'replied in discussion',
                            'description' => ''
                        ),
                        array(
                            'name' => 'started forum',
                            'description' => ''
                        ),
                    )
                );
            } catch(\Exception $e){
                // Decide later
            }
        }
    }
});


/**
 * WPMKTENGINE Extension
 */

add_action('wpmktengine_init', function($repositarySettings, $api, $cache){

    /**
     * Started Discussion (name of topic - name of Forum)
     */

    add_action('bbp_new_topic', function($topic_id, $forum_id, $anonymous_data, $topic_author) use ($api){
        // Get user
        $user = wp_get_current_user();
        $topic = get_post($topic_id);
        $forum = get_post($forum_id);
        $api->putActivityByMail($user->user_email, 'started discussion', '' . $topic->post_title . ' - '. $forum->post_title . '', '', get_permalink($topic->ID));
    }, 10, 4);

    /**
     * Replied in Discussion (name of topic - name of Forum)
     */

    add_action('bbp_new_reply', function($reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author, $something, $reply_to) use ($api){
        // Get user
        $user = wp_get_current_user();
        $topic = get_post($topic_id);
        $forum = get_post($forum_id);
        $api->putActivityByMail($user->user_email, 'replied in discussion', '' . $topic->post_title . ' - '. $forum->post_title . '', '', get_permalink($topic->ID));
    }, 10, 7);


    /**
     * New Forum
     */

    add_action('bbp_new_forum', function($args) use ($api){
        // Do we have data?
        if(is_array($args)){
            if(
                array_key_exists('forum_id', $args)
                &&
                array_key_exists('forum_author', $args)
                &&
                array_key_exists('post_parent', $args)
            ){
                // We have the data, let's get more info and place the activity there
                $user = get_user_by('id', $args['forum_author']);
                $topic = get_post($args['post_parent']);
                $forum = get_post($args['forum_id']);
                // Put the activity in, if we have all
                if(!empty($user) && !empty($topic) && !empty($forum)){
                    $api->putActivityByMail($user->user_email, 'started forum', '' . $forum->post_title . ' - '. $topic->post_title . '', '', get_permalink($forum->ID));
                }
            }
        }
    }, 10, 1);

}, 10, 3);


/**
 * Genoo / WPME deactivation function
 */
if(!function_exists('genoo_wpme_deactivate_plugin')){

    /**
     * @param $file
     * @param $message
     * @param string $recover
     */

    function genoo_wpme_deactivate_plugin($file, $message, $recover = '')
    {
        // Require files
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        // Deactivate plugin
        deactivate_plugins($file);
        unset($_GET['activate']);
        // Recover link
        if(empty($recover)){
            $recover = '</p><p><a href="'. admin_url('plugins.php') .'">&laquo; ' . __('Back to plugins.', 'wpmktengine') . '</a>';
        }
        // Die with a message
        wp_die($message . $recover);
        exit();
    }
}
