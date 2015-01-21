<?php

/**
 *  SW Branding
 *
 *  @package     SW Ready
 *  @subpackage  SW Branding
 *  @copyright   Studio Wolf
 *  @license     Studio Wolf
 *  @since       2.0.0
 */

class SWBranding {

    var $parent;


    /**
     * Constructor
     *
     * @since  2.0.0
     */

    function __construct($parent)
    {
        $this->parent = $parent;

        // Handle login screen
        global $pagenow;
        if($pagenow == 'wp-login.php') {
            add_action('init', array($this, 'init'));
        }
    }


    /**
     * Load settings and init hooks for login-page
     *
     * @since  2.0.0
     */

    function init()
    {
        // Add hooks if needed
        if($this->parent->settings['stylesheet_url']) {
            add_action('style_loader_tag', '__return_false');
            add_action('login_enqueue_scripts', array($this, 'login_enqueue_scripts'));
        }
        if($this->parent->settings['header_url']) {
            add_filter('login_headerurl', array($this, 'login_headerurl'));
        }
    }


    /**
     * Change the logo URL
     *
     * @since  2.0.0
     */

    function login_headerurl()
    {
        return $this->parent->settings['header_url'];
    }


    /**
     * Add the new stylesheet
     *
     * @since  2.0.0
     */

    function login_enqueue_scripts()
    {
        echo '<link rel="stylesheet" id="sw-login-css"  href="' . $this->parent->settings['stylesheet_url'] . '" type="text/css" media="all" />';
    }
}