<?php

global $nh_admin_pages;
$nh_admin_pages = array();



$nh_admin_pages['nh-design'] = array(
	'title' => 'Design',
	'menu'  => 'Design',
	'file'  => 'design.php',
	'class' => 'NH_AdminPage_Design',
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

$nh_admin_pages['nh-content'] = array(
	'title' => 'Content',
	'menu'  => 'Content',
	'file'  => 'content.php',
	'class' => 'NH_AdminPage_Content',
);





$nh_admin_pages['nh-ajax-stories'] = array(
	'file'  => 'stories.php',
	'class' => 'NH_AdminAjaxPage_Stories',
);

$nh_admin_pages['nh-ajax-banner'] = array(
	'file'  => 'banner.php',
	'class' => 'NH_AdminAjaxPage_Banner',
);


