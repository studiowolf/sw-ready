<?php

/**
 *  SW Editing controller
 *
 *  @package     SW Ready
 *  @subpackage  SW Editing controller
 *  @copyright   Studio Wolf
 *  @license     Studio Wolf
 *  @since       1.0
 */

class SWEditing
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
    }


    /**
     * Hook filter for custom TinyMCE toolbar
     *
     * @since  1.0
     */

    function admin_init ()
    {
        add_filter('tiny_mce_before_init', array($this, 'tiny_mce_before_init'));
    }


    /**
     * Finetune the TinyMCE toolbar
     *
     * @param  array $settings the current TinyMCE settings
     * @return array the new TinyMCE settings
     * @since  1.0
     */

    function tiny_mce_before_init($settings)
    {
    	// Format to show in the dropdown

        $settings['block_formats'] = 'Paragraph=p;Header 2=h2;Header 3=h3';

    	// Disable certain menu items
        $settings['toolbar1'] = 'formatselect,bold,italic,bullist,numlist,blockquote,hr,link,unlink,pastetext,removeformat,charmap,undo,redo,wp_help';
    	$settings['toolbar2'] = '';

    	return $settings;
    }
}