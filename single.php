<?php
/**
 * Displays a single page.
 *
 * @package WordPress
 * @subpackage news-hub-uncc
 */

//nh_print('page:single.php');
global $nh_config, $nh_template_vars;

$nh_template_vars = array();
$nh_template_vars['content-type'] = 'single';
$nh_template_vars['page-title'] = get_the_title();
$post_type = get_post_type( get_the_ID() );

$categories = nh_get_categories();
$tags = nh_get_tags();
$nh_template_vars['section'] = $nh_config->get_section( $post_type, $categories, $tags, false, array('news') );

nh_get_template_part( 'standard-template' );

