<?php
/**
 * Displays the category archive page.
 *
 * @package WordPress
 * @subpackage news-hub-uncc
 */

//nh_print('PAGE:category.php');
global $nh_config, $nh_template_vars;

$nh_template_vars = array();
$nh_template_vars['content-type'] = 'listing';
$nh_template_vars['page-title'] = single_cat_title( '', false );

$category = get_category( get_cat_ID( single_cat_title('', false) ) );
$nh_template_vars['section'] = $nh_config->get_section( 'post', array( 'category' => $category->slug ), null );

$description = category_description( $category->term_id );
if( !empty($description) ) $nh_template_vars['description'] = $description;

nh_get_template_part( 'standard-template' );

