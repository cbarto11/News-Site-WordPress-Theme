<?php

/**
 *
 */
class NH_AdminPage_Content extends NH_AdminPage
{

	private static $_instance = null;
	

	public $slug = null;
	public $tabs = array();
	public $tab = null;
	

	/* Default private constructor. */
	private function __construct( $slug )
	{
		$this->slug = $slug;
		
		$this->tabs = array(
			'banner' => 'Banner',
			'front-page' => 'Front Page',
			'sidebar' => 'Sidebar',
			'news-rss' => 'News RSS Feed',
		);
		$this->tabs = apply_filters( $this->slug.'-tabs', $this->tabs );
		
        $this->tab = ( !empty($_GET['tab']) && array_key_exists($_GET['tab'], $this->tabs) ? $_GET['tab'] : apply_filters( $this->slug.'-default-tab', 'banner' ) );		
	}
	
	
	
	/**
	 *
	 */	
	public static function get_instance( $slug )
	{
		if( self::$_instance === null )
		{
			self::$_instance = new NH_AdminPage_Content( $slug );
		}
		
		return self::$_instance;
	}



	
	/**
	 * 
	 */
	public function enqueue_scripts()
	{
		wp_deregister_script( 'jquery' );
		wp_enqueue_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js' );

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-widget' );
		wp_enqueue_script( 'jquery-ui-mouse' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_enqueue_script( 
			'admin-banner-js', 
			get_template_directory_uri().'/admin/scripts/jquery.banner.js', 
			array(),
			'1.0' );
		
		wp_enqueue_script( 
			'admin-stories-js', 
			get_template_directory_uri().'/admin/scripts/jquery.stories.js', 
			array(),
			'1.0' );
	}
	
	
	
	/**
	 * 
	 */
	public function add_head_script()
	{
		?>
		<style>
		
		
	.clearfix:before,
	.clearfix:after {
		content:"";
		display:table;
		clear:both;
	}
	
	/* For IE 6/7 (trigger hasLayout) */
	.clearfix {
		zoom:1;
	}
		
		p.submit {
			clear:both;
		}
		
		.nav-tab.active {
			color:#000;
			background-color:#fff;
		}
		
		.column {
			padding:6px;
			background-color:#eee;
			border:solid 1px #ccc;
			width:100px;
			float:left;
			margin-right:10px;
		}
		
		.column-title {
			text-align:center;
			border-bottom:solid 1px #ccc;
		}
		
		.section {
			padding:3px;
			background-color:#ccc;
			border:solid 1px #aaa;
			margin:3px;
			font-size:10px;
			height:40px;
		}
		
		.section .key {
			font-weight:bold;
			white-space:nowrap;
			text-overflow:ellipsis;
			overflow:hidden;
			background-color:#aaa;
			cursor:move;
		}
		
		.section select {
			font-size:10px;
			line-height:1.4em;
			height:1.4em;
			width:75px;
			margin:2px 5px;
		}
		
		.section-placeholder {
			padding:3px;
			background-color:#eee;
			border:solid 1px #ccc;
			margin:3px;
			height:40px;
		}
		
		/* from banner.php */
		
		.column-list {
			padding:10px 0px;
		}

		.admin-container {
			width:840px;
		}
		.instructions {
			font-style:italic;
			margin-bottom:1em;
		}
		.wp-upload-form {
			border:solid 1px #ccc;
			padding:20px;
			margin-bottom:20px;
		}
		.wp-upload-form .save-button {
			float:right;
		}
		#slider-banner-list {
			width:950px;
			padding:20px;
		}
		.banner-list {
		}
		.banner-list > div {
			position:relative;
			display:inline-block;
		}
		.banner-list img.banner {
			width:200px;
			height:70px;
		}
		.banner-list img.delete-button {
			width:16px;
			height:16px;
			position:absolute;
			top:5px;
			right:5px;
			z-index:10;
		}
		.banner-list img.delete-button:hover {
			cursor:pointer;
		}
		.slider-list {
			border:solid 1px gray;
			padding:5px;
			margin:0px;
		}
		.slider-list ol {
			list-style-type:none;
			margin:0px;
			padding:0px;
			width:100%;
		}
		.slider-list ol li.banner {
			padding:5px;
			cursor:move;
			background-color:silver;
			vertical-align:middle;
		}
		.slider-list ol li.banner img {
			display:block;
		}
		.slider-list ol li.banner div {
			margin-right:10px;
		}
		.slider-list ol li.banner:hover {
			background-color:#ffc;
		}
		.slider-list ol li.banner.out {
			background-color:red;
		}
		.slider-list ol li.banner span {
			position: absolute;
			margin-left: -1.3em;
		}
		.slider-list .banner > div {
			display:inline-block;
			vertical-align:middle;
		}
		.slider-list .banner img {
			width:200px;
		}
		.slider-list .banner input {
			width:188px;
			padding:3px 5px;
			border:solid 1px #cccccc;
			background-color:#f6f6f6;
		}
		.slider-list .banner label {
			font-size:0.8em;
			font-weight:bold;
		}
		h4 {
			margin:0; padding:0;
			margin-bottom:10px;
			text-align:center;
		}		
		.update-button {
			padding: 0.5em;
			float: right;
			display: block;
			background-color: #ccc;
			margin-top: 0.5em;
		}

		.admin-container {
			width:840px;
		}
		.instructions {
			font-style:italic;
			margin-bottom:1em;
		}
		.selected-story-wrapper {
			border: solid 1px #ccc;
			padding: 20px;
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
			
				jQuery( '.column-selection' ).change(
					function()
					{
						var num_columns = parseInt( this.value );
						for( var i = 1; i <= num_columns; i++ )
						{
							jQuery('.column-'+i).show();
						}
						for( var i = num_columns+1; i <= 2; i++ )
						{
							var column = jQuery('.column-'+i);
							if( !column ) break;
							
							jQuery(column).hide();
							jQuery('.column-sections .column-list')
								.append( jQuery(column).find('.column-list').html() );
							jQuery(column).find('.column-list').html('');
						}
					})
					.change();
				
				jQuery( ".column-list" ).sortable({
					connectWith: ".column-list",
					handle: ".key",
					placeholder: "section-placeholder"
				});
				
				jQuery( ".section" ).each( function()
				{
				
					var self = this;
					
					jQuery(self).find(".num-stories").change( function()
					{
						var num_stories = parseInt( jQuery(this).val() );
						var height = num_stories * 40;
						jQuery(self).css( 'height', height+'px' );
					})
					.change();
				
				});
				
			});
		</script>
		<?php
	}


	/**
	 *
	 */
	public function register_settings()
	{
		add_filter( $this->slug.'-process-input', array($this, 'process_input'), 99, 5 );
	}
	
	
	/**
	 *
	 */
	public function add_settings_sections()
	{
		add_settings_section(
			'banner', 'Banner', array( $this, 'print_banner_section' ),
			$this->slug.':banner'
		);

		add_settings_section(
			'front-page', 'Front Page', array( $this, 'print_front_page_section' ),
			$this->slug.':front-page'
		);

		add_settings_section(
			'sidebar', 'Sidebar', array( $this, 'print_sidebar_section' ),
			$this->slug.':sidebar'
		);

		add_settings_section(
			'news-rss', 'News RSS Feed', array( $this, 'print_news_rss_section' ),
			$this->slug.':news-rss'
		);
	}
	
	
	/**
	 *
	 */
	public function add_settings_fields()
	{
// 		add_settings_field( 
// 			'front-page', 'Front Page', array( $this, 'print_columns_selection' ),
// 			$this->slug.':columns', 'columns', array( 'front-page' )
// 		);
	}
	
	
	/**
	 *
	 */
	public function process_input( $options, $page, $tab, $option, $input )
	{
		global $nh_config;
		
// 		nh_print($page);
// 		nh_print($tab);
// 		nh_print($option);
// 		nh_print($input);

		if( !array_key_exists($tab, $input) ) $tab_input = array();
		else $tab_input = $input[$tab];

// 		nh_print($tab_input);
		
		switch( $tab )
		{
			case 'banner':

				//
				// Get list of banner slider images.
				//
				$banner_images = array();
				if( isset($tab_input['banner-id']) )
				{
					for( $i = 0; $i < count($tab_input['banner-id']); $i++ )
					{
						$banner_images[] = array(
							'id'  => intval( $tab_input['banner-id'][$i] ),
							'link' => $tab_input['banner-link'][$i],
							'title' => $tab_input['banner-title'][$i],
						);
					}
				}
				
				
				//			
				// Just uploading an image, so save banner images for the page refresh.
				// Process the uploaded file.
				//			
				if( isset($input['upload-banner-image']) )
				{
					set_transient( 'banner-slides', $banner_images );

// 					nh_print($_FILES);
					
					$uploaded_file = null;
					if( isset($_FILES['banner-image-upload']) )
					{
						$uploaded_file = $_FILES['banner-image-upload'];
					}
					else
					{
						add_settings_error( '', '', 'No upload file included.' );
						return $options;
					}
					
					list( $width, $height ) = getimagesize( $uploaded_file['tmp_name'] );
					$banner_width = $nh_config->get_value( 'banner', 'width' );
					$banner_height = $nh_config->get_value( 'banner', 'height' );
					
					if( ($banner_width !== null) && ($banner_width > 0) && ($width !== $banner_width) )
					{
						add_settings_error( '', '', 
							'The banner must be '.$banner_width.' x '.$banner_height.'. '.
							'The uploaded image is '.$width.' x '.$height.'.<br/>'.
							'The upload has been cancelled.'
						);
						return $options;
					}
					
					if( ($banner_height !== null) && ($banner_height > 0) && ($height !== $banner_height ) )
					{
						add_settings_error( '', '', 
							'The banner must be '.$banner_width.' x '.$banner_height.'. '.
							'The uploaded image is '.$width.' x '.$height.'.<br/>'.
							'The upload has been cancelled.'
						);
						return $options;
					}
					
					$wp_filetype = wp_check_filetype_and_ext( $uploaded_file['tmp_name'], $uploaded_file['name'], false );
					if ( ! wp_match_mime_types( 'image', $wp_filetype['type'] ) )
					{
						add_settings_error( '', '', 
							'The uploaded file is not a valid image.<br/>'.
							'Please try again.'
						);
						return $options;
					}
					
					$file = wp_handle_upload( $uploaded_file, array( 'test_form' => false ) );
					if( isset($file['error']) )
					{
						add_settings_error( '', '', 
							'Image Upload Error:<br/>'.
							$file['error']
						);
						return $options;
					}
					
					$url = $file['url'];
					$type = $file['type'];
					$file = $file['file'];
					$filename = basename($file);

					$object = array(
						'post_title'     => $filename,
						'post_content'   => $url,
						'post_mime_type' => $type,
						'guid'           => $url,
						'context'        => 'banner'
					);

					$attachment_id = wp_insert_attachment( $object, $file );

					if( !$attachment_id )
					{
						add_settings_error( '', '', 'An error occurred while inserting the banner into the database.' );
						return $options;
					}
					
					$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file );
					wp_update_attachment_metadata( $attachment_id, $attachment_data );
					
					add_settings_error( '', '', 'The banner image was successfully added.', 'updated' );
					return $options;
				}
				
				// 
				// The number of slides should not be more than the max slides.
				// 
				$max_slides = $nh_config->get_value( 'banner', 'max-slides' );
				if( ($max_slides !== null) && ($max_slides >= 0) && (count($banner_images) > $max_slides ) )
				{
					add_settings_error( '', '', 
						'The banner slider is set to only accept a maximum of '.$max_slides.' slides.<br/>'.
						'Please reduce the number of slides and try again.'
					);
					
					set_transient( 'banner-slides', $banner_images );
					return $options;
				}

				//
				// Saving banner images.
				//
				delete_transient( 'banner-slides' );
				$options['banner-slides'] = $banner_images;
				break;
			
			case 'front-page':
			case 'sidebar':
			case 'news-rss':
				$stories = $tab_input['stories'];
				
				foreach( $stories as $ss => $sections )
				{
					$stories_section = $ss.'-stories';
					
					foreach( $sections as $section_key => $story_ids )
					{
						for( $i = 0; $i < count($story_ids); $i++ )
						{
							$story_ids[$i] = intval($story_ids[$i]);
							if( $story_ids[$i] < 1 ) $story_ids[$i] = -1;
						}
						
						$options[$stories_section][$section_key] = $story_ids;
					}
				}
				break;
		}		
		
		return $options;
	}
	

	/**
	 *
	 */
	public function show()
	{
		global $nh_admin_pages;
		
		$use_multipart_encode = false;
		switch( $this->tab )
		{
			case 'banner':
				$use_multipart_encode = true;
				if( !isset($_GET['settings-updated']) )
					delete_transient( 'banner-slides' );
				break;
		}
		?>
		
		<div class="wrap">
	 
			<div id="icon-themes" class="icon32"></div>
			<h2><?php echo $nh_admin_pages[$this->slug]['title']; ?></h2>
			<?php settings_errors(); ?>
		 
			<h2 class="nav-tab-wrapper">
				<?php foreach( $this->tabs as $k => $t ): ?>
					<a href="?page=<?php echo $this->slug; ?>&tab=<?php echo $k; ?>" class="nav-tab <?php if($k==$this->tab) echo 'active'; ?>"><?php echo $t; ?></a>
				<?php endforeach; ?>
			</h2>
		
			<form method="post" action="options.php" <?php echo ( $use_multipart_encode ? 'enctype="multipart/form-data"' : '' ); ?>>
				<?php submit_button(); ?>
				<?php settings_fields( $this->slug ); ?>
				<input type="hidden" name="tab" value="<?php echo $this->tab; ?>" />
				<?php do_settings_sections( $this->slug.':'.$this->tab ); ?>
				<?php submit_button(); ?>
			</form>
					 
		</div><!-- /.wrap -->
		
		<?php
	}
	



	public function print_banner_section()
	{
		echo '<p>print_banner_section</p>';
		
		global $nh_config;
		
		
		if( get_transient( 'banner-slides' ) !== false )
		{
			$options = get_transient( 'banner-slides' );
		}
		else
		{
			$options = $nh_config->get_banner_slides();
			if( $options === null ) $options = array();
		}

		?>
		
		<label for="upload"><?php _e( 'Choose an image from your computer:' ); ?></label><br />
		<input type="file" id="upload" name="banner-image-upload" />
		<?php submit_button( 'Upload Banner Image', 'primary', 'nh-options[upload-banner-image]', false ); ?>

		<?php
		$args = array(
			'post_type' => 'attachment',
			'posts_per_page' => -1,
			'post_status' => 'any',
			'post_parent' => null,
			'meta_key' => '_wp_attachment_context',
			'meta_value' => 'banner'
   		);

		$banners = new WP_Query($args);		
		?>

		<div id="slider-banner-list">
		<div class="banner-list">
			<h4>Banner List</h4>

		<?php
		if( $banners->have_posts() )
		{
			while( $banners->have_posts())
			{
				$banners->the_post();
				$thumbnail_url = wp_get_attachment_image_src( get_the_ID(), 'thumbnail_landscape' );
				if( $thumbnail_url ) $thumbnail_url = $thumbnail_url[0];
				
				?>
				<div>
					<img src="<?php echo $thumbnail_url; ?>" banner-id="<?php echo get_the_ID(); ?>" class="banner" />
					<img src="<?php echo nh_get_theme_file_url('/images/delete-icon.png'); ?>" banner-id="<?php echo get_the_ID(); ?>" class="delete-button" />
				</div>
				<?php
			}
		}
		else
		{
			?>
			No Banner images found.
			<?php
		}
		?>

		</div><!-- .banner-list -->

		<?php
		wp_reset_query();
		?>

		<div class="slider-list">
			<h4>Slider List</h4>
		
			<ol>
			<?php

			foreach($options as $option):
			
				$thumbnail_url = wp_get_attachment_image_src( $option['id'], 'thumbnail_landscape' );
				if( $thumbnail_url )
					$thumbnail_url = $thumbnail_url[0];
				else
					continue;
			
				?>
				<li class="banner clearfix">

					<div class="image">
					<img src="<?php echo $thumbnail_url; ?>" />
					</div>
					
					<input type="hidden" 
					       name="<?php nh_input_name_e( $this->tab, 'banner-id', '' ); ?>" 
					       value="<?php echo htmlentities($option['id']); ?>" />

					<div class="link">
					<label for="<?php nh_input_name_e( $this->tab, 'banner-link', '' ); ?>">Link</label>
					<input type="text" 
					       id="<?php nh_input_name_e( $this->tab, 'banner-link', '' ); ?>" 
					       name="<?php nh_input_name_e( $this->tab, 'banner-link', '' ); ?>" 
					       value="<?php echo htmlentities($option['link']); ?>" />
					</div>
					
					<div class="title">
					<label for="<?php nh_input_name_e( $this->tab, 'banner-title', '' ); ?>">Title</label>
					<input type="text" 
					       id="<?php nh_input_name_e( $this->tab, 'banner-title', '' ); ?>" 
					       name="<?php nh_input_name_e( $this->tab, 'banner-title', '' ); ?>" 
					       value="<?php echo htmlentities(stripslashes($option['title'])); ?>" />
					</div>

				</li>
				<?php
			
			endforeach;

			?>
			</ol>
		</div><!-- .slider-list -->
		
		<?php
	}
	
	public function print_front_page_section()
	{
		echo '<p>Chose the number of posts you want to be displayed in each section below. Type in the name of an existing post to select it for any given allocated Front Page block.</p>';
		
		$this->print_stories( 'front-page' );
	}

	public function print_sidebar_section()
	{
		echo '<p>Chose the number of posts you want to be displayed in each sidebar block below. Type in the name of an existing post to select it for any given allocated Sidebar block.</p>';

		$this->print_stories( 'sidebar' );
	}
	
	public function print_news_rss_section()
	{
		echo '<p>Chose the number of posts you want to be displayed in the selected news feed below. Type in the name of an existing post to select it for any given allocated selected news block.</p>';

		$this->print_stories( 'rss-feed', 'news' );
	}


	private function print_stories( $stories_key, $section_keys = null )
	{
		global $nh_config;

		$num_stories_options = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 );
		
		if( $section_keys )
		{
			if( !is_array($section_keys) ) $section_keys = array( $section_keys );
		}
		else
		{
			$sections = $nh_config->get_value( $stories_key.'-sections' );
			if( $sections === null )
			{
				$section_keys = array();
			}
			else
			{
				ksort( $sections );
				$section_keys = array();
				foreach( $sections as $column_name => $column_sections )
				{
					$section_keys = array_merge( $section_keys, $column_sections );
				}
			}
		}
		$section_keys = array_unique( $section_keys );
		
		$section_data = array();
		foreach( $section_keys as $section_key )
		{
			$section = $nh_config->get_section_by_key( $section_key, true );
			if( $section === null ) continue;
			
			$stories = $nh_config->get_value( $stories_key.'-stories', $section_key );
			if( $stories === null ) $stories = array();
			
			$section_data[$section_key] = array(
				'section' => $section,
				'stories' => $stories,
			);
		}
		
		?>

		<?php foreach( $section_data as $section_key => $section_data ): ?>

			<h3><?php echo $section_data['section']->title; ?></h3>
			
			<?php for($i = 0; $i < $section_data['section']->num_stories[$stories_key]; $i++): ?>
			
				<?php
				$post_title = '';
				if( count($section_data['stories']) > $i )
				{
					$post_id = $section_data['stories'][$i];
					if( $post_id > 0 ) $post_title = get_the_title($post_id);
				}
				else
				{
					$post_id = -1;
				}
				?>
				
				<input type="hidden" name="<?php nh_input_name_e( $this->tab, 'stories', $stories_key, $section_key, '' ); ?>" 
									 value="<?php echo $post_id; ?>" 
									 post_title="<?php echo $post_title; ?>"
									 section="<?php echo $section_key; ?>"
									 class="story-selector" />
		
			<?php endfor; ?>

		<?php endforeach; ?>

		<?php
	}
		
}


