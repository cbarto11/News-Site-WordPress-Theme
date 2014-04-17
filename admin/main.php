<?php


define( 'ADMIN_PATH', dirname(__FILE__) );


if( is_admin() ):

//----------------------------------------------------------------------------------------
// Setup the plugin's admin pages.
//----------------------------------------------------------------------------------------
add_action('admin_menu', array('NS_AdminMain', 'setup_admin_pages'));  
add_action('admin_init', array('NS_AdminMain', 'setup_actions'));

//----------------------------------------------------------------------------------------
// Setup the admin page to accept AJAX requests.
//----------------------------------------------------------------------------------------
add_action("wp_ajax_ns-banner-options", array('NS_AdminMain', 'show_banner_ajax_page'));
add_action("wp_ajax_ns-stories-options", array('NS_AdminMain', 'show_stories_ajax_page'));

endif;


/**
 *
 */
class NS_AdminMain
{
	/**
	 *
	 */	
	public static function setup_admin_pages()
	{
		global $ns_config;
		
		add_menu_page(
			'Front Page',								// text to be displayed in the menu.
			'Front Page',								// text to be displayed for this actual menu item.
			'administrator',							// type of user that can access menu page.
			'ns-front-page',							// unique ID / slug for menu item.
			array( 'NS_AdminMain', 'show_admin_page' )	// function to call when rendering the menu page.
	    );
	    
	    if( $ns_config->show_template_part('banner') ):
		add_submenu_page(
			'ns-front-page',							// slug of parent menu
			'Banner',									// text to be displayed in the menu.
			'Banner',									// text to be displayed for this actual menu item.
			'administrator',							// type of user that can access menu page.
			'ns-banner',								// unique ID / slug for menu item.
			array( 'NS_AdminMain', 'show_admin_page' )	// function to call when rendering the menu page.
		);
	    endif;

	    add_submenu_page(
	    	'ns-front-page',
	    	'Header',
	    	'Header',
	    	'administrator',
	    	'ns-header',
	    	array( 'NS_AdminMain', 'show_admin_page' )
	    );

	    add_submenu_page(
	    	'ns-front-page',
	    	'Front Page',
	    	'Front Page',
	    	'administrator',
	    	'ns-front-page-stories',
	    	array( 'NS_AdminMain', 'show_admin_page' )
	    );

	    add_submenu_page(
	    	'ns-front-page',
	    	'Sidebar',
	    	'Sidebar',
	    	'administrator',
	    	'ns-sidebar-stories',
	    	array( 'NS_AdminMain', 'show_admin_page' )
	    );

	    add_submenu_page(
	    	'ns-front-page',
	    	'News',
	    	'News',
	    	'administrator',
	    	'ns-news-stories',
	    	array( 'NS_AdminMain', 'show_admin_page' )
	    );

	    add_submenu_page(
	    	'ns-front-page',
	    	'Reset',
	    	'Reset',
	    	'administrator',
	    	'ns-reset-options',
	    	array( 'NS_AdminMain', 'show_admin_page' )
	    );

	    remove_submenu_page( 'ns-front-page', 'ns-front-page' );
	    unset($GLOBALS['submenu']['ns-front-page'][0]);
	}

	
	
	/**
	 *
	 */	
	public static function show_admin_page()
	{
		require_once( ADMIN_PATH.'/admin-page.php' );
		NS_AdminPage::init();
		NS_AdminPage::show_page();
	}

	
	
	/**
	 * Processes AJAX requests from the plugin.
	 */
	public static function show_admin_ajax_page( $page )
	{
		require_once( ADMIN_PATH.'/admin-ajax-page.php' );
		NS_AdminAjaxPage::init( $page );
		NS_AdminAjaxPage::process();
		NS_AdminAjaxPage::output();
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
		NS_AdminPage::init();
		NS_AdminPage::setup_actions();
	}

}	

