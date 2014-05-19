<?php
/**
 * The template for displaying all pages.
 *
 * @package WordPress
 * @subpackage news-hub-uncc
 */

//nh_print('PAGE:page.php');
global $nh_config, $nh_template_vars;

$nh_template_vars = array();
$nh_template_vars['content-type'] = 'single';
$nh_template_vars['page-title'] = get_the_title();

$post_type = get_post_type();
$taxonomies = nh_get_taxonomies();
$nh_template_vars['section'] = $nh_config->get_section( $post_type, $taxonomies, false, array('news') );

nh_get_template_part( 'standard-template' );

