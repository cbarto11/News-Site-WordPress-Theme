<?php
/**
 * Displays the search results.
 *
 * @package WordPress
 * @subpackage clas-exchange
 */

//ns_print('page:search.php');
global $ns_config, $ns_template_vars;

$ns_template_vars = array();
$ns_template_vars['content-type'] = 'search';
$ns_template_vars['page-title'] = get_search_query();
$ns_template_vars['section'] = $ns_config->get_default_section();

$ns_template_vars['listing-name'] = 'Full-Text Search';

ns_get_template_part( 'standard-template' );

