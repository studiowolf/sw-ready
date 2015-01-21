<?php

/**
 *  SW Rewrite controller
 *
 *  @package     SW Ready
 *  @subpackage  SW Rewrite controller
 *  @copyright   Studio Wolf
 *  @license     Studio Wolf
 *  @since       1.0
 *
 */

class SWRewrite {

    var $parent;
    private $page_rewrite_rules;


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

        add_filter('page_rewrite_rules', array($this, 'page_rewrite_rules'), 9999);
        add_filter('rewrite_rules_array', array($this, 'rewrite_rules_array'), 9999);
        add_action('init', array($this, 'init'));
    }


    /**
     * Set page rewrite rules to verbose
     *
     * This is needed because we set the page rules on top. Because every URL
     * machtes to the page rewrite rule, verbose checks if the page really
     * exists. If not, then other rewrite rules will be tested. It takes an
     * extra query, but it assures that pages always work, if created under a
     * post type permastruct.
     *
     * @since  1.1.1
     */

    function init()
    {
        $GLOBALS['wp_rewrite']->use_verbose_page_rules = true;
    }


    /**
     * Collect the page rewrite rules and return an empty array, so we can put
     * then on top later
     *
     * @param  array $page_rewrite_rules the page post type rewrite rules
     * @return array the page post type rewrite rules
     * @since  1.0
     */

    function page_rewrite_rules($page_rewrite_rules)
    {
        $this->page_rewrite_rules = $page_rewrite_rules;
        return array();
    }


    /**
     * Prepend page rewrite rules before others
     *
     * @param  array $rewrite_rules all rewrite rules
     * @return array page prepended rewrite rules
     * @since  1.0
     */

    function rewrite_rules_array($rewrite_rules)
    {
        return array_merge($this->page_rewrite_rules, $rewrite_rules);

    }
}