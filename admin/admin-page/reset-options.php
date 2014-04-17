<?php

/**
 *
 */
class NS_AdminPage_ResetOptions
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
		global $ns_config;
		self::init();
		self::process_post();
		
		$nonce = wp_create_nonce("ns-stories-options-nonce");
		?>
		<div id="reset-options-editor" class="admin-container">
		
		<h2>Reset Options</h2>
		<div class="instructions">Instruction go here...</div>
		
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

		<input type="hidden" name="ns-reset-options-nonce" value="<?php echo $nonce; ?>" />
		<input type="submit" name="reset_options" value="Reset Options" />
		
		</form>
		
		</div><!-- #reset-options-editor -->

		<?php
	}



	/**
	 *
	 */	
	private static function process_post()
	{
		if( !isset($_POST['reset_options']) ) { return; }
		global $ns_config;
		$ns_config->reset_options();
	}
}

