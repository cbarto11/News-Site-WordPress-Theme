<?php
/**
 * Displays the Event custom post type archive page.
 *
 * @package WordPress
 * @subpackage news-hub-uncc
 */

// nh_print( 'PAGE:archive.php' );
global $wp_query, $nh_config, $nh_template_vars;

$nh_template_vars = array();
$nh_template_vars['content-type'] = 'listing';
$section = nh_get_section();

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

