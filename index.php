<?php
/**
 * The template for displaying all pages.
 *
 * @package WordPress
 * @subpackage news-hub-uncc
 */

//nh_print('page:index.php');
global $nh_config, $nh_template_vars;

$nh_template_vars = array();
$nh_template_vars['content-type'] = 'single';
$nh_template_vars['page-title'] = 'Index Page';
$nh_template_vars['section'] = $nh_config->get_default_section();

nh_get_template_part( 'standard-template' );

