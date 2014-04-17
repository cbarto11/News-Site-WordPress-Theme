<?php

/**
 *
 */
class NH_AdminAjaxPage_Stories
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
			!wp_verify_nonce($_POST['nonce'], "nh-stories-options-nonce") )
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
			case 'get-search-results':
				self::get_search_results();
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
	private static function get_search_results()
	{
		global $nh_config;
		
		if( !isset($_POST['section']) )
		{
			self::$_status = false;
			self::$_message = 'No section data.';
			return;
		}
		
		if( !isset($_POST['search_text']) )
		{
			self::$_status = false;
			self::$_message = 'No search text.';
			return;
		}
		
		$section = $nh_config->get_section_by_key( $_POST['section'] );
		
		if( $section === null )
		{
			self::$_status = false;
			self::$_message = 'Invalid section: "'.$_POST['section'].'"';
			return;
		}
		
		$search_results = $section->get_search_results( $_POST['search_text'] );
		if( $search_results === false )
		{
			self::$_status = false;
			self::$_message = 'Unable to retrieve search results.';
			return;
		}
		
		self::$_output = $search_results;
		self::$_message = 'The search results were successfully retrieved.';
	}

}

