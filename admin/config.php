<?php

global $nh_admin_pages;
$nh_admin_pages = array();

// $nh_admin_pages[''] = array(
// 	'title' => '',
// 	'menu'  => '',
// 	'file'  => '.php',
// 	'class' => '',
// );

$nh_admin_pages['nh-theme-options'] = array(
	'title' => 'Theme Options',
	'menu'  => 'Theme Options',
	'file'  => 'theme-options.php',
	'class' => 'NH_AdminPage_ThemeOptions',
);

$nh_admin_pages['nh-sections'] = array(
	'title' => 'Sections',
	'menu'  => 'Sections',
	'file'  => 'sections.php',
	'class' => 'NH_AdminPage_Sections',
);

$nh_admin_pages['nh-layout'] = array(
	'title' => 'Layout',
	'menu'  => 'Layout',
	'file'  => 'layout.php',
	'class' => 'NH_AdminPage_Layout',
);

$nh_admin_pages['nh-site-editor'] = array(
	'title' => 'Site Editor',
	'menu'  => 'Site Editor',
	'file'  => 'site-editor.php',
	'class' => 'NH_AdminPage_SiteEditor',
);



// 
// nh_print($page, 'page');
// nh_print($tab, 'tab');
// nh_print($option, 'name');
// nh_print($options, 'options');
// nh_print($input, 'input');
// 		
// create slug/key
// sanitize_title( $title, $fallback_title, $context )
// 		
// create class name with A-Z,a-z,0-9,_,-
// sanitize_html_class( "" )
// 		
// Sanitize a string from user input or from the db.
// sanitize_text_field( $str )
// 		
// Encodes < > & " ' (less than, greater than, ampersand, double quote, single quote). Will never double encode entities.
// esc_attr_e( $text )
// 		
// Sanitize content for allowed HTML tags for post content.
// wp_kses_post( $data );
// 

