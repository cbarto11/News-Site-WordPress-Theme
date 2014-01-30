<?php
/**
 * Displays the category archive page.
 *
 * @package WordPress
 * @subpackage clas-exchange
 */

//ns_print('category.php');
global $ns_config, $ns_template_vars;

$ns_template_vars = array();
$ns_template_vars['content-type'] = 'listing';
$category = get_category( get_cat_ID( single_cat_title('', false) ) );
$ns_template_vars['page-title'] = single_cat_title( '', false );
$ns_template_vars['section'] = $ns_config->get_section( 'post', $category->slug, null );

ns_get_template_part( 'standard-template' );

