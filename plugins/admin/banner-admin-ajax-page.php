<?php

/**
 *
 */
class Exchange_BannerAdminAjaxPage
{
	private $_error_messages;
	private $_title;
	
	private $_status;
	private $_message;



	/**
	 *
	 */	
	public function __construct()
	{
		$this->_status = true;
		$this->_message = '';
	}
	
	

	/**
	 *
	 */
	public function process_post()
	{
		if( !isset($_POST['nonce']) || 
			!wp_verify_nonce($_POST['nonce'], "exchange-banner-options-nonce") )
		{
			$this->_status = false;
			$this->_message = 'Invalid nonce code ('.$_POST['nonce'].').';
			return;
   		}
   		
		if( !isset($_POST) )
		{
			$this->_status = false;
			$this->_message = 'No post data.';
			return;
		}
		
		if( !isset($_POST['ajax-action']) )
		{
			$this->_status = false;
			$this->_message = 'No action post data.';
			return;
		}

		switch( $_POST['ajax-action'] )
		{
			case 'delete-banner':
				$this->delete_banner();
				break;
			
			default:
				$this->_status = false;
				$this->_message = 'Invalid ajax-action type ('.$_POST['ajax-action'].').';
				break;
		}	
	}



	/**
	 *
	 */
	public function display_output()
	{
		echo json_encode(
			array(
				'status' => $this->_status,
				'message' => $this->_message
			)
		);
	}
	
	

	/**
	 *
	 */
	private function delete_banner()
	{
		if( !isset($_POST['banner_id']) )
		{
			$this->_status = false;
			$this->_message = 'No banner id data.';
			return;
		}
		
		$banner_id = $_POST['banner_id'];
		
		if( wp_delete_attachment( $banner_id, true ) === false )
		{
			$this->_status = false;
			$this->_message = 'Deleting the banner attachment failed.';
			return;
		}
		
		$this->_message = 'The banner attachment was deleted.';
	}
}
