<?php

/**
 * API functions
 *
 * @package     SW Ready
 * @subpackage  API functions
 * @copyright   Studio Wolf
 * @license     Studio Wolf
 * @since       1.0
 *
 * @todo        Automatically detect different video URLS (youtube, vimeo, etc)
 * @todo        Improved breadcrumbs
*/


/**
 * Get the navigation pages on a specific level
 *
 * @param  int $level the depth level from the top
 * @param  boolean $include_parent include the paretn of the navigation items as first item
 * @return array of page navigation object
 * @since  1.0
 */

function sw_get_navigation($level = null, $include_parent = false)
{
    global $post;
    global $sw_page;

    // Create a SWNavigationPage object from the current post and set it as
    // global if not yet created
    if(!isset($sw_page)) {
        $sw_page = new SWNavigationPage($post);
    }

    // If a page and a level is given, create navigational on the right level
    if($level) {
        $page_parents = $sw_page->get_parents();
        if(isset($page_parents[$level-1])) {
            // store parent for further processing
            $parent = $page_parents[$level-1];
        } else {
            // No navigation at this level, return false
            return false;
        }
    }

    $navigation_items = array();

    // If there is a parent and include_parent is true, then include the parent first
    if($include_parent && isset($parent)) {

        // Set this PageNavigation as the included parent
        $parent->is_included_parent();
        $navigation_items[] = $parent;
    }

    if(isset($parent)) {

        // Get pages based on the parent
        $posts = get_pages(array(
            'post_type' => 'page',
            'parent' => $parent->get_id(),
            'child_of' => $parent->get_id(),
            'sort_column' => 'menu_order',
            'hierarchical' => 0,
            'meta_key' => 'show_in_navigation',
            'meta_value' => true,
        ));
    } else {
        $posts = array();
        // Create the root menu
        $posts = get_pages(array(
            'post_type' => 'page',
            'parent' => null,
            'child_of' => null,
            'sort_column' => 'menu_order',
            'hierarchical' => 0,
            'meta_key' => 'show_in_navigation',
            'meta_value' => true,
        ));
    }

    foreach($posts as $sw_post) {
        $navigation_items[] = new SWNavigationPage($sw_post);
    }

    return $navigation_items;
}


/**
 * Get breadcrumbs
 *
 * @param  boolean $include_home include home in the breadcrumbs
 * @return array of NavigationItems for use in the breadcrumbs
 * @since  1.0
 */

function sw_get_breadcrumbs($include_home = true)
{
    global $post;
    global $sw_page;

    // Create a SWNavigationPage object from the current post and set it as
    // global if not yet created
    if(!isset($sw_page)) $sw_page = new SWNavigationPage($post);

    // See OLE breadcrumbs if we need breadcrumbs for archives

    $parents = $sw_page->get_parents();

    // Get the home page from WP options
    if($include_home && $home_page_id = get_option('page_on_front')) {
        $home_page = new SWNavigationPage(get_post($home_page_id));
        array_unshift($parents, $home_page);
    }

    return $parents;
}


/**
 * Get post parent
 * This function fetches the current posts parent
 *
 * @return post object of the current posts parent
 * @since  1.2.6
 */

function sw_get_post_parent() {
    global $post;
    global $sw_page;

    // Create a SWNavigationPage object from the current post and set it as
    // global if not yet created
    if(!isset($sw_page)) $sw_page = new SWNavigationPage($post);

    $parent = $sw_page->get_parent();

    return $parent;
}


/**
 * Create an image tag from an ACF object
 *
 * @param  int $id attachement ID or array $id ACF object array
 * @param  string $size the defined size of the image
 * @return string image tag
 * @since  1.0
 */

function sw_get_image_tag($id, $size)
{
    if(is_numeric($id)) {
        $image_url = sw_get_image_url($id, $size);
        $alt = get_post_meta($id, '_wp_attachment_image_alt', true);
        return '<img src="'.$image_url.'" alt="'. $alt .'" />';
    }

    // DEPRECATED, but array are still supported for now
    if(is_array($id)) {
        if($src = $id['sizes'][$size]) {
            $alt = $id['alt'];
            return '<img src="'.$src.'" alt="'. $alt .'" />';
        }
    }

    return false;
}


/**
 * Create an image url from an ACF object
 *
 * @param  int $id attachement ID or array $id ACF object array
 * @param  string $size the defined size of the image
 * @return string image url
 * @since  1.0
 */

