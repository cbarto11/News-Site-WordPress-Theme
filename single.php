<?php
/**
 * Displays a single page.
 *
 * @package WordPress
 * @subpackage clas-exchange
 */

//ns_print('page:single.php');
global $ns_config, $ns_template_vars;

$ns_template_vars = array();
$ns_template_vars['content-type'] = 'single';
$ns_template_vars['page-title'] = get_the_title();
$post_type = get_post_type( get_the_ID() );

$categories = ns_get_categories();
$tags = ns_get_tags();
$ns_template_vars['section'] = $ns_config->get_section( $post_type, $categories, $tags, false, array('news') );

ns_get_template_part( 'standard-template' );

