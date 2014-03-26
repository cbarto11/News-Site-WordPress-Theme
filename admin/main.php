<?php

//----------------------------------------------------------------------------------------
// Setup the plugin's admin pages.
//----------------------------------------------------------------------------------------
add_action('admin_menu', array('NS_AdminPlugin', 'setup_admin_pages'));  

//----------------------------------------------------------------------------------------
// Setup the admin page to accept AJAX requests.
//----------------------------------------------------------------------------------------
add_action("wp_ajax_ns-banner-options", array('NS_AdminPlugin', 'show_banner_ajax_page'));
add_action("wp_ajax_ns-stories-options", array('NS_AdminPlugin', 'show_stories_ajax_page'));



/**
 *
 */
class NS_AdminPlugin
{
	/**
	 *
	 */	
	public static function setup_admin_pages()
	{
	    add_action( 'admin_enqueue_scripts', array('NS_AdminPlugin', 'add_scripts') );

		add_menu_page(
			'Front Page Editor',							// text to be displayed in the menu.
			'Front Page Editor',							// text to be displayed for this actual menu item.
			'administrator',								// type of user that can access menu page.
			'front-page-editor',							// unique ID / slug for menu item.
			array( 'NS_AdminPlugin', 'show_admin_page' )	// function to call when rendering the menu page.
	    );
	    
	    add_submenu_page(
	    	'front-page-editor',							// slug of parent menu
	    	'Banner',										// text to be displayed in the menu.
	    	'Banner',										// text to be displayed for this actual menu item.
	    	'administrator',								// type of user that can access menu page.
	    	'ns-banner',									// unique ID / slug for menu item.
	    	array( 'NS_AdminPlugin', 'show_banner_page' )	// function to call when rendering the menu page.
	    );

	    add_submenu_page(
	    	'front-page-editor',
	    	'Front Page',
	    	'Front Page',
	    	'administrator',
	    	'ns-front-page-stories',
	    	array( 'NS_AdminPlugin', 'show_front_page' )
	    );

	    add_submenu_page(
	    	'front-page-editor',
	    	'Sidebar',
	    	'Sidebar',
	    	'administrator',
	    	'ns-sidebar-stories',
	    	array( 'NS_AdminPlugin', 'show_sidebar_page' )
	    );

	    add_submenu_page(
	    	'front-page-editor',
	    	'News',
	    	'News',
	    	'administrator',
	    	'ns-news-stories',
	    	array( 'NS_AdminPlugin', 'show_news_page' )
	    );

	    remove_submenu_page( 'front-page-editor', 'font-page-editor' );
	    unset($GLOBALS['submenu']['front-page-editor'][0]);
	}

	
	
	/**
	 *
	 */	
	public static function show_admin_page()
	{
		exit();
	}

	
	
	/**
	 *
	 */	
	public static function show_banner_page()
	{
		require_once( dirname(__FILE__).'/banner-admin-page.php' );
		$admin_page = new NS_BannerAdminPage;
		$admin_page->show_page();
	}



	/**
	 *
	 */
	public static function show_banner_ajax_page()
	{
		require_once( dirname(__FILE__).'/banner-admin-ajax-page.php' );
		$admin_page = new NS_BannerAdminAjaxPage;
		$admin_page->process_post();
		$admin_page->display_output();
		exit();
	}

	
	
	/**
	 *
	 */	
	public static function show_front_page()
	{
		require_once( dirname(__FILE__).'/front-page-admin-page.php' );
		$admin_page = new NS_FrontPageAdminPage;
		$admin_page->show_page();
	}

	
	
	/**
	 *
	 */	
	public static function show_sidebar_page()
	{
		require_once( dirname(__FILE__).'/sidebar-admin-page.php' );
		$admin_page = new NS_SidebarAdminPage;
		$admin_page->show_page();
	}



	/**
	 *
	 */
	public static function show_stories_ajax_page()
	{
		require_once( dirname(__FILE__).'/stories-admin-ajax-page.php' );
		$admin_page = new NS_StoriesAdminAjaxPage;
		$admin_page->process_post();
		$admin_page->display_output();
		exit();
	}	
	


	/**
	 *
	 */	
	public static function show_news_page()
	{
		require_once( dirname(__FILE__).'/news-admin-page.php' );
		$admin_page = new NS_NewsAdminPage;
		$admin_page->show_page();
	}



	/**
	 *
	 */	
	public static function add_scripts()
	{
		wp_register_script( 
			'admin-banner-js', 
			get_template_directory_uri().'/admin/admin-banner.js', 
			array(),
			'1.0' );

		wp_register_script( 
			'admin-stories-js', 
			get_template_directory_uri().'/admin/admin-stories.js', 
			array(),
			'1.0' );

		wp_register_style( 
			'ns-admin-css', 
			get_template_directory_uri().'/styles/admin.css', 
			array(), 
			'1.0' );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'admin-banner-js' );
		wp_enqueue_script( 'admin-stories-js' );
		wp_enqueue_style( 'ns-admin-css' );
	}
}	
