<?php
/*
Plugin Name: SW Ready
Plugin URI: http://www.studiowolf.nl/
Description: Basic set of plugins to set Studio Wolf branding and adapted Wordpress behaviour.
Version: 2.0.2
Author: Studio Wolf
Author URI: http://www.studiowolf.nl/
License: For unlimited single website usage, do not copy
Copyright: Studio Wolf (hallo@studiwolf.com)
*/


class SWBasic
{
    var $plugin_path;
    var $template_path;
    var $plugin_url;

    var $settings;


    /**
     * Constructor
     *
     * @since  1.0
     */

    function __construct()
    {
        $this->plugin_path = plugin_dir_path( __FILE__ );
        $this->template_path = $this->plugin_path . "templates/";
        $this->plugin_url = plugins_url('',__FILE__);

        $this->default_settings();

        global $pagenow;

        // Handle admin and login screens
        if(is_admin() || $pagenow == 'wp-login.php') {

            // Load admin controllers
            require_once('controllers/admin.php'); new SWAdmin($this);
            require_once('controllers/editing.php'); new SWEditing($this);

            // Set default settings
            $this->settings = array(
                'branding' => array(
                    'company_name' => 'Studio Wolf', // Company name
                    'company_url' => 'http://www.studiowolf.nl/', // Company url
                    'company_phone' => '050 8200 271', // Company phone
                    'company_email' => 'hallo@studiowolf.nl', // Company email
                    'contact_name' => 'Tim Sluis', // Contact name
                    'contact_phone' => false, // Contact phone
                    'contact_email' => 'tim@studiowolf.nl' // Contact email
                ),
                'stylesheet_url' => $this->plugin_url . '/css/login.css', // False for no style
                'header_url' => 'http://www.studiowolf.nl/' // False for default
            );

            add_action('init', array($this, 'init'));
        } else {

            // Load site models and controllers
            require_once('controllers/site.php'); new SWSite($this);
            require_once('models/navigation-page.php');
        }

        require_once('controllers/rewrite.php'); new SWRewrite($this);
    }


    /**
     * Set the functions default settings
     *
     * @since  1.0
     */

    function default_settings()
    {
        // Options to enable the comment system
        if (!defined('SW_BASIC_ENABLE_COMMENTS')) {
            define('SW_BASIC_ENABLE_COMMENTS', false);
        }

        // Options to enable the links section (gone in WP 3.5)
        if (!defined('SW_BASIC_ENABLE_LINKS')) {
            define('SW_BASIC_ENABLE_LINKS', false);
        }

        // Options to enable Wordpress news on the dashboard
        if (!defined('SW_BASIC_ENABLE_DASHBOARD_NEWS')) {
            define('SW_BASIC_ENABLE_DASHBOARD_NEWS', false);
        }
    }


    /**
     * Load settings
     *
     * @since  1.2
     */

    function init()
    {
        // setup defaults
        $this->settings = apply_filters('sw_ready_settings', $this->settings);

        global $pagenow;

        if($pagenow == 'wp-login.php') {
            // Add hooks if needed
            if($this->settings['stylesheet_url']) {
                add_action('style_loader_tag', '__return_false');
                add_action('login_enqueue_scripts', array($this, 'login_enqueue_scripts'));
            }
            if($this->settings['header_url']) {
                add_filter('login_headerurl', array($this, 'login_headerurl'));
            }
        }
    }


    /**
     * Change the logo URL
     *
     * @since  2.0.0
     */

    function login_headerurl()
    {
        return $this->settings['header_url'];
    }


    /**
     * Add the new stylesheet
     *
     * @since  2.0.0
     */

    function login_enqueue_scripts()
    {
        echo '<link rel="stylesheet" id="sw-login-css"  href="' . $this->settings['stylesheet_url'] . '" type="text/css" media="all" />';
    }
}
new SWBasic();

// Load api
require_once('api.php');