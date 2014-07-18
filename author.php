<?php
/**
 * Displays the author page.
 *
 * @package WordPress
 * @subpackage news-hub-uncc
 */

// nh_print( 'PAGE:author.php' );
global $wp_query, $nh_config, $nh_template_vars;

$nh_template_vars = array();
$nh_template_vars['content-type'] = 'author';
$nh_template_vars['section'] = $nh_config->get_default_section();

$nh_template_vars['page-title'] = get_the_author_meta( 'display_name' );
$nh_template_vars['listing-name'] = 'AUTHOR';

nh_get_template_part( 'standard-template' );

