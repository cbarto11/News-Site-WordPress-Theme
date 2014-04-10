<?php

/**
 *
 */
class NS_ResetOptionsAdminPage
{

	/**
	 *
	 */
	public function __construct()
	{
		
	}



	/**
	 *
	 */
	public function show_page()
	{
		global $ns_config;
		$this->process_post();
		
		$nonce = wp_create_nonce("ns-stories-options-nonce");
		?>
		<div id="reset-options-editor">
		
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
	private function process_post()
	{
		if( !isset($_POST['reset_options']) ) { return; }
		global $ns_config;
		$ns_config->reset_options();
	}
}
