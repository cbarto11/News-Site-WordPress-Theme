<?php
/**
 * Displays a single page.
 *
 * @package WordPress
 * @subpackage clas-exchange
 */

global $ns_config, $ns_template_vars;

$ns_template_vars = array();
$ns_template_vars['content-type'] = 'single';
$ns_template_vars['page-title'] = get_the_title();
$post_type = get_post_type( get_the_ID() );

$categories = get_the_category();
$category = array();
if( $categories )
{
	foreach( $categories as $c ) $category[] = $c->slug;
}

$tags = get_the_tags();
$tag = array();
if( $tags )
{
	foreach( $tags as $t ) $tag[] = $t->slug;
}

$ns_template_vars['section'] = $ns_config->get_section( $post_type, $category, $tag, false, array('news') );

ns_get_template_part( 'standard-template' );

