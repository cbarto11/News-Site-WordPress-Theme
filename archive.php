<?php
/**
 * Displays the Event custom post type archive page.
 *
 * @package WordPress
 * @subpackage clas-exchange
 */

//ns_print('archive.php');
global $ns_config, $ns_template_vars;

$ns_template_vars = array();
$ns_template_vars['content-type'] = 'listing';
$post_type = get_post_type();
$category = get_the_category();
$tags = get_the_tags();
$section = $ns_config->get_section( $post_type, $category, $tags, false );

if( $section->key == 'none' )
{
	if( is_day() ):
		$ns_template_vars['page-title'] = sprintf( 'Daily Archives: %s', '<span>'.get_the_date().'</span>' );
	elseif( is_month() ):
		$ns_template_vars['page-title'] = sprintf( 'Monthly Archives: %s', '<span>'.get_the_date('F Y').'</span>' );
	elseif( is_year() ):
		$ns_template_vars['page-title'] = sprintf( 'Yearly Archives: %s', '<span>'.get_the_date('Y').'</span>' );
	else:
		$ns_template_vars['page-title'] = 'Archives';
	endif;
}
else
{
	$ns_template_vars['page-title'] = $section->name;
}

$ns_template_vars['section'] = $section;
ns_get_template_part( 'standard-template' );

