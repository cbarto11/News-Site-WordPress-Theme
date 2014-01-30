<?php

/**
 *
 */
class NS_FrontPageAdminPage
{
	private $COLUMNS = array( '1', '2' );



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

		$options = $ns_config->get_admin_options( 'front-page' );
		$nonce = wp_create_nonce("ns-stories-options-nonce");
		
		//ns_print($options, 'SHOW PAGE OPTIONS');
		
		?>
		<h2>Featured Stories Editor</h2>
		<div class="instructions">Instruction go here...</div>
		
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

		<input type="hidden" name="ns-stories-options-nonce" value="<?php echo $nonce; ?>" />
		<?php
		
		foreach( $options['sections'] as $column => $sections ):
			
			foreach( $sections as $section_key ):
				$section = $ns_config->get_section_by_key( $section_key );
				//ns_print($section);
				?>

				<h3><?php echo $section->title; ?></h3>

				<?php for($i = 0; $i < $section->num_stories['front-page']; $i++): ?>
				
					<?php
					$post_title = '';
					if( (isset($options['stories'][$section_key])) && 
					    (count($options['stories'][$section_key]) > $i) )
					{
						$post_id = $options['stories'][$section_key][$i];
						if( $post_id > 0 ) $post_title = get_the_title($post_id);
					}
					else
					{
						$post_id = -1;
					}
					?>
					
					<input type="hidden" name="stories[<?php echo $section_key; ?>][]" 
					                     value="<?php echo $post_id; ?>" 
					                     post_title="<?php echo $post_title; ?>"
					                     section="<?php echo $section_key; ?>"
					                     class="story-selector" />

				<?php endfor; ?>
		
			<?php endforeach; ?>

		<?php endforeach; ?>
		
		<input type="submit" name="set_front_page_stories" value="Update Stories" />
		
		</form>

		<?php
	}



	/**
	 *
	 */	
	private function process_post()
	{
		global $ns_config;
		
		if( !isset($_POST['set_front_page_stories']) ) { return; }
		if( !isset($_POST['stories']) ) { return; }

		//ns_print( $_POST['stories'], 'STORIES' );

		$ns_config->set_stories( 'front-page', $_POST['stories'] );
	}
}
