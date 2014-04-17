<?php


class NS_AdminAjaxPage
{
	private static $_output;
	private static $_page;
	private static $_class;
	private static $_filename;

	/* */
	private function __construct() { }
	

	/**
	 * 
	 */
	public static function init( $page )
	{
		self::$_output = array();

		if( self::$_page !== null ) return;
		
		if( $page )
		{		
			switch( $page )
			{
				case 'banner':
				case 'stories':
					self::$_page = $page; break;

				default:
					self::$_page = null; break;
			}
		}
		else
		{
			self::$_page = null;
		}
		
		if( self::$_page == null ) return;
		
		self::$_class = str_replace( '-', '', ucfirst(self::$_page) );
		self::$_class = 'NS_AdminAjaxPage_'.self::$_class;

		self::$_filename = ADMIN_PATH.'/ajax-admin-page/'.self::$_page.'.php';
		if( file_exists(self::$_filename) ) { require_once(self::$_filename); }
		
		call_user_func( array(self::$_class, 'init') );
	}
	

	/**
	 * 
	 */
	public static function process()
	{
		call_user_func( array(self::$_class, 'process_post') );
	}
	

	/**
	 * 
	 */
	public static function output()
	{
		self::$_output = call_user_func( array(self::$_class, 'get_output') );

		echo json_encode(
			self::$_output
		);
	}

}

