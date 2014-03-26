<?php

/**
 *
 */
class NS_NewsAdminPage
{
	private $COLUMNS = array( 'news' );

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
		
		$options = $ns_config->get_admin_options( 'news' );
		$nonce = wp_create_nonce("ns-stories-options-nonce");
		
		//ns_print($options, 'SHOW PAGE OPTIONS');
		
		?>
		<div id="news-stories-editor">
		
		<h2>News Editor</h2>
		<div class="instructions">Instruction go here...</div>
		
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

		<input type="hidden" name="ns-stories-options-nonce" value="<?php echo $nonce; ?>" />
		<?php
		$section = $ns_config->get_section_by_key( 'news' );
		?>

		<h3><?php echo $section->title; ?></h3>

		<?php for($i = 0; $i < $section->num_stories['rss-feed']; $i++): ?>
		
			<?php
			$post_title = '';
			if( (isset($options['stories'])) && 
			    (count($options['stories']) > $i) )
			{
				$post_id = $options['stories'][$i];
				if( $post_id > 0 ) $post_title = get_the_title($post_id);
			}
			else
			{
				$post_id = -1;
			}
			?>
			
			<input type="hidden" name="stories[<?php echo $section->key; ?>][]" 
								 value="<?php echo $post_id; ?>" 
								 post_title="<?php echo $post_title; ?>"
								 section="<?php echo $section->key; ?>"
								 class="story-selector" />

		<?php endfor; ?>
		
		
		<input type="submit" name="set_news_stories" value="Update Stories" />
		
		</form>

		</div><!-- #news-stories-editor -->

		<?php
	}



	/**
	 *
	 */	
	private function process_post()
	{
		global $ns_config;
		
		if( !isset($_POST['set_news_stories']) ) { return; }
		if( !isset($_POST['stories']) ) { return; }

		//ns_print( $_POST['stories'], 'STORIES' );

		$ns_config->set_stories( 'news', $_POST['stories']['news'] );
	}

}

