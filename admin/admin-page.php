<?php


/**
 * Processes, generates, and displays the plugin's admin page.
 */
class NH_AdminPage
{

	private static $_page;
	private static $_class;
	private static $_filename;


	/**
	 * Default Constructor.
	 */	
	private function __construct() {}

	
	/**
	 * 
	 */	
	public static function init()
	{
		if( self::$_page !== null ) return;
		
		if( !empty($_GET['page']) )
		{		
			switch( $_GET['page'] )
			{
				case 'nh-banner':
				case 'nh-header':
				case 'nh-front-page-stories':
				case 'nh-sidebar-stories':
				case 'nh-news-stories':
				case 'nh-reset-options':
					self::$_page = substr($_GET['page'], 3); break;

				default:
					self::$_class = ''; return; break;
			}
		}
		else
		{
			self::$_class = ''; return; break;
		}
		
		self::$_class = str_replace( '-', '', ucfirst(self::$_page) );
		self::$_class = 'NH_AdminPage_'.self::$_class;

		self::$_filename = ADMIN_PATH.'/admin-page/'.self::$_page.'.php';
		if( file_exists(self::$_filename) ) require_once(self::$_filename);
		
		if( !class_exists(self::$_class) ) return;
		call_user_func( array(self::$_class, 'init') );
	}
	

	/**
	 * 
	 */	
	public static function show_page()
	{
		if( !class_exists(self::$_class) ) return;
		call_user_func( array(self::$_class, 'show_page') );
	}
	
	
	/**
	 *
	 */	
	public static function setup_actions()
	{
		if( !class_exists(self::$_class) ) return;
		add_action( 'admin_enqueue_scripts', array(self::$_class, 'enqueue_scripts') );
		add_action( 'admin_head', array(self::$_class, 'add_head_script') );
	}

}

