<?php

/**
 *
 */
class NS_AdminPage_NewsStories
{
	private static $COLUMNS = array( 'news' );
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

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_enqueue_script( 
			'admin-stories-js', 
			get_template_directory_uri().'/admin/scripts/jquery.stories.js', 
			array(),
			'1.0' );
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
		.selected-story-wrapper {
			border: solid 1px #ccc;
			padding:20px;
			margin-bottom:5px;
			min-height:2em;
			width:800px;
		}
		.selected-story-wrapper input[type="text"] {
			float:left;
			display:inline-block;
			width:200px;
		}
		.selected-story-wrapper .search-results {
			clear:both;
			margin-top:10px;
			border:solid 1px #ccc;
		}
		.selected-story-wrapper div {
			padding:3px;
		}
		.selected-story-wrapper .result.odd {
			background-color:#ddd;
		}
		.selected-story-wrapper .result.even {
			background-color:#eee
		}
		.selected-story-wrapper .result:hover {
			background-color:#ccc;
			cursor:pointer;
		}
		.selected-story-wrapper .reset-item {
			width: 2em;
			height: 2em;
			background-image:url('<?php echo get_template_directory_uri(); ?>/images/delete-icon.png');
			background-repeat:no-repeat;
			background-position:center center;
			display:inline-block;
			margin-left:20px;
			float:left;
		}
		.selected-story-wrapper .reset-item:hover {
			cursor:pointer;
		}
		.selected-story-wrapper .selected-item {
			margin-left: 4px;
			display: inline-block;
			height: 2em;
			line-height: 2em;
			width:530px;
			overflow:auto;
		}
		.section-header {
			width:840px;
		}
		h3 {
			display:inline-block;
		}
		.num-stories {
			display:inline-block;
			margin:1em 0;
			float:right;
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
		global $ns_config;
		self::init();
		self::process_post();
		
		$options = $ns_config->get_admin_options( 'news' );
		$nonce = wp_create_nonce("ns-stories-options-nonce");
		
		$num_stories_options = array( 1, 2, 5, 10, 15, 20, 25, 30 );
		
		//ns_print($options, 'SHOW PAGE OPTIONS');
		
		?>
		<div id="news-stories-editor" class="admin-container">
		
		<h2>News Editor</h2>
		<div class="instructions">Chose the number of posts you want to be displayed in the selected news feed below. Type in the name of an existing post to select it for any given allocated selected news block.</div>
		
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

		<input type="hidden" name="ns-stories-options-nonce" value="<?php echo $nonce; ?>" />
		<?php
		$section = $ns_config->get_section_by_key( 'news' );
		?>

		<div class="section-header">

		<h3><?php echo $section->title; ?></h3>

		<div class="num-stories">
		<strong># of posts: &nbsp;</strong>
		<select name="num-stories[<?php echo $section->key; ?>]">
			<?php foreach( $num_stories_options as $num ): ?>
			<option value="<?php echo $num; ?>" <?php echo ($num == $section->num_stories['rss-feed'] ? 'selected' : ''); ?>><?php echo $num; ?></option>
			<?php endforeach; ?>
		</select>
		</div>
		
		</div>

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
		
		
		<input type="submit" name="set_news_stories" value="Update Posts" class="update-button" />
		
		</form>

		</div><!-- #news-stories-editor -->

		<?php
	}



	/**
	 *
	 */	
	private static function process_post()
	{
		global $ns_config;
		
		if( !isset($_POST['set_news_stories']) ) { return; }
		if( !isset($_POST['stories']) ) { return; }

		//ns_print( $_POST['stories'], 'STORIES' );

		$ns_config->set_stories( 'news', $_POST['stories']['news'] );
		$ns_config->set_num_stories( 'news', $_POST['num-stories'] );
		$ns_config->save_options();
	}

}