function sw_get_image_url($id, $size)
{
    if(is_numeric($id)) {
        $src = wp_get_attachment_image_src($id, $size);
        return $src[0];
    }

    // DEPRECATED, but arrays are still supported
    if($src = $id['sizes'][$size]) {
        return $src;
    } else {
        if($src = $sizes[$size]) {
            return $src;
        }
    }
    return false;
}


/**
 * Create an embed tag from an ACF object
 *
 * @param  array $object ACF object
 * @return embed code
 * @since  1.2.5
 */

function sw_get_embed_tag($object)
{
    $tag = false;

    // Check if object has an url
    if($url = $object['url']) {
        $tag = wp_oembed_get($url);
    }

    return $tag;
}


/**
 * Create an file tag from an ACF object
 *
 * @param  array $object ACF file object
 * @param  string caption for the file
 * @return string file tag
 * @since  1.0
 */

 function sw_get_file_tag($id, $caption = false)
 {
     if(is_numeric($id)) {
         // Chekck if the attachement exists
         if($url = wp_get_attachment_url($id)) {

             $title = get_the_title($id);
             // if(!$caption) {
             //     $caption = $id['caption'];
             // }
             return '<a href="'. $url .'" rel="external" />'. $title .'</a>';
         }
     }

     return false;
 }


/**
 * Create an file url from an ACF object
 *
 * @param  array $object ACF file object
 * @return string file url
 * @since  1.0
 */

function sw_get_file_url($object)
{
    if(!is_array($object)) {
        // Fetch object
        $url = wp_get_attachment_url($object);
        return $url;
    } elseif($url = $object['url']) {
        return $url;
    }
    return false;
}


/**
 * Add an image placeholder for a certain dimension
 * @param  string $name name of the defined image size
 * @param  string $src the source of the placeholder file
 * @since  1.0
 * @todo   revise the placeholder structure
 */

function sw_add_image_size_placeholder($name, $src)
{
    SWBasic::$image_placeholders[$name] = $src;
}


/**
 * Display paginaton for every possible situation
 * Arguments are the same as WP function paginate_link()
 *
 * @param  WP_Query object $query the query to paginate on
 * @return string pagination structure
 * @since  1.1
 */

function sw_paginate($query = false, $args = false)
{
    if(!$query) {
        global $wp_query;
        $query = $wp_query;
    }

    $big = 999999999; // need an unlikely integer
    $default_args = array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'end_size' => 2,
        'current' => max(1, get_query_var('paged')),
        'total' => $query->max_num_pages,
        'type' => 'list',
        'prev_text' => 'Vorige',
        'next_text' => 'Volgende',
        'list_class' => false,
        'current_class' => false
    );

    $args = wp_parse_args($args, $default_args);
    $paginate_links = paginate_links($args);

    // Change list class
    if($args['list_class'] && $args['type'] == 'list') {
        $paginate_links = str_replace('page-numbers', $args['list_class'], $paginate_links);
    }

    // Change current class
    if($args['current_class']) {
        $paginate_links = str_replace('current', $args['current_class'], $paginate_links);
    }

    return $paginate_links;
}



/* DEPRECATED API FUNCTIONS */

/**
 * Create an video tag from an ACF object
 *
 * @param  array $object ACF video object
 * @return string video tag
 * @todo  dertmine the video service autimatically
 * @since  1.0
 */

function sw_get_video_tag($object)
{
    // Check if object has an url
    if($url = $object['url']) {

        // Now we only support youtube, so create the correct embed url
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=embed/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\‌​n]+#", $url, $matches);

        // Return false if it is an invalid URL
        if(count($matches) > 0) {
            $embed_url = "http://www.youtube.com/embed/" . $matches[0] . "?rel=0&amp;wmode=transparent&amp;feature&amp;showinfo=0";
            return '<iframe src="' . $embed_url . '" frameborder="0" allowfullscreen></iframe>';
        }
    }
    return false;
}


/**
 * Create an image tag from an ACF object
 *
 * @param  array $object ACF image object
 * @param  string $size the defined size of the image
 * @return string image tag
 * @since  1.0
 */

function sw_image_tag($object, $size)
{
    return sw_get_image_tag($object, $size);
}


/**
 * Create an image url from an ACF object
 *
 * @param  array $object ACF image object
 * @param  string $size the defined size of the image
 * @return string image url
 * @since  1.0
 */

function sw_image_url($object, $size)
{
    return sw_get_image_url($object, $size);
}
