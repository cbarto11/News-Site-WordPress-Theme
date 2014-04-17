<?php

/**
 *
 */
class NH_AdminPage_Header
{

	public static $error_messages;
	public static $notice_messages;


	/* Default private constructor. */
	private function __construct() { }
	
	
	/**
	 *
	 */	
	public static function init()
	{
		self::$error_messages = array();
		self::$notice_messages = array();
	}


	/**
	 *
	 */	
	public static function display_messages()
	{
		foreach( self::$error_messages as $message )
		{
			?><div class="error"><?php echo $message; ?></div><?php
		}
		
		foreach( self::$notice_messages as $message )
		{
			?><div class="updated"><?php echo $message; ?></div><?php
		}
	}
	

	/**
	 * 
	 */
	public static function enqueue_scripts()
	{
		wp_deregister_script('jquery');
		wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
	}
	

	/**
	 * 
	 */
	public static function add_head_script()
	{
		?>
		<style>
		
		.admin-container {
			width:840px;
		}
		.instructions {
			font-style:italic;
			margin-bottom:1em;
		}
		.update-button {
			padding: 0.5em;
			float: right;
			display: block;
			background-color: #ccc;
			margin-top: 0.5em;
		}
			
		</style>
  		<script type="text/javascript">
			jQuery(document).ready( function()
			{
				
				
				
			});
		</script>
		<?php
	}
	

	/**
	 *
	 */
	public static function show_page()
	{
		global $nh_config;
		self::init();
		self::process_post();

		$options = $nh_config->get_admin_options( 'front-page' );
		$nonce = wp_create_nonce("nh-stories-optionh-nonce");

		$num_stories_options = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 );
		
		$header = $nh_config->get_value( 'header' );
				
// 		nh_print($options, 'SHOW PAGE OPTIONS');
		
		?>
		<div id="header-editor" class="admin-container">

		<h2>Featured Posts Editor</h2>
		<div class="instructions">Chose the number of posts you want to be displayed in each section below. Type in the name of an existing post to select it for any given allocated Front Page block.</div>
		
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

		<input type="hidden" name="nh-stories-optionh-nonce" value="<?php echo $nonce; ?>" />
		
		<h5>Title</h5>
		<label>Text: </label>
		<input type="text" name="title[text]" value="<?php echo $header['title']['text']; ?>" /><br/>
		<label>URL: </label>
		<input type="text" name="title[url]" value="<?php echo $header['title']['url']; ?>" /><br/>

		<h5>Description</h5>
		<label>Text: </label>
		<input type="text" name="description[text]" value="<?php echo $header['description']['text']; ?>" /><br/>
		<label>URL: </label>
		<input type="text" name="description[url]" value="<?php echo $header['description']['url']; ?>" /><br/>
		
		<input type="submit" name="set_header_values" value="Update Header" class="update-button" />
		
		</form>
		
		</div><!-- #header-editor -->

		<?php
	}



	/**
	 *
	 */	
	private static function process_post()
	{
		global $nh_config;
		
		if( !isset($_POST['title']) ) { return; }
		if( !isset($_POST['description']) ) { return; }

		//nh_print( $_POST['stories'], 'STORIES' );

		$nh_config->set_header( $_POST['title'], $_POST['description'] );
		$nh_config->save_options();
	}

}

