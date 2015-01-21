<?php

/**
 *  SW Site controller
 *
 *  @package     SW Ready
 *  @subpackage  SW Site controller
 *  @copyright   Studio Wolf
 *  @license     Studio Wolf
 *  @since       1.0
 */

class SWSite
{
    var $parent;


    /**
     * Constructor
     *
     * Set hooks
     *
     * @since  1.0
     * @param  $parent SWReady object
     */

    function __construct($parent)
    {
        $this->parent = $parent;

        // Remove comment feed and remove admin bar on website
        add_filter('post_comments_feed_link', '__return_false');
        add_filter('show_admin_bar', '__return_false');

        if(SW_BASIC_ENABLE_COMMENTS) {
            add_filter('get_avatar', array($this, 'get_avatar'));
            add_action('preprocess_comment', array($this, 'preprocess_comment'));
        }
    }


    /**
     * Regenerate avatar without unnecesary attributes
     *
     * @param  string $avatar the avatar tag
     * @return string the stripped avatar tag
     * @since  1.0
     */

    function get_avatar($avatar)
    {
        $result = array();

        // Only match the src-attribute
        preg_match( '/src=\'(.*?)\'/i', $avatar, $result ) ;
        // Create and return new image element
        return("<img src='".$result[1]."'/>");
    }


    /**
     * Filter comments from spam, spam element must be emtpy
     * Ignores if no spam field is available
     *
     * @param  string $comment the comment
     * @return string the comment
     * @since  1.0
     */

    function preprocess_comment($comment)
    {
        $spam = $_POST['spam'];

        // Variable spam should not be set. If set, spam is active.
        if(!isset($spam) || $spam != "") {
            wp_die("You're spam!");
        }
        return $comment;
    }
}

/**
 * These functions are not yet implemented, only if you use wp_head() in frontend, otherwise not needed
 *
 * Remove the wlwmanifest_link (Windows Live Writer)
 * //remove_action('wp_head', 'wlwmanifest_link');
 *
 * Remove WP Version information
 * //remove_action('wp_head', 'wp_generator');
 *
 * Remove the RSD header link
 * //remove_action('wp_head', 'rsd_link');
 *
 * Remove prev and next links from the <head>
 * //remove_action('wp_head', 'start_post_rel_link', 10, 0 );
 * //remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
 */