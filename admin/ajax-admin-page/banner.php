<?php

/**
 *
 */
class NH_AdminAjaxPage_Banner
{
	private static $_status;
	private static $_message;
	private static $_output;


	/**
	 *
	 */	
	private function __construct() { }
	
	public static function init()
	{
		self::$_status = true;
		self::$_message = '';
		self::$_output = '';
	}
	
	
	/**
	 *
	 */
	public static function get_output()
	{
		return array(
				'status' => self::$_status,
				'message' => self::$_message,
				'output' => self::$_output
			);
	}
	
	
	/**
	 *
	 */
	public static function process_post()
	{
		if( !isset($_POST['nonce']) || 
			!wp_verify_nonce($_POST['nonce'], "nh-banner-options-nonce") )
		{
			self::$_status = false;
			self::$_message = 'Invalid nonce code ('.$_POST['nonce'].').';
			return;
   		}
   		
		if( !isset($_POST) )
		{
			self::$_status = false;
			self::$_message = 'No post data.';
			return;
		}
		
		if( !isset($_POST['ajax-action']) )
		{
			self::$_status = false;
			self::$_message = 'No action post data.';
			return;
		}

		switch( $_POST['ajax-action'] )
		{
			case 'delete-banner':
				self::delete_banner();
				break;
			
			default:
				self::$_status = false;
				self::$_message = 'Invalid ajax-action type ('.$_POST['ajax-action'].').';
				break;
		}	
	}


	/**
	 *
	 */
	private static function delete_banner()
	{
		if( !isset($_POST['banner_id']) )
		{
			self::$_status = false;
			self::$_message = 'No banner id data.';
			return;
		}
		
		$banner_id = $_POST['banner_id'];
		
		if( wp_delete_attachment( $banner_id, true ) === false )
		{
			self::$_status = false;
			self::$_message = 'Deleting the banner attachment failed.';
			return;
		}
		
		self::$_message = 'The banner attachment was deleted.';
	}

}

