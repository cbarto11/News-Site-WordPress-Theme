<?php
/**
 * Displays the search results.
 *
 * @package WordPress
 * @subpackage news-hub-uncc
 */

//nh_print('page:search.php');
global $nh_config, $nh_template_vars;

$nh_template_vars = array();
$nh_template_vars['content-type'] = 'search';
$nh_template_vars['page-title'] = get_search_query();
$nh_template_vars['section'] = $nh_config->get_default_section();

$nh_template_vars['listing-name'] = 'Full-Text Search';

nh_get_template_part( 'standard-template' );

