<?php
/**
 * Displays the front page.
 *
 * @package WordPress
 * @subpackage news-hub-uncc
 */

// nh_print('PAGE:front-page.php');
global $nh_config, $nh_template_vars;

$nh_template_vars = array();
$nh_template_vars['content-type'] = 'front-page';
$nh_template_vars['page-title'] = 'Home';
$nh_template_vars['section'] = $nh_config->get_default_section();

nh_get_template_part( 'standard-template' );

