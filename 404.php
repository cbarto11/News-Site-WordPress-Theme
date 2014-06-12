<?php
/**
 * Displays when a requested page is not found (404 error).
 *
 * @package WordPress
 * @subpackage news-hub-uncc
 */

// nh_print('PAGE:404.php');
global $nh_config, $nh_template_vars;

$nh_template_vars = array();
$nh_template_vars['content-type'] = '404';
$nh_template_vars['page-title'] = '404 Error: Page Not Found.';

$nh_template_vars['section'] = $nh_config->get_default_section();

nh_get_template_part( 'standard-template' );

