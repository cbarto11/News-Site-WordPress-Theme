<?php


define( 'ADMIN_PATH', dirname(__FILE__) );


if( is_admin() ):

//----------------------------------------------------------------------------------------
// Setup the plugin's admin pages.
//----------------------------------------------------------------------------------------
add_action('admin_menu', array('NH_AdminMain', 'setup_admin_pages'));  
add_action('admin_init', array('NH_AdminMain', 'setup_actions'));

//----------------------------------------------------------------------------------------
// Setup the admin page to accept AJAX requests.
//----------------------------------------------------------------------------------------
add_action("wp_ajax_nh-banner-options", array('NH_AdminMain', 'show_banner_ajax_page'));
add_action("wp_ajax_nh-stories-options", array('NH_AdminMain', 'show_stories_ajax_page'));

endif;


/**
 *
 */
class NH_AdminMain
{
	/**
	 *
	 */	
	public static function setup_admin_pages()
	{
		global $nh_config;
		
		add_menu_page(
			'Front Page',								// text to be displayed in the menu.
			'Front Page',								// text to be displayed for this actual menu item.
			'administrator',							// type of user that can access menu page.
			'nh-front-page',							// unique ID / slug for menu item.
			array( 'NH_AdminMain', 'show_admin_page' )	// function to call when rendering the menu page.
	    );
	    
	    if( $nh_config->show_template_part('banner') ):
		add_submenu_page(
			'nh-front-page',							// slug of parent menu
			'Banner',									// text to be displayed in the menu.
			'Banner',									// text to be displayed for this actual menu item.
			'administrator',							// type of user that can access menu page.
			'nh-banner',								// unique ID / slug for menu item.
			array( 'NH_AdminMain', 'show_admin_page' )	// function to call when rendering the menu page.
		);
	    endif;

	    add_submenu_page(
	    	'nh-front-page',
	    	'Header',
	    	'Header',
	    	'administrator',
	    	'nh-header',
	    	array( 'NH_AdminMain', 'show_admin_page' )
	    );

	    add_submenu_page(
	    	'nh-front-page',
	    	'Front Page',
	    	'Front Page',
	    	'administrator',
	    	'nh-front-page-stories',
	    	array( 'NH_AdminMain', 'show_admin_page' )
	    );

	    add_submenu_page(
	    	'nh-front-page',
	    	'Sidebar',
	    	'Sidebar',
	    	'administrator',
	    	'nh-sidebar-stories',
	    	array( 'NH_AdminMain', 'show_admin_page' )
	    );

	    add_submenu_page(
	    	'nh-front-page',
	    	'News',
	    	'News',
	    	'administrator',
	    	'nh-news-stories',
	    	array( 'NH_AdminMain', 'show_admin_page' )
	    );

	    add_submenu_page(
	    	'nh-front-page',
	    	'Reset',
	    	'Reset',
	    	'administrator',
	    	'nh-reset-options',
	    	array( 'NH_AdminMain', 'show_admin_page' )
	    );

	    remove_submenu_page( 'nh-front-page', 'nh-front-page' );
	    unset($GLOBALS['submenu']['nh-front-page'][0]);
	}

	
	
	/**
	 *
	 */	
	public static function show_admin_page()
	{
		require_once( ADMIN_PATH.'/admin-page.php' );
		NH_AdminPage::init();
		NH_AdminPage::show_page();
	}

	
	
	/**
	 * Processes AJAX requests from the plugin.
	 */
	public static function show_admin_ajax_page( $page )
	{
		require_once( ADMIN_PATH.'/admin-ajax-page.php' );
		NH_AdminAjaxPage::init( $page );
		NH_AdminAjaxPage::process();
		NH_AdminAjaxPage::output();
		exit();
	}
	
	
	public static function show_banner_ajax_page()
	{
		self::show_admin_ajax_page( 'banner' );
	}
	
	public static function show_stories_ajax_page()
	{
		self::show_admin_ajax_page( 'stories' );
	}


	/**
	 * Adds the needed JavaScript and CSS files needed for the plugin.
	 */	
	public static function setup_actions()
	{
		require_once( ADMIN_PATH.'/admin-page.php' );
		NH_AdminPage::init();
		NH_AdminPage::setup_actions();
	}

}	

