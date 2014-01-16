<?php
/**
 * Displays the Event custom post type archive page.
 *
 * @package WordPress
 * @subpackage clas-exchange
 */

global $ns_config, $ns_template_vars;

$ns_template_vars = array();
$ns_template_vars['content-type'] = 'listing';
if( is_day() ):
	$ns_template_vars['page-title'] = sprintf( 'Daily Archives: %s', '<span>'.get_the_date().'</span>' );
elseif( is_month() ):
	$ns_template_vars['page-title'] = sprintf( 'Monthly Archives: %s', '<span>'.get_the_date('F Y').'</span>' );
elseif( is_year() ):
	$ns_template_vars['page-title'] = sprintf( 'Yearly Archives: %s', '<span>'.get_the_date('Y').'</span>' );
else:
	$ns_template_vars['page-title'] = 'Archives';
endif;

$ns_template_vars['section'] = $ns_config->get_default_section();

ns_get_template_part( 'standard-template' );

