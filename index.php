<?php
/**
 * The template for displaying all pages.
 *
 * @package WordPress
 * @subpackage clas-exchange
 */

ns_print('index-page');
global $ns_config, $ns_template_vars;

$ns_template_vars = array();
$ns_template_vars['content-type'] = 'single';
$ns_template_vars['page-title'] = 'Index Page';
$ns_template_vars['section'] = $ns_config->get_default_section();

ns_get_template_part( 'standard-template' );

