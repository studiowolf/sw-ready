<?php

/**
 * Navigation Page Model
 *
 * @package     SW Ready
 * @subpackage  Navigation Page Model
 * @copyright   Studio Wolf
 * @license     Studio Wolf
 * @since       1.0
 *
 * @todo implement long title
 * @todo Use cookies to see from which cat the user came, see get_parent function
 * @todo get children of this page
 * @todo Replace default page-attributes: http://wordpress.stackexchange.com/questions/44966/how-can-i-add-extra-attribute-in-the-page-attribute-section-in-wp-admin-for-pa
 */


class SWNavigationPage
{
    protected $_post;
    protected $_is_included_parent;


    /**
     * Constructing a NavigationPage
     *
     * @since   1.0
     * @param   $post the post object related to this NavigationPage
     */

    function __construct($post)
    {

        $this->_post = $post;
        $this->_is_included_parent = false;
    }


    /**
     * Get the id of this object
     *
     * @return int ID
     * @since  1.0
     */

    public function get_id()
    {
        if($this->_post) {
            return $this->_post->ID;
        } else {
            return false;
        }
    }


    protected $_parents;

    /**
     * Get the line of parents of this object
     *
     * @return array parrents
     * @since  1.0
     */

    public function get_parents()
    {
        if (!$this->_parents) {
            $parents = array();

            // If the this page has a parent, add it to the chain
            if($parent = $this->get_parent()) {
                $parents = $parent->get_parents();
            }

            // Add the current object to the chain
            $parents[] = $this;

            $this->_parents = $parents;
        }
        return $this->_parents;
    }


    protected $_parent;

    /**
     * Get the parent of this object
     *
     * @return object the parent
     * @since  1.0
     */

    public function get_parent()
    {
        if(isset($this->_post) && !$this->_parent && $this->_post->post_parent) {
            $parent = get_page($this->_post->post_parent);
        } elseif(!$this->is_page()) {

            // If there the current object isn't a page, then the parent is
            // stored in the settings. Array key: post type name, Array value:
            // page id of parent
            $post_parents = array();
            $term_parents = array();
            $user_parent = false;

            // Apply filter to hook to get the settings
            $term_parents = apply_filters('sw_term_parents', $term_parents);
            $post_parents = apply_filters('sw_post_parents', $post_parents);
            $user_parent = apply_filters('sw_user_parent', $user_parent);
            $use_taxonomies = false;

            // We need to take care of categories in the parent
            if(function_exists('sw_get_term_reference') && isset($this->_post) && array_key_exists($this->get_post_type(), $term_parents)
                && !is_archive()
                && $term_ids = $this->get_term_ids()) {

                // Let's find a reference for the first term
                if($term_reference = sw_get_term_reference($term_ids[0])) {
                    // We can make use of taxonomies
                    $use_taxonomies = true;
                }
            }

            if($use_taxonomies && array_key_exists($term_reference, $term_parents[$this->get_post_type()])) {
                // Search for the taxonomy parent of the first taxonomy id
                $parent = sw_get_page_by_reference($term_parents[$this->get_post_type()][$term_reference]);

                // @TODO: Use cookies to see from which cat the user came
            } elseif(is_author() && $user_parent) {
                // If page is an user page, find the user page parent
                if(!function_exists('sw_get_page_by_reference')) {
                    $parent = get_page($user_parent);
                } else {
                    $parent = sw_get_page_by_reference($user_parent);
                }

            } elseif(array_key_exists($this->get_post_type(), $post_parents)) {

                // Search for the post type parent id, check if it is a reference or ID
                $reference = $post_parents[$this->get_post_type()];

                // Check if the sw_get_page_by_reference() function exists to couple to sw_acm plugin
                if(!function_exists('sw_get_page_by_reference')) {
                    $parent = get_page($reference);
                } else {
                    $parent = sw_get_page_by_reference($reference);
                }
            }
        }

        // Check if parent is set. The parent is always a page
        if(isset($parent)) {
            $page = $parent;

            //Check if the page is indeed a page and is published
            if($page->post_type == 'page' && $page->post_status == 'publish') {

                // Everything ok, then create new NavigationPage Object
                $this->_parent = new SWNavigationPage($page);
                return $this->_parent;
            }
        }
        return $this->_parent;
    }


    protected $_children;

    /**
     * Fetch and store children
     *
     * @return array of children pages
     * @since  1.0
     */

    public function get_children()
    {
        if(!isset($this->_children)) {

            $children = get_children(array(
                'post_parent' => $this->get_id(),
                'post_type' => 'page',
                'post_status' => 'publish',
                'orderby' => 'menu_order',
                'order' => 'ASC'
            ));

            if(!empty($children)) {

                $new_children = array();
                foreach($children as $child) {
                    $new_children[] = new SWNavigationPage($child);
                }
                $this->_children = $new_children;

            } else {
                $this->_children = false;
            }
        }

        return $this->_children;
    }


    /**
     * Check if this object has children
     *
     * @return boolean true if yes, no if false
     * @since  1.0
     */

    public function has_children()
    {
        if($this->get_children()) {
            return true;
        } else {
            return false;
        }
    }


    protected $_is_active;

    /**
     * Check if this page is active in any navigation at this moment
     *
     * @param  object $parent the parent object
     * @return boolean yes if active, no if not
     * @since  1.0
     */

    public function is_active($parent = null)
    {
        global $sw_page;

        if($this->get_id() == $sw_page->get_id()) {
            return true;
        }

        // Return flase if pages are search or 404
        if(is_404() || is_search()) return;

        // Only look if item is active if the item is not a parent
        if (!$this->_is_included_parent) {

            // Look at all the parents of the current page to see if a navigation item matches
            foreach($sw_page->get_parents() as $parent) {
                if ($this->get_id() == $parent->get_id()) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * Get the navigation title of this item
     *
     * @return string the title
     * @since  1.0
     */

    public function get_navigation_title()
    {
        return $this->_post->post_title;
    }


    /**
     * Get the headline of this item
     *
     * @return string headline
     * @since  1.0
     */

    public function get_headline()
    {
        // Check if the SW Content Management plugin is activated
        if(function_exists("register_field_group")) {
            return sw_get_headline($this->get_id());
        } else {
            return $this->get_navigation_title();
        }
    }


    /**
     * Display navigation title when item is printed
     *
     * @return string [description]
     * @since  1.0
     */

    public function __toString()
    {
        return $this->get_navigation_title();
    }


    /**
     * Check if this item is an included parent in the navigation
     *
     * @return boolean true is yes, false if no
     * @since  1.0
     */

    public function is_included_parent()
    {
        $this->_is_included_parent = true;
    }


    /**
     * Check if this NavigationObject is a page
     *
     * @return boolean true if yes, no if false
     * @since  1.0
     */

    private function is_page()
    {
        if(isset($this->_post) && $this->_post->post_type == 'page') {
            return true;
        }
        return false;
    }


    /**
     * Get the post type of this item
     *
     * @return string post type
     * @since  1.0
     */

    private function get_post_type()
    {
        return get_post_type($this->_post);
    }


    /**
     * Check the term_ids this item is related with
     *
     * @return array of texonomy terms
     *
     */

    private function get_term_ids()
    {
        // Find the post type taxonomies
        $taxonomies = get_object_taxonomies($this->get_post_type());

        // Find the terms matching the post
        $taxonomy_terms = wp_get_post_terms($this->get_id(), $taxonomies, array('fields' => 'ids'));

        // Return the combined categories
        return $taxonomy_terms;
    }
}