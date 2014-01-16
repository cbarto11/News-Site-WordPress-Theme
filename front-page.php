<?php
/**
 * Displays the front page.
 *
 * @package WordPress
 * @subpackage clas-exchange
 */

global $ns_config, $ns_template_vars;

$ns_template_vars = array();
$ns_template_vars['content-type'] = 'front-page';
$ns_template_vars['page-title'] = 'Home';
$ns_template_vars['section'] = $ns_config->get_default_section();

ns_get_template_part( 'standard-template' );

