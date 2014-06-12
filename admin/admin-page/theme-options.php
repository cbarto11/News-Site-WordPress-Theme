<?php


/**
 *
 */
class NH_AdminPage_ThemeOptions extends NH_AdminPage
{

	private static $_instance = null;
	
	
	public $slug = null;
	public $tag = null;
	

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
			self::$_instance = new NH_AdminPage_ThemeOptions( $slug );
		}
		
		return self::$_instance;
	}


	
	/**
	 * 
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_style( 'google-jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css' );
		wp_enqueue_script( 'google-jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js' );
		wp_enqueue_media();
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
		
		.position > div {
			display:inline-block;
			width:50px;
			height:50px;
			background-color:#ccc;
		}
		
		.position > div.selected {
			background-color:#000;
		}
		
		</style>
  		<script type="text/javascript">
			jQuery(document).ready( function()
			{
				
					var custom_uploader;
 	
 					jQuery('.media-select').each( function()
 					{
 						var media_select = this;
 						
						jQuery(media_select).find('.select-image').click( function(e) 
						{
							e.preventDefault();
 
							//If the uploader object has already been created, reopen the dialog
							if (custom_uploader)
							{
								custom_uploader.open();
								return;
							}
 
							//Extend the wp.media object
							custom_uploader = wp.media.frames.file_frame = wp.media(
							{
								title: 'Choose Image',
								button: {
									text: 'Choose Image'
								},
								multiple: false
							});
 
							//When a file is selected, grab the URL and set it as the text field's value
							custom_uploader.on('select', function() 
							{
								attachment = custom_uploader.state().get('selection').first().toJSON();
								jQuery(media_select).find('.image-id').val(attachment.id);
								jQuery(media_select).find('img').attr('src', attachment.url);
							});
 
							//Open the uploader dialog
							custom_uploader.open();
						});					
 					
 					
 					});
					
					jQuery( 'input.number' ).each( function()
					{
						var min = parseInt(jQuery(this).attr('min'));
						var max = parseInt(jQuery(this).attr('max'));
						var step = parseInt(jQuery(this).attr('step'));
						var start = parseInt(jQuery(this).attr('start'));
					
						var values = {};
						
						if( !isNaN(min) ) values['min'] = min;
						if( !isNaN(max) ) values['max'] = max;
						if( !isNaN(step) ) values['step'] = step;
						if( !isNaN(start) ) values['start'] = start;
					
						jQuery(this).spinner( values ).attr( 'readonly', true );
					});
					
					
					
					jQuery( '.image-selector' ).each( function()
					{
						var image_selector = this;
						
						jQuery(image_selector).find('.selection-type').change( function()
						{
							if( this.checked )
							{
								switch( jQuery(this).val() )
								{
									case 'relative':
										jQuery(image_selector).find('.relative-path').show();
										jQuery(image_selector).find('.media-select').hide();
										break;
									case 'media':
										jQuery(image_selector).find('.relative-path').hide();
										jQuery(image_selector).find('.media-select').show();
										break;
								}
							}
						});
						
						jQuery(image_selector).find('.selection-type:checked').change();
						
						jQuery(image_selector).find('.use-site-url').change( function()
						{
							if( this.checked )
							{
								jQuery(image_selector).find('.link-url').hide();
							}
							else
							{
								jQuery(image_selector).find('.link-url').show();
							}
						}).change();
					});
					
					
					jQuery('.position').each( function()
					{
						var container = this;
						
						jQuery(container).find('input').attr('readonly', true);
						var position = jQuery(container).find('input').val();
						
						var v = [ 'vtop', 'vcenter', 'vbottom' ];
						var h = [ 'hleft', 'hcenter', 'hright' ];
						
						for( var r = 0; r < 3; r++ )
						{
							for( var c = 0; c < 3; c++ )
							{
								var pos = h[c]+' '+v[r];
								var cls = '';
								if( position == pos ) cls = 'selected';
								jQuery(container).append('<div position="'+pos+'" class="'+cls+'"></div>');
							}
							jQuery(container).append('<br/>');
						}
						
						jQuery(container).find('div').click( function()
						{
							jQuery(container).find('div').removeAttr('class');
							jQuery(this).attr('class', 'selected');
							jQuery(container).find('input').val( jQuery(this).attr('position') );
						});
					});
					
					
					
					jQuery('.checkbox-section').each( function()
					{
						var checkbox_section = this;
						
						jQuery(checkbox_section).find('input[type=checkbox]').change( function()
						{
							if( this.checked )
							{
								jQuery(checkbox_section).find('.section').hide();
							}
							else
							{
								jQuery(checkbox_section).find('.section').show();
							}
						}).change();
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
			'variations', 'Variations', array( $this, 'print_variations_section' ),
			$this->slug.':variations'
		);

		add_settings_section(
			'general', 'General', array( $this, 'print_general_section' ),
			$this->slug.':general'
		);
		
		add_settings_section(
			'header', 'Header', array( $this, 'print_header_section' ), 
			$this->slug.':header'
		);

		add_settings_section(
			'banner', 'Banner', array( $this, 'print_banner_section' ), 
			$this->slug.':banner'
		);
		
		add_settings_section(
			'subheader', 'Subheader', array( $this, 'print_subheader_section' ), 
			$this->slug.':subheader'
		);
		
		add_settings_section(
			'mobile-menu', 'Mobile Menu', array( $this, 'print_mobile_menu_section' ), 
			$this->slug.':mobile-menu'
		);

		add_settings_section(
			'main', 'Main', array( $this, 'print_main_section' ), 
			$this->slug.':main'
		);

		add_settings_section(
			'content', 'Content', array( $this, 'print_content_section' ), 
			$this->slug.':content'
		);

		add_settings_section(
			'sidebar', 'Sidebar', array( $this, 'print_sidebar_section' ), 
			$this->slug.':sidebar'
		);
		
		add_settings_section(
			'footer', 'Footer', array( $this, 'print_footer_section' ), 
			$this->slug.':footer'
		);
	}
	
	
	/**
	 *
	 */
	public function add_settings_fields()
	{
		//
		// Variations
		//
		
		add_settings_field( 
			'variation', 'Current Variation', array( $this, 'print_variation_list' ),
			$this->slug.':variations', 'variations', array(  )
		);

		//
		// General
		//
		
		add_settings_field( 
			'custom-post-types', 'Custom Post Types', array( $this, 'print_custom_post_types' ),
			$this->slug.':general', 'general', array(  )
		);
		
		//
		// Header
		//
	 
		add_settings_field( 
			'show-part', 'Show?', array( $this, 'print_show_part' ),
			$this->slug.':header', 'header', array( 'header' )
		);
		add_settings_field( 
			'widgets', 'Widget Areas', array( $this, 'print_widgets' ),
			$this->slug.':header', 'header', array( 'header' )
		);
		add_settings_field( 
			'image', 'Image', array( $this, 'print_image' ),
			$this->slug.':header', 'header', array( 'header', 'image' )
		);
		add_settings_field( 
			'title', 'Title', array( $this, 'print_header_title' ),
			$this->slug.':header', 'header', array( 'header' )
		);
		
		//
		// Banner
		//
		
		add_settings_field( 
			'show-part', 'Show?', array( $this, 'print_show_part' ),
			$this->slug.':banner', 'banner', array( 'banner' )
		);
		add_settings_field( 
			'widgets', 'Widget Areas', array( $this, 'print_widgets' ),
			$this->slug.':banner', 'banner', array( 'banner' )
		);
		add_settings_field( 
			'max-slides', '# of Slides', array( $this, 'print_num_slides' ),
			$this->slug.':banner', 'banner', array( 'banner' )
		);
		add_settings_field( 
			'size', 'Size', array( $this, 'print_size' ),
			$this->slug.':banner', 'banner', array( 'banner' )
		);
		
		//
		// Subheader
		//
		
		add_settings_field( 
			'show-part', 'Show?', array( $this, 'print_show_part' ),
			$this->slug.':subheader', 'subheader', array( 'subheader' )
		);
		add_settings_field( 
			'widgets', 'Widget Areas', array( $this, 'print_widgets' ),
			$this->slug.':subheader', 'subheader', array( 'subheader' )
		);
		add_settings_field( 
			'image', 'Image', array( $this, 'print_image' ),
			$this->slug.':subheader', 'subheader', array( 'subheader', 'image' )
		);
		
		//
		// Mobile Menu
		//
		
		add_settings_field( 
			'show-part', 'Show?', array( $this, 'print_show_part' ),
			$this->slug.':mobile-menu', 'mobile-menu', array( 'mobile-menu' )
		);
		add_settings_field( 
			'widgets', 'Widget Areas', array( $this, 'print_widgets' ),
			$this->slug.':mobile-menu', 'mobile-menu', array( 'mobile-menu' )
		);
		add_settings_field( 
			'menu-tabs', 'Menu Tabs', array( $this, 'print_mobile_menu_widgets' ),
			$this->slug.':mobile-menu', 'mobile-menu', array( 'mobile-menu' )
		);
		
		//
		// Main
		//
		
		add_settings_field( 
			'widgets', 'Widget Areas', array( $this, 'print_widgets' ),
			$this->slug.':main', 'main', array( 'main' )
		);
		
		//
		// Content
		//
		
		add_settings_field( 
			'widgets', 'Widget Areas', array( $this, 'print_widgets' ),
			$this->slug.':content', 'content', array( 'content' )
		);
		
		//
		// Sidebar
		//
		
		add_settings_field( 
			'widget', 'Widget Areas', array( $this, 'print_widgets' ),
			$this->slug.':sidebar', 'sidebar', array( 'sidebar' )
		);
		
		//
		// Footer
		//
		
		add_settings_field( 
			'show-part', 'Show?', array( $this, 'print_show_part' ),
			$this->slug.':footer', 'footer', array( 'footer' )
		);
		add_settings_field( 
			'widgets', 'Widget Areas', array( $this, 'print_widgets' ),
			$this->slug.':footer', 'footer', array( 'footer' )
		);
		add_settings_field( 
			'column-widgets', '', array( $this, 'print_footer_widgets' ),
			$this->slug.':footer', 'footer', array( 'footer' )
		);
		add_settings_field( 
			'copyright', 'Copyright', array( $this, 'print_copyright' ),
			$this->slug.':footer', 'footer', array( 'footer' )
		);
		
	}
	
	
	/**
	 *
	 */
	public function process_input( $options, $page, $tab, $option, $input )
	{
		if( $option !== 'nh-options' ) return $options;
		
		global $nh_config;
		
		if( !array_key_exists($tab, $input) ) return $options;
		$tab_input = $input[$tab];
		
		$tab_input = array_map( 'nh_string_to_value', $tab_input );
// 		nh_print($tab_input);
	
		switch( $tab )
		{
			case 'variations':
				// [variation]
				if( isset($tab_input['variation']) ):
					$variations = $nh_config->get_variations();
					$chosen_variation = $tab_input['variation'];
					if( (!in_array($chosen_variation, $variations)) && ($chosen_variation !== 'default') )
					{
						add_settings_error( '', '', 'Invalid variation: '.$chosen_variation );
						return $options;
					}
					$nh_config->set_variation( $chosen_variation );
				endif;
				
				// [reset-options]
				if( isset($tab_input['reset-options']) ):
					add_settings_error( '', '', 'Settings saved.', 'updated' );
					add_settings_error( '', '', 'Reset options for variation: '.$chosen_variation, 'updated' );
					return array();
				endif;
				
				break;
			
			case 'general':
				// [custom-post-type]
				// converted in nh_string_to_value
				
				break;
			
			case 'header':
				// [show-part]
				// [widget]
				// converted in nh_string_to_value
				
				// [image][selection-type]
				// [image][attachment-id]
				// [image][path]
				// [image][key]
				// [image][title]
				// [image][link]
				// nothing to do

				// [image][use-site-link]
				// converted in nh_string_to_value

				// [title][position]
				// [title][text]
				// [title][link]
				// nothing to do

				// [title][use-site-link]
				// converted in nh_string_to_value

				// [description][text]
				// [description][link]
				// nothing to do

				// [description][use-site-link]
				// converted in nh_string_to_value

				break;
			
			case 'banner':
				// [show-part]
				// [widget]
				// converted in nh_string_to_value

				// [max-slides]
				if( isset($tab_input['max-slides']) ):
					$tab_input['max-slides'] = intval( $tab_input['max-slides'] );
				endif;
				
				// [width]
				if( isset($tab_input['width']) ):
					$tab_input['width'] = intval( $tab_input['width'] );
				endif;
				
				// [height]
				if( isset($tab_input['height']) ):
					$tab_input['height'] = intval( $tab_input['height'] );
				endif;
				
				break;
			
			case 'subheader':
				// [show-part]
				// [widget]
				// converted in nh_string_to_value
				
				// [image][selection-type]
				// [image][attachment-id]
				// [image][path]
				// [image][key]
				// [image][title]
				// [image][link]
				// nothing to do

				// [image][use-site-link]
				// converted in nh_string_to_value				
				
				break;
			
			case 'mobile-menu':
				// [show-part]
				// [widget]
				// converted in nh_string_to_value
				
  				// [menu-widget]
  				// nothing to do
				
				break;
			
			case 'main':
				// [widget]
				// converted in nh_string_to_value

				break;
			
			case 'content':
				// [widget]
				// converted in nh_string_to_value

				break;
			
			case 'sidebar':
				// [widget]
				// converted in nh_string_to_value


				break;
			
			case 'footer':
				// [show-part]
				// [widget]
				// converted in nh_string_to_value
				
				// [copyright]
				// nothing to do

				break;
		}
		
// 		nh_print( 'tab_input altered' );
// 		nh_print( array($tab => $tab_input) );
// 		
// 		nh_print( 'new options' );
// 		nh_print( array_merge( $options, array($tab => $tab_input) ) );
		
		return array_merge( $options, array($tab => $tab_input) );
	}
	
	
	/**
	 *
	 */
	public function show()
	{
		global $nh_admin_pages;
		
		$tabs = array(
			'variations' => 'Variations',
			'general' => 'General',
			'header' => 'Header',
			'banner' => 'Banner',
			'subheader' => 'Subheader',
			'mobile-menu' => 'Mobile Menu',
			'main' => 'Main',
			'content' => 'Content',
			'sidebar' => 'Sidebar',
			'footer' => 'Footer',
		);
		
		$tabs = apply_filters( $this->slug.'-tabs', $tabs );
		
        $this->tab = ( !empty($_GET['tab']) && array_key_exists($_GET['tab'], $tabs) ? $_GET['tab'] : apply_filters( $this->slug.'-default-tab', 'variations' ) );
		?>
		
		<div class="wrap">
	 
			<div id="icon-themes" class="icon32"></div>
			<h2><?php echo $nh_admin_pages[$this->slug]['title']; ?></h2>
			<?php settings_errors(); ?>
		 
			<h2 class="nav-tab-wrapper">
				<?php foreach( $tabs as $k => $t ): ?>
					<a href="?page=<?php echo $this->slug; ?>&tab=<?php echo $k; ?>" class="nav-tab <?php if($k==$this->tab) echo 'active'; ?>"><?php echo $t; ?></a>
				<?php endforeach; ?>
			</h2>
		
			<form method="post" action="options.php">
				<?php settings_fields( $this->slug ); ?>
				<input type="hidden" name="tab" value="<?php echo $this->tab; ?>" />
				<?php do_settings_sections( $this->slug.':'.$this->tab ); ?>
				<?php submit_button(); ?>
			</form>
		 
		</div><!-- /.wrap -->
		
		<?php
	}
	



	//
	// Section functions
	//

	
	
	public function print_variations_section()
	{
		echo '<p>print_variations_section</p>';
	}
	
	public function print_general_section()
	{
		echo '<p>print_general_section</p>';
	}
		
	public function print_header_section()
	{
		echo '<p>print_header_section</p>';
	}
	
	public function print_banner_section()
	{
		echo '<p>print_banner_section</p>';
	}
		
	public function print_subheader_section()
	{
		echo '<p>print_subheader_section</p>';
	}
		
	public function print_mobile_menu_section()
	{
		echo '<p>print_mobile_menu_section</p>';
	}

	public function print_main_section()
	{
		echo '<p>print_main_section</p>';
	}

	public function print_content_section()
	{
		echo '<p>print_content_section</p>';
	}

	public function print_sidebar_section()
	{
		echo '<p>print_sidebar_section</p>';
	}
		
	public function print_footer_section()
	{
		echo '<p>print_footer_section</p>';
	}
	



	//
	// Fields functions
	//





	public function print_variation_list( $args )
	{
		global $nh_config;
		
		$current_variation = $nh_config->get_current_variation();
		$variations = $nh_config->get_variations();
		?>
		
		<select name="<?php nh_input_name_e( $this->tab, 'variation' ); ?>">
			<option value="default" 
			        <?php selected( 'default', $current_variation ); ?>>
			    default
			</option>
		
		<?php foreach( $variations as $variation ): ?>
			<option value="<?php echo $variation; ?>" 
			        <?php selected( $variation, $current_variation); ?>>
				<?php echo $variation; ?>
			</option>
		<?php endforeach; ?>
		
		</select>
		
		<div>
		<input type="checkbox" 
		       name="<?php nh_input_name_e( $this->tab, 'reset-options' ); ?>" 
		       value="reset-options" />
		Reset options?
		</div>
		
		<?php		
	}
	
	
	public function print_custom_post_types( $args )
	{
		global $nh_config;
		
		$custom_post_types = $nh_config->get_custom_post_types();
		?>
		
		<?php foreach( $custom_post_types as $type ): ?>
			<div class="custom-post-type">
			    <input type="hidden" 
			           name="<?php nh_input_name_e( $this->tab, 'custom-post-type', $type ); ?>" 
			           value="b:false" />
			    <input type="checkbox" 
			           name="<?php nh_input_name_e( $this->tab, 'custom-post-type', $type ); ?>" 
			           value="b:true" 
			           <?php checked( true, $nh_config->use_custom_post_type($type) ); ?> />
			    <label><?php echo $type; ?></label>
		    </div>
	    <?php endforeach; ?>
		
		<?php
	}
	
	public function print_show_part( $args )
	{
		global $nh_config;
		?>
	    
	    <input type="hidden"   
	           name="<?php nh_input_name_e( $this->tab, 'show-part' ); ?>" 
	           value="b:false" />
	    <input type="checkbox" 
	           name="<?php nh_input_name_e( $this->tab, 'show-part' ); ?>" 
	           value="b:true" <?php checked(true, $nh_config->show_template_part($args[0]), true); ?> />
	    <label>Show template part?</label>
		
		<?php
	}
	
	public function print_widgets( $args )
	{
		global $nh_config;
		?>
	    
	    <input type="hidden" 
	           name="<?php nh_input_name_e( $this->tab, 'widget', 'top' ); ?>" 
	           value="b:false" />
	    <input type="checkbox" 
	           name="<?php nh_input_name_e( $this->tab, 'widget', 'top' ); ?>"
	           value="b:true" 
	           <?php checked(true, $nh_config->use_widget( $this->tab, 'top' ), true); ?> />
	    <label>Top</label>
	    
	    <input type="hidden" 
	           name="<?php nh_input_name_e( $this->tab, 'widget', 'bottom' ); ?>" 
	           value="b:false" />
	    <input type="checkbox" 
	           name="<?php nh_input_name_e( $this->tab, 'widget', 'bottom' ); ?>"
	           value="b:true" 
	           <?php checked(true, $nh_config->use_widget( $this->tab, 'bottom' ), true); ?> />
	    <label>Bottom</label>
		
		<?php
	}
	
	
	public function print_mobile_menu_widgets( $args )
	{
		global $nh_config;
		$widget_areas = $nh_config->get_mobile_widget_areas();
		
		$wa = array();
		for( $i = 0; $i < 3; $i++ ) $wa[] = false;
		foreach( $widget_areas as $area )
		{
			if( $area['index'] < 0 && $area['index'] >= count($wa) ) continue;
			$wa[$area['index']] = $area;
		}
		
		for( $i = 0; $i < 3; $i++ ): ?>
			<input type="text" 
			       name="<?php nh_input_name_e( $this->tab, 'menu-widget', $i ); ?>" 
			       value="<?php echo !empty($wa[$i]['name']) ? $wa[$i]['name'] : '' ?>" /><br/>
		<?php endfor;
	}
	
	
	public function print_image( $args )
	{
		global $nh_config;

		$image = $nh_config->get_value( $args );
		
		$image_type_selection = array(
			'relative' => 'Relative Path',
			'media' => 'Media Library Image',
		);
		
		if( !array_key_exists($image['selection-type'], $image_type_selection) )
			$image['selection-type'] = 'relative';
		?>

		<div class="image-selector">
		
		<?php foreach( $image_type_selection as $key => $value ): ?>
			<input type="radio"
			       class="selection-type" 
			       name="<?php nh_input_name_e( $args, 'selection-type' ); ?>"
			       value="<?php echo $key; ?>" 
			       <?php checked($key, $image['selection-type'], true); ?> />
			<?php echo $value; ?>
			<br/>
		<?php endforeach; ?>
		
		
		<div class="media-select">
			<input type="hidden" 
			       class="image-id" 
			       name="<?php nh_input_name_e( $args, 'attachment-id' ); ?>" 
			       value="<?php echo $image['attachment-id']; ?>" />
			<input type="button" 
			       value="Select Image" 
			       class="select-image button" />
			
			<div class="image">
				<?php
				$image_tag = wp_get_attachment_image( $image['attachment-id'], 'full' ); 
				if( $image_tag === '' ) $image_tag = '<img src="" />';
				echo $image_tag;
				?>
			</div>
		</div>
		
		<div class="relative-path">
			<label>Path</label>
			<input type="text" 
			       name="<?php nh_input_name_e( $args, 'path' ); ?>" 
			       value="<?php echo $image['path']; ?>" />
			<br/>
		</div>
		
		<label>Class</label>
		<input type="text" 
		       name="<?php nh_input_name_e( $args, 'class' ); ?>" 
		       value="<?php echo $image['class']; ?>" />
		<br/>
		
		<label>Title</label>
		<input type="text" 
		       name="<?php nh_input_name_e( $args, 'title' ); ?>" 
		       value="<?php echo $image['title']; ?>" />
		<br/>
		
		<div class="checkbox-section">
			<label>Link</label>
		    <input type="hidden" 
		           name="<?php nh_input_name_e( $args, 'use-site-link' ); ?>" 
		           value="b:false" />
			<input type="checkbox" 
			       name="<?php nh_input_name_e( $args, 'use-site-link' ); ?>"  
			       class="use-site-url" 
			       value="b:true" 
			       <?php checked(true, $image['use-site-link'], true); ?> />
			
			<label>use site URL</label>
			<div class="section">
				<input type="text" 
				       name="<?php nh_input_name_e( $args, 'link' ); ?>" 
				       class="link-url" 
				       value="<?php echo $image['link']; ?>" />
			</div>
		</div>
		
		</div>
		<?php
	}
	
	public function print_header_title( $args )
	{
		global $nh_config;
		
		$title = $nh_config->get_value( 'header','title' );
		$description = $nh_config->get_value( 'header','description' );
		
// 		nh_print($title, 'title');
		?>
		
	    <label>Position</label>
	    <div class="position">
			<input type="text"
			       name="<?php nh_input_name_e( $this->tab, 'title', 'position' ); ?>"
			       value="<?php echo $title['position']; ?>" />
		</div>
	    
	    <label>Text</label>
		<input type="text" 
		       id="title-text" 
		       name="<?php nh_input_name_e( $this->tab, 'title', 'text' ); ?>" 
		       value="<?php echo $title['text']; ?>" />
		<br/>

		<div class="checkbox-section">
			
			<label>URL</label>
			<input type="hidden" 
			       name="<?php nh_input_name_e( $this->tab, 'title', 'use-site-link' ); ?>" 
			       value="b:false" />
			<input type="checkbox" 
			       name="<?php nh_input_name_e( $this->tab, 'title', 'use-site-link' ); ?>" 
			       class="use-site-url" 
			       value="b:true" 
			       <?php checked(true, $title['use-site-link'], true); ?> />
			
			<label>use site URL</label>
			<div class="section">
				<input type="text"
				       name="<?php nh_input_name_e( $this->tab, 'title', 'link' ); ?>" 
				       value="<?php echo $title['link']; ?>" />
			</div>
		</div>

	    <label for="description-text">Text</label>
		<input type="text" 
		       id="description-text" 
		       name="<?php nh_input_name_e( $this->tab, 'description', 'text' ); ?>" 
		       value="<?php echo $description['text']; ?>" />
		<br/>

		<div class="checkbox-section">
			
			<label for="description-url">URL</label>
			<input type="hidden" 
			       name="<?php nh_input_name_e( $this->tab, 'description', 'use-site-link' ); ?>" 
			       value="b:false" />
			<input type="checkbox" 
			       name="<?php nh_input_name_e( $this->tab, 'description', 'use-site-link' ); ?>" 
			       class="use-site-url" 
			       value="b:true" 
			       <?php checked(true, $description['use-site-link'], true); ?> />
			
			<label>use site URL</label>
			<div class="section">
				<input type="text"
				       name="<?php nh_input_name_e( $this->tab, 'description', 'link' ); ?>" 
				       value="<?php echo $description['link']; ?>" />
				<br/>
			</div>
			
		</div>

		<?php
	}
	
	public function print_num_slides( $args )
	{
		global $nh_config;
		
		$num_slides = $nh_config->get_value('banner', 'max-slides');
		
		?>
		<input type="text" 
		       class="number" 
		       name="<?php nh_input_name_e( $this->tab, 'max-slides' ); ?>"
		       value="<?php echo $num_slides; ?>" min="-1" />
		<?php
	}
	
	public function print_size( $args )
	{
		global $nh_config;
		
		$width = $nh_config->get_value('banner', 'width');
		$height = $nh_config->get_value('banner', 'height');
		
		?>
		<input type="text" 
		       class="number" 
		       name="<?php nh_input_name_e( $this->tab, 'width' ); ?>" 
		       value="<?php echo $width; ?>" 
		       min="-1" 
		       step="10" />
		&nbsp;x&nbsp;
		<input type="text" 
		       class="number" 
		       name="<?php nh_input_name_e( $this->tab, 'height' ); ?>" 
		       value="<?php echo $height; ?>" 
		       min="-1" />
		<?php
	}
	
	public function print_footer_widgets( $args )
	{
		global $nh_config;
		
		for( $i = 0; $i < 4; $i++ ): ?>
	    
	    <input type="hidden" 
	           name="<?php nh_input_name_e( $this->tab, 'widget', 'column-'.($i+1) ); ?>" 
	           value="b:false" />
	    <input type="checkbox" 
	           id="widget-top" 
	           name="<?php nh_input_name_e( $this->tab, 'widget', 'column-'.($i+1) ); ?>" 
	           value="b:true" 
	           <?php checked(true, $nh_config->use_widget( $this->tab, 'column-'.($i+1) )); ?> />
	    <label for="show-part">Column <?php echo ($i+1); ?></label>
	    
		<?php endfor;
	}
	
	public function print_copyright( $args )
	{
		global $nh_config;
		
		$copyright = $nh_config->get_value('footer','copyright');
		?>
		
		<input type="text" 
		       id="copyright" 
		       name="<?php nh_input_name_e( $this->tab, 'copyright' ); ?>" 
		       value="<?php echo $copyright; ?>" />
		
		<?php
	}
		
}


