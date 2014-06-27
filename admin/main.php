<?php


define( 'ADMIN_PATH', dirname(__FILE__) );


if( is_admin() ):

//----------------------------------------------------------------------------------------
// Setup the plugin's admin pages.
//----------------------------------------------------------------------------------------
add_action( 'admin_menu', array('NH_AdminMain', 'setup_config'), 9 );
add_action( 'admin_menu', array('NH_AdminMain', 'setup_admin_pages'), 10 );
add_action( 'admin_init', array('NH_AdminMain', 'setup_config'), 9 );
add_action( 'admin_init', array('NH_AdminMain', 'setup_actions'), 5 );
add_action( 'admin_init', array('NH_AdminMain', 'register_settings'), 10 );

//----------------------------------------------------------------------------------------
// Setup the admin page to accept AJAX requests.
//----------------------------------------------------------------------------------------
add_action( 'wp_ajax_nh-banner-options', array('NH_AdminMain', 'show_banner_ajax_page') );
add_action( 'wp_ajax_nh-stories-options', array('NH_AdminMain', 'show_stories_ajax_page') );

endif;


class NH_AdminMain
{
	
	private static $_page = null;
	
	
	public static function setup_admin_pages()
	{
		global $nh_config, $nh_admin_pages;
	    
	    add_menu_page(
			'News Hub Settings', 
			'News Hub',
			'administrator',
			'nh-settings',
			array( 'NH_AdminMain', 'show_admin_page' )
	    );
	    
	    foreach( $nh_admin_pages as $page => $info )
	    {
	    	if( substr_compare('nh-ajax-', $page, 0, 8) ):
			add_submenu_page(
				'nh-settings',
				$info['title'],
				$info['menu'],
				'administrator',
				$page,
				array( 'NH_AdminMain', 'show_admin_page' )
			);
			endif;
	    }
	    
		remove_submenu_page( 'nh-settings', 'nh-settings' );
		unset($GLOBALS['submenu']['nh-settings'][0]);
		
		require_once( dirname(__FILE__).'/functions.php' );		
	}

	
	public static function init_page()
	{
		if( self::$_page !== null ) return true;
		
		global $nh_admin_pages, $pagenow;
		switch( $pagenow )
		{
			case 'options.php':
				$page = ( !empty($_POST['option_page']) ? $_POST['option_page'] : null );
				break;
			
			case 'admin.php':
				$page = ( !empty($_GET['page']) ? $_GET['page'] : null );
				break;
			
			default: return false; break;
		}
		
		if( array_key_exists($page, $nh_admin_pages) )
		{
			$info = $nh_admin_pages[$page];
			$path = nh_get_theme_file_path( 'admin/admin-page/'.$info['file'] );
			if( $path === null ) return false;
			
			require_once( dirname(__FILE__).'/admin-page.php' );
			require_once( $path );
		}
		else
		{
			self::$_page = null;
			return false;
		}
		
		if( !class_exists($info['class']) )
		{
			self::$_page = null;
			return false;
		}
		
		self::$_page = call_user_func( array($info['class'], 'get_instance'), $page );
		return true;
	}
	
	
	public static function setup_config()
	{
		global $nh_admin_pages;
		require_once( get_template_directory().'/admin/config.php' );
		$nh_admin_pages = apply_filters( 'nh-admin-pages', $nh_admin_pages );
	}
	
	
	public static function setup_actions()
	{
		global $pagenow;
		switch( $pagenow )
		{
			case 'options.php':
				if( !self::init_page() ) return;
				break;
			
			case 'admin.php':
				if( !self::init_page() ) return;
				add_action( 'admin_enqueue_scripts', array(self::$_page, 'enqueue_scripts') );
				add_action( 'admin_head', array(self::$_page, 'add_head_script') );
				break;
			
			default: return; break;
		}
		
		add_action( self::$_page->slug.'-register-settings', array(self::$_page, 'register_settings') );
		add_action( self::$_page->slug.'-add-settings-sections', array(self::$_page, 'add_settings_sections') );
		add_action( self::$_page->slug.'-add-settings-fields', array(self::$_page, 'add_settings_fields') );
	}


	public static function register_settings()
	{
		if( !self::init_page() ) return;
		
		do_action( self::$_page->slug.'-register-settings' );
		do_action( self::$_page->slug.'-add-settings-sections' );
		do_action( self::$_page->slug.'-add-settings-fields' );
		
		register_setting( self::$_page->slug, 'nh-options' );
		add_filter( 'sanitize_option_nh-options', array(get_class(), 'process_input'), 10, 2 );
	}
	
	
	public static function process_input( $input, $option )
	{
		global $nh_config;
		
		$page = $_POST['option_page'];
		$tab = ( !empty($_POST['tab']) ? $_POST['tab'] : null );
		$post = ( !empty($_POST[$option]) ? $_POST[$option] : null );
		$options = $nh_config->get_options();
		
		if( $tab !== null )
		$options = apply_filters( $page.'-'.$tab.'-process-input', $options, $page, $tab, $option, $post );
		
		$options = apply_filters( $page.'-process-input', $options, $page, $tab, $option, $post );
		
		return $options;
	}
	
	public static function show_admin_page()
	{
		if( !self::init_page() ) return;
		self::$_page->show();
	}
	
	
	private static function show_admin_ajax_page( $page )
	{
		global $nh_admin_pages;
		if( !array_key_exists('nh-ajax-'.$page, $nh_admin_pages) ) exit();
		
		$info = $nh_admin_pages['nh-ajax-'.$page];
		
		$path = nh_get_theme_file_path( 'admin/admin-ajax-page/'.$info['file'] );
		if( $path === null ) exit();
	
		require_once( dirname(__FILE__).'/admin-ajax-page.php' );
		require_once( $path );
		
		if( !class_exists($info['class']) ) exit();

		$page = call_user_func( array($info['class'], 'get_instance') );
		$page->show();
		
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

}	


