<?php

/**
 *
 */
class NH_AdminPage_Banner
{

	/* */
	public static $error_messages;
	public static $notice_messages;
	private static $config;


	/* Default private constructor. */
	private function __construct() { }
	
	
	/**
	 *
	 */	
	public static function init()
	{
		global $nh_config;
		
		self::$error_messages = array();
		self::$notice_messages = array();
		self::$config = $nh_config->get_value('banner');
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
			'admin-banner-js', 
			get_template_directory_uri().'/admin/scripts/jquery.banner.js', 
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
			width:630px;
			padding:5px;
			float:left;
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
			float:left;
			border:solid 1px gray;
			width:220px;
			padding:5px;
			margin:0px;
		}
		.slider-list ol {
			list-style-type:none;
			margin:0px;
			padding:0px;
		}
		.slider-list ol li.banner {
			margin:5px;
			padding:5px;
			cursor:move;
			background-color:silver;
		}
		.slider-list ol li.banner.out {
			background-color:red;
		}
		.slider-list ol li.banner span {
			position: absolute;
			margin-left: -1.3em;
		}
		.slider-list .banner img {
			width:200px;
			height:70px;
		}
		.slider-list .banner input {
			width:188px;
			padding:3px 5px;
			border:solid 1px #cccccc;
			background-color:#f6f6f6;
		}
		.slider-list .banner label {
			display:block;
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
		
		$options = $nh_config->get_banner_images();
		$nonce = wp_create_nonce("nh-banner-optionh-nonce");

		if( $options === null ) $options = array();		
		?>
		
		<div id="banner-editor" class="admin-container">
		
		<h2>Banner Editor</h2>
		<div class="instructions">Instruction go here...</div>
		
		<input type="hidden" name="nh-banner-optionh-nonce" value="<?php echo $nonce; ?>" />
		
		<form id="upload-form" class="wp-upload-form" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<label for="upload"><?php _e( 'Choose an image from your computer:' ); ?></label><br />
			<input type="file" id="upload" name="import" />
			<input type="hidden" name="action" value="upload-file" />
			<?php //wp_nonce_field( 'custom-header-upload', '_wpnonce-custom-header-upload' ); ?>
			<input type="submit" value="save slider" class="save-button" />
		</form>		
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
					<img src="<?php echo $thumbnail_url; ?>" banner_id="<?php echo get_the_ID(); ?>" class="banner" />
					<img src="<?php echo nh_get_theme_file_url('/images/delete-icon.png'); ?>" banner_id="<?php echo get_the_ID(); ?>" class="delete-button" />
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
		
			<form name="slider-form" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<ol>
			<?php

			foreach($options as $option)
			{
				$thumbnail_url = wp_get_attachment_image_src( $option['id'], 'thumbnail_landscape' );
				if( $thumbnail_url )
					$thumbnail_url = $thumbnail_url[0];
				else
					continue;
			
				?>
				<li class="banner">
					<img src="<?php echo $thumbnail_url; ?>" />
					<input type="hidden" name="banner_id[]" value="<?php echo htmlentities($option['id']); ?>" />
					<label>URL</label>
					<input type="text" name="banner_url[]" value="<?php echo htmlentities($option['url']); ?>" />
					<label>ALT</label>
					<input type="text" name="banner_alt[]" value="<?php echo htmlentities(stripslashes($option['alt'])); ?>" />
				</li>
				<?php
			}

			?>
				</ol>
				
				<input type="hidden" name="action" value="save-options" />
				<input type="submit" value="Save Slides" class="update-button" />
			</form>
		</div><!-- .slider-list -->
		</div><!-- slider-banner-list -->
		
		</div><!-- #banner-editor -->
		
		<?php
	}


	/**
	 *
	 */	
	private static function process_post()
	{
		if( !isset($_POST) || !isset($_POST['action']) )
			return;
		
		switch( $_POST['action'] )
		{
			case 'upload-file':
				self::save_file();
				break;
				
			case 'save-options':
				self::save_options();
				break;
		}		
	}
	
	
	/**
	 *
	 */
	private static function save_file()
	{
		global $nh_config;
		$overrides = array('test_form' => false);

		$uploaded_file = $_FILES['import'];
		list($width, $height) = getimagesize( $uploaded_file['tmp_name'] );
		if( $width != self::$config['width'] || $height != self::$config['height'] )
		{
			?>
			<div class="error"><p>
			The banner must be <?php echo self::$config['width']; ?> x <?php echo self::$config['height']; ?>.
			The uploaded banner is <?php echo $width; ?> x <?php echo $height; ?>.
			The upload has been cancelled.
			</p></div>
			<?php
			
			return;
		}

		$wp_filetype = wp_check_filetype_and_ext( $uploaded_file['tmp_name'], $uploaded_file['name'], false );
		if ( ! wp_match_mime_types( 'image', $wp_filetype['type'] ) )
			wp_die( __( 'The uploaded file is not a valid image. Please try again.' ) );

		$file = wp_handle_upload($uploaded_file, $overrides);

		if ( isset($file['error']) )
			wp_die( $file['error'],  __( 'Image Upload Error' ) );

		$url = $file['url'];
		$type = $file['type'];
		$file = $file['file'];
		$filename = basename($file);

		// Construct the object array
		$object = array(
			'post_title'     => $filename,
			'post_content'   => $url,
			'post_mime_type' => $type,
			'guid'           => $url,
			'context'        => 'banner'
		);

		// Save the data
		$attachment_id = wp_insert_attachment( $object, $file );
		
		if( !$attachment_id )
		{
			?>
			<div class="error"><p>An error occurred while inserting the banner into the database.</p></div>
			<?php
			
			return;
		}
		
		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file );
		wp_update_attachment_metadata( $attachment_id, $attachment_data );
		
		?>
		<div class="updated"><p>The banner was successfully added.</p></div>
		<?php
	}
	
	
	/**
	 *
	 */
	private static function save_options()
	{
		global $nh_config;
		$slider_banners = array();
		
		$count = 0;
		foreach( $_POST['banner_id'] as $banner_id )
		{
			$slider_banners[$count] = array();
			$slider_banners[$count]['id'] = $banner_id;
			$count++;
		}

		$count = 0;
		foreach( $_POST['banner_url'] as $banner_url )
		{
			$slider_banners[$count]['url'] = $banner_url;
			$count++;
		}
		
		$count = 0;
		foreach( $_POST['banner_alt'] as $banner_alt )
		{
			$slider_banners[$count]['alt'] = $banner_alt;
			$count++;
		}
		
		$banner_options = $nh_config->set_option_value( 'banner', 'images', $slider_banners );
		$nh_config->save_options();
	}

}

