<?php

/**
 *
 */
class Exchange_BannerAdminPage
{
	private $messages;
	private $config;



	/**
	 *
	 */
	public function __construct()
	{
		global $exchange_config;
		$this->messages = array();
		$this->config = $exchange_config->get_banner_config();
	}
	
	
	
	/**
	 *
	 */
	public function show_page()
	{
		global $exchange_config;
		$this->process_post();
		
		$banner_options = new Exchange_BannerOptions;
		$options = $banner_options->get_options();
		
		$nonce = wp_create_nonce("exchange-banner-options-nonce");
		
		?>
		
		<div class="banner-editor">
		
		<h2>Banner Editor</h2>
		<div class="instructions">Instruction go here...</div>
		
		<input type="hidden" name="exchange-banner-options-nonce" value="<?php echo $nonce; ?>" />
		
		<form id="upload-form" class="wp-upload-form" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<label for="upload"><?php _e( 'Choose an image from your computer:' ); ?></label><br />
			<input type="file" id="upload" name="import" />
			<input type="hidden" name="action" value="upload-file" />
			<?php //wp_nonce_field( 'custom-header-upload', '_wpnonce-custom-header-upload' ); ?>
			<input type="submit" value="save slider" />
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
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/delete-icon.png" banner_id="<?php echo get_the_ID(); ?>" class="delete-button" />
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
				<input type="submit" value="Save Slides" />
			</form>
		</div><!-- .slider-list -->
		</div><!-- slider-banner-list -->
		
		</div><!-- .banner-editor -->
		
		<?php
	}



	/**
	 *
	 */	
	private function process_post()
	{
		if( !isset($_POST) || !isset($_POST['action']) )
			return;
		
		switch( $_POST['action'] )
		{
			case 'upload-file':
				$this->save_file();
				break;
				
			case 'save-options':
				$this->save_options();
				break;
		}		
	}
	

	
	/**
	 *
	 */
	private function save_file()
	{
		global $exchange_config;
		$overrides = array('test_form' => false);

		$uploaded_file = $_FILES['import'];
		list($width, $height) = getimagesize( $uploaded_file['tmp_name'] );
		if( $width != $this->config['width'] || $height != $this->config['height'] )
		{
			?>
			<div class="error"><p>
			The banner must be <?php echo $this->config['width']; ?> x <?php echo $this->config['height']; ?>.
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
	private function save_options()
	{
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
		
		$banner_options = new Exchange_BannerOptions;
		$banner_options->save_options( $slider_banners );
	}
}
