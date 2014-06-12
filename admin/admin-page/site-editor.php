<?php

/**
 *
 */
class NH_AdminPage_SiteEditor extends NH_AdminPage
{

	private static $_instance = null;
	

	/* Default private constructor. */
	private function __construct( $slug )
	{
		$this->slug = $slug;
	}
	
	
	
	/**
	 *
	 */	
	public static function get_instance( $slug )
	{
		if( self::$_instance === null )
		{
			self::$_instance = new NH_AdminPage_SiteEditor( $slug );
		}
		
		return self::$_instance;
	}



	
	/**
	 * 
	 */
	public function enqueue_scripts()
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
	public function add_head_script()
	{
		?>
		<style>
		
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
		nh_print($page);
		nh_print($tab);
		nh_print($option);
		nh_print($input);
		
		return $options;
	}
	

	/**
	 *
	 */
	public function show()
	{
		global $nh_admin_pages;
		
		$tabs = array(
			'banner' => 'Banner',
			'front-page' => 'Front Page',
			'sidebar' => 'Sidebar',
			'news-rss' => 'News RSS Feed',
		);

		$tabs = apply_filters( $this->slug.'-tabs', $tabs );

        $tab = ( !empty($_GET['tab']) && array_key_exists($_GET['tab'], $tabs) ? $_GET['tab'] : apply_filters( $this->slug.'-default-tab', 'banner' ) );
		?>
		
		<div class="wrap">
	 
			<div id="icon-themes" class="icon32"></div>
			<h2><?php echo $nh_admin_pages[$this->slug]['title']; ?></h2>
			<?php settings_errors(); ?>
		 
			<h2 class="nav-tab-wrapper">
				<?php foreach( $tabs as $k => $t ): ?>
					<a href="?page=<?php echo $this->slug; ?>&tab=<?php echo $k; ?>" class="nav-tab <?php if($k==$tab) echo 'active'; ?>"><?php echo $t; ?></a>
				<?php endforeach; ?>
			</h2>
		
			<?php
			//nh_print( get_option($this->slug.':') );
			?>
				 
			<form method="post" action="options.php">
				<?php settings_fields( $this->slug ); ?>
				<input type="hidden" name="tab" value="<?php echo $tab; ?>" />
				<?php do_settings_sections( $this->slug.':'.$tab ); ?>
				<?php submit_button(); ?>
			</form>
		 
		</div><!-- /.wrap -->
		
		<?php
	}
	



	public function print_banner_section()
	{
		echo '<p>print_banner_section</p>';
		
		global $nh_config;
		
		$options = $nh_config->get_banner_images();

		if( $options === null ) $options = array();		
		?>
		
		<form id="upload-form" class="wp-upload-form" enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<label for="upload"><?php _e( 'Choose an image from your computer:' ); ?></label><br />
			<input type="file" id="upload" name="import" />
			<input type="hidden" name="action" value="upload-file" />
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
					<input type="hidden" 
					       name="<?php nh_input_name_e( $this->tab, 'banner', 'id', '' ); ?>" 
					       value="<?php echo htmlentities($option['id']); ?>" />

					<label>URL</label>
					<input type="text" 
					       name="<?php nh_input_name_e( $this->tab, 'banner', 'url', '' ); ?>" 
					       value="<?php echo htmlentities($option['url']); ?>" />

					<label>ALT</label>
					<input type="text" 
					       name="<?php nh_input_name_e( $this->tab, 'banner', 'alt', '' ); ?>" 
					       value="<?php echo htmlentities(stripslashes($option['alt'])); ?>" />

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

		$this->print_stories( 'news' );
	}


	private function print_stories( $name )
	{
		global $nh_config;

		$options = $nh_config->get_admin_options( $name );
		$num_stories_options = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 );
		
		nh_print( $options, $name );
				
		?>
		<?php
		
		foreach( $options['sections'] as $column => $sections ):
			
			foreach( $sections as $section_key ):
				$section = $nh_config->get_section_by_key( $section_key );
				?>

				<div class="section-header">

				<h3><?php echo $section->title; ?></h3>

				<div class="num-stories">
				<strong># of posts: &nbsp;</strong>
				<select name="<?php nh_input_name( $this->tab, 'num-stories', $section->key ); ?>">
					<?php foreach( $num_stories_options as $num ): ?>
					<option value="<?php echo $num; ?>" <?php echo ($num == $section->num_stories[$this->tab] ? 'selected' : ''); ?>><?php echo $num; ?></option>
					<?php endforeach; ?>
				</select>
				</div>
				
				</div>

				<?php for($i = 0; $i < $section->num_stories[$this->tab]; $i++): ?>
				
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
					
					<input type="hidden" name="<?php nh_input_name( $this->tab, 'stories', $section_key ); ?>" 
					                     value="<?php echo $post_id; ?>" 
					                     post_title="<?php echo $post_title; ?>"
					                     section="<?php echo $section_key; ?>"
					                     class="story-selector" />

				<?php endfor; ?>
		
			<?php endforeach; ?>

		<?php endforeach; ?>

		<?php
	}
		
}


