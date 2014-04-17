<?php


/**
 * Processes, generates, and displays the plugin's admin page.
 */
class NS_AdminPage
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
				case 'ns-banner':
				case 'ns-header':
				case 'ns-front-page-stories':
				case 'ns-sidebar-stories':
				case 'ns-news-stories':
				case 'ns-reset-options':
					self::$_page = substr($_GET['page'], 3); break;

				default:
					self::$_page = 'banner'; break;
			}
		}
		else
		{
			self::$_page = 'banner';
		}
		
		self::$_class = str_replace( '-', '', ucfirst(self::$_page) );
		self::$_class = 'NS_AdminPage_'.self::$_class;

		self::$_filename = ADMIN_PATH.'/admin-page/'.self::$_page.'.php';
		if( file_exists(self::$_filename) ) require_once(self::$_filename);
	}
	

	/**
	 * 
	 */	
	public static function show_page()
	{
		call_user_func( array(self::$_class, 'show_page') );
	}
	
	
	/**
	 *
	 */	
	public static function setup_actions()
	{
		add_action( 'admin_enqueue_scripts', array(self::$_class, 'enqueue_scripts') );
		add_action( 'admin_head', array(self::$_class, 'add_head_script') );
	}

}

