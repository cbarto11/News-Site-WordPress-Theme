<?php
/**
 * Displays the tag archive page.
 *
 * @package WordPress
 * @subpackage news-hub-uncc
 */

// nh_print( 'PAGE:tag.php' );
global $nh_config, $nh_template_vars;

$nh_template_vars = array();
$nh_template_vars['content-type'] = 'listing';
$nh_template_vars['page-title'] = single_tag_title( '', false );

$tag = get_term_by( 'title', single_tag_title('', false), 'post_tag' );
$nh_template_vars['section'] = $nh_config->get_section( 'post', array('post_tag' => $tag->slug) );

$description = tag_description( $tag->term_id );
if( !empty($description) ) $nh_template_vars['description'] = $description;

nh_get_template_part( 'standard-template' );

