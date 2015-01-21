<?php

/**
 *  SW Admin controller
 *
 *  @package     SW Ready
 *  @subpackage  SW Admin controller
 *  @copyright   Studio Wolf
 *  @license     Studio Wolf
 *  @since       1.0
 */

class SWAdmin
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

        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'admin_menu'));
    }


    /**
     * Trigger when admin is loaded
     *
     * @since  1.0
     */

    function admin_init()
    {
        // Disable WP-admin access if a user is a subscriber
        if (!current_user_can('edit_posts') && !isset($_SERVER['DOING_AJAX']) && home_url() . $_SERVER['PHP_SELF'] != admin_url('admin-ajax.php')) {
            wp_logout();
            wp_redirect(wp_login_url());
            exit;
        }

        // Remove comment meta boxes if comments are disabled
        if(!SW_BASIC_ENABLE_COMMENTS) {
            remove_meta_box('commentsdiv','post','normal');
            remove_meta_box('commentstatusdiv','post','normal');
            // Remove comments column from pages
            add_filter('manage_pages_columns', array($this, 'manage_pages_columns'));

        } else {
            remove_all_filters('get_avatar');
        }

        // Remove custom field meta boxes, we use ACF
        remove_meta_box('postcustom','post','normal');

        // Remove page meta boxes
        remove_meta_box('commentsdiv','page','normal');
        remove_meta_box('commentstatusdiv','page','normal');
        remove_meta_box('postcustom','page','normal');
        remove_meta_box('authordiv','page','normal');

        // Remove options on the user profile page
        remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');

        // Trigger other admin only hooks
        add_action('wp_before_admin_bar_render', array($this, 'wp_before_admin_bar_render'));
        add_action('wp_dashboard_setup',  array($this, 'wp_dashboard_setup'));
        add_action('admin_head', array($this, 'admin_head'));

        if($this->parent->settings['branding']['company_name']) {
            add_filter('admin_footer_text', array($this, 'admin_footer_text'));
        }

        // Remove update notice if user is not an administrator
        if (!current_user_can('administrator')) {
            add_action('init', create_function('$a', "remove_action( 'init', 'wp_version_check' );"), 2);
            add_filter('pre_option_update_core', create_function( '$a', "return null;"));
        }
    }


    /**
     * Remove admin menu items
     *
     * @since  1.0
     */

    function admin_menu ()
    {
        global $menu;
        $restricted = array();

        // Menu items to be removed
        if(!SW_BASIC_ENABLE_COMMENTS) $restricted[] = __('Comments');
        if(!SW_BASIC_ENABLE_LINKS) $restricted[] = __('Links');
        //$restricted[] = __('Extra');

        end ($menu);
        while (prev($menu)) {
            $value = explode(' ', $menu[key($menu)][0]);
            if(in_array($value[0] != NULL?$value[0]:"" , $restricted)) {
                unset($menu[key($menu)]);
            }
        }
    }


    /**
     * Customize the Wordpress dasboard
     *
     * @since  1.0
     * @todo   Remove specific branding and build hook for different brands
     */

    function wp_dashboard_setup()
    {
        global $wp_meta_boxes;

        // Remove widgets from home screen
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
        if(!SW_BASIC_ENABLE_DASHBOARD_NEWS) {
           unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
           unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
        }
        if(!SW_BASIC_ENABLE_COMMENTS) {
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
        }

        // Add branding widget, if company name is given
        if($this->parent->settings['branding']['company_name']) {
            wp_add_dashboard_widget('brand_widget', 'Informatie over dit systeem', array($this, 'brand_widget'));
        }
    }


    /**
     * Building and displaying the brand widget
     *
     * @since  1.0
     */

    function brand_widget()
    {
        $settings = $this->parent->settings;

        $contact = array(
            'name' => $settings['branding']['contact_name'],
            'phone' => $settings['branding']['contact_phone'],
            'email' => $settings['branding']['contact_email']
        );
        $company = array(
            'name' => $settings['branding']['company_name'],
            'phone' => $settings['branding']['company_phone'],
            'email' => $settings['branding']['company_email'],
            'url' => $settings['branding']['company_url']
        );

        // Load template
        set_query_var('contact', $contact);
        set_query_var('company', $company);
        load_template($this->parent->template_path . "/brand_widget.php");
    }


    /**
     * Remove the tabs from the admin screen
     *
     * @since  1.0
     */

    function admin_head()
    {
        $screen = get_current_screen();
        $screen->remove_help_tabs();
    }


    /**
     * Remove items from the admin bar
     *
     * @since  1.0
     *
     */

    function wp_before_admin_bar_render()
    {
        global $wp_admin_bar;

        $wp_admin_bar->remove_menu('wp-logo');
        $wp_admin_bar->remove_menu('new-content');
        $wp_admin_bar->remove_menu('comments');
    }


    /**
     * Display branding in the footer
     *
     * @since  1.0
     */

    function admin_footer_text()
    {
        $settings = $this->parent->settings;
        $company = array(
            'name' => $settings['branding']['company_name'],
            'phone' => $settings['branding']['company_phone'],
            'email' => $settings['branding']['company_email'],
            'url' => $settings['branding']['company_url']
        );

        set_query_var('company', $company);
        load_template($this->parent->template_path . "/admin_footer_text.php");
    }


    /**
     * Removed the comments column from pages
     *
     * @since  1.2.4
     */

    function manage_pages_columns($defaults) {
        unset($defaults['comments']);
        return $defaults;
    }
}