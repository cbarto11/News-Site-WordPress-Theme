<?php
/**
 * Displays the tag archive page.
 *
 * @package WordPress
 * @subpackage clas-exchange
 */

global $ns_config, $ns_template_vars;

$ns_template_vars = array();
$ns_template_vars['content-type'] = 'listing';
$tag = get_term_by( 'title', single_tag_title('', false) );
$ns_template_vars['page-title'] = single_tag_title( '', false );
$ns_template_vars['section'] = $ns_config->get_section( 'post', $tag->slug, null );

ns_get_template_part( 'standard-template' );

