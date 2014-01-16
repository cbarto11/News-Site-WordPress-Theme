<?php

/**
 *
 */
class Exchange_NewsAdminPage
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
		global $exchange_config;
		$this->process_post();
		
		$news_stories_options = new Exchange_NewsStoriesOptions;
		$options = $news_stories_options->get_options( 'rss' );

		$nonce = wp_create_nonce("exchange-stories-options-nonce");

		?>
		<div id="news-stories-editor">
		
		<h2>News Editor</h2>
		<div class="instructions">Instruction go here...</div>
		
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

		<input type="hidden" name="exchange-stories-options-nonce" value="<?php echo $nonce; ?>" />
		<?php
		
		foreach( $this->COLUMNS as $column_id ):
			$column = $exchange_config->get_column($column_id);
			
			foreach( $column as $section_id ): 
				$section = $exchange_config->get_section($section_id);
				?>

				<h3><?php echo $section->title; ?></h3>

				<?php for($i = 0; $i < $section->rss_feed_num_stories; $i++): ?>
				
					<?php
					$post_title = '';
					if( (isset($options[$section->key])) && (count($options[$section->key]) > $i) )
					{
						$post_id = $options[$section->key][$i];
						if( $post_id > 0 ) $post_title = get_the_title($post_id);
					}
					else
					{
						$post_id = -1;
					}
					?>
					
					<input type="hidden" name="<?php echo $section->key; ?>[]" 
					                     value="<?php echo $post_id; ?>" 
					                     post_title="<?php echo $post_title; ?>"
					                     section="<?php echo $section->key; ?>"
					                     class="story-selector" />

				<?php endfor; ?>
		
			<?php endforeach; ?>

		<?php endforeach; ?>
		
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
		global $exchange_config;
		
		if( !isset($_POST) ) { return; }
		if( !isset($_POST['set_news_stories']) ) { return; }

		$options = array();
		foreach( $this->COLUMNS as $column_id )
		{
			$column = $exchange_config->get_column( $column_id );
			foreach( $column as $section_id )
			{
				$section = $exchange_config->get_section( $section_id );
				$options[$section->key] = array();
				if( isset($_POST[$section->key]) )
				{
					foreach( $_POST[$section->key] as $post_id )
					{
						$options[$section->key][] = $post_id;
					}
				}
				
				while( count($options[$section->key]) < $section->rss_feed_num_stories )
				{
					$options[$section->key][] = -1;
				}
			}
		}
		
		$news_stories_options = new Exchange_NewsStoriesOptions;
		$news_stories_options->save_options( $options );
	}
}
