<?php
/**
 * Displays the Event custom post type archive page.
 *
 * @package WordPress
 * @subpackage news-hub-uncc
 */

//nh_print('archive.php');
global $nh_config, $nh_template_vars;

$nh_template_vars = array();
$nh_template_vars['content-type'] = 'listing';
$post_type = get_post_type();
$category = nh_get_categories();
$tags = nh_get_tags();
$section = $nh_config->get_section( $post_type, $category, $tags, false );

if( $section->key == 'none' )
{
	if( is_day() ):
		$nh_template_vars['page-title'] = sprintf( 'Daily Archives: %s', '<span>'.get_the_date().'</span>' );
	elseif( is_month() ):
		$nh_template_vars['page-title'] = sprintf( 'Monthly Archives: %s', '<span>'.get_the_date('F Y').'</span>' );
	elseif( is_year() ):
		$nh_template_vars['page-title'] = sprintf( 'Yearly Archives: %s', '<span>'.get_the_date('Y').'</span>' );
	else:
		$nh_template_vars['page-title'] = 'Archives';
	endif;
}
else
{
	$nh_template_vars['page-title'] = $section->name;
}

$nh_template_vars['section'] = $section;
nh_get_template_part( 'standard-template' );

