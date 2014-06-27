<?php


/**
 *
 */
class NH_AdminPage_Design extends NH_AdminPage
{

	private static $_instance = null;
	
	
	public $slug = null;
	public $tabs = array();
	public $tab = null;
	

	
	//------------------------------------------------------------------------------------
	// Constructor.
	// Setup the page's slug and tabs.
	//------------------------------------------------------------------------------------
	private function __construct( $slug )
	{
		global $nh_config;

		$this->slug = $slug;
		
		$this->tabs = array(
			'variations' => 'Variations',
			'templates' => 'Templates',
			'widgets' => 'Widgets',
			'general' => 'General',
			'header' => 'Header',
			'banner' => 'Banner',
			'subheader' => 'Subheader',
			'mobile-menu' => 'Mobile Menu',
		);
		$this->tabs = apply_filters( $this->slug.'-tabs', $this->tabs );
		
		foreach( $this->tabs as $tab_name => $tab_title )
		{
			if( !$nh_config->show_template_part($tab_name) )
			    unset($this->tabs[$tab_name]);
		}
		
        $this->tab = ( !empty($_GET['tab']) && array_key_exists($_GET['tab'], $this->tabs) ? $_GET['tab'] : apply_filters( $this->slug.'-default-tab', 'variations' ) );		
	}
	
	
	
	//------------------------------------------------------------------------------------
	// Create or get the current instance of this page.
	//------------------------------------------------------------------------------------
	public static function get_instance( $slug )
	{
		if( self::$_instance === null )
		{
			self::$_instance = new NH_AdminPage_Design( $slug );
		}
		
		return self::$_instance;
	}



//========================================================================================
//=============================================================== Scripts and Styles =====

	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function enqueue_scripts()
	{
		wp_enqueue_style( 'google-jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css' );
		wp_enqueue_script( 'google-jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js' );
		wp_enqueue_media();
	}
	
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function add_head_script()
	{
		?>
		<style>
		
		.nav-tab.active {
			color:#000;
			background-color:#fff;
		}
		
		.position-controller {
			display:block;
			clear:both;
			text-align:center;
			border:solid 1px #000;
			background-color:#fff;
			padding:0px 5px;
		}
		
		.position-controller > div {
			display:inline-block;
			width:20%;
			height:30px;
			border:solid 1px #ccc;
			background-color:#eee;
			margin:10px 5px;
			cursor:pointer;
		}
		
		.position-controller > div.selected {
			border:solid 1px #000;
		}
		
		.position-controller > div:hover {
			background-color:#ffc;
		}
		
		.position-controller .hleft {
			float:left;
		}

		.position-controller .hright {
			float:right;
		}
		
		.position-controller > div.selected {
			background-color:#000;
		}
		
		.top-submit {
			float:right;
		}
		
		input.no-border {
			border:none;
			outline:none;
			box-shadow:none;
			background:transparent;
		}
		
		
		.widget-areas {
			width: 55%;
			display: inline-block;
			vertical-align: top;
			margin-right:30px;
		}
		
		.template-parts {
			display:inline-block;
			vertical-align:top;
			margin-right:30px;
		}


		/** Site Layout **/

		.site-layout {
			width:40%;
			display:inline-block;
		}
		
		.site-layout .site {
			margin:5px auto;
		}
		
		.site-layout div {
			border:solid 1px black;
			margin:5px;
		}

		.site-layout .site {
			padding:5px;
		}

		/*  Template Parts  */
		
		.site-layout .part {
			background-color:#fff;
			position:relative;
		}

		.site-layout .main-parts-container {
			text-align:center;
			border:none;
			margin:0;			
		}
		
		.site-layout .main .part {
			display:inline-block;
			margin:0;
		}

		.site-layout .main .content {
			width:70%;
		}
		
		.site-layout .main .sidebar {
			width:25%;
		}
		
		.site-layout .title {
			font-size:0.8em;
			line-height:50%;
			color:#666;
			text-align:center;
			border:none;
			z-index:10;
			position:absolute;
			top:50%; left:0;
			width:100%;
			margin:0;
		}
		
		/*  Widget Areas  */
		
		.tab-templates .site-layout .widget {
			visibility:hidden !important;
		}
		
		.site-layout .widget {
			height:10px;
			background-color:#eee;
			border-color:#aaa;
		}
		
		/*  Top Widgets  */
		
		.site-layout .widget.site-top,
		.site-layout .widget.header-top,
		.site-layout .widget.banner-top,
		.site-layout .widget.subheader-top,
		.site-layout .widget.mobile-menu-top,
		.site-layout .widget.main-top,
		.site-layout .widget.content-top,
		.site-layout .widget.sidebar-top,
		.site-layout .widget.footer-top {
		}

		.site-layout .widget.main-top,
		.site-layout .widget.main-bottom {
			clear:both;
		}
		
		/*  Bottom Widgets  */
		
		.site-layout .widget.site-bottom {
		}
		
		.site-layout .widget.header-bottom,
		.site-layout .widget.subheader-bottom,
		.site-layout .widget.mobile-menu-bottom,
		.site-layout .widget.main-bottom,
		.site-layout .widget.footer-bottom {
			margin-top:16px;
		}

		.site-layout .widget.banner-bottom {
			margin-top:36px;
		}

		.site-layout .widget.content-bottom,
		.site-layout .widget.sidebar-bottom {
			margin-top:66px;
		}

		.site-layout .widget.main-bottom,
		.site-layout .widget.footer-bottom {
			margin-top:0px;
		}
		
		/*  Footer Widgets  */
		
		.site-layout .footer-columns-container {
			text-align:center;
			border:none;
			margin:0;
		}
		
		.site-layout .widget.footer-column-1,
		.site-layout .widget.footer-column-2,
		.site-layout .widget.footer-column-3,
		.site-layout .widget.footer-column-4 {
			display:inline-block;
			width:20%;
			height:50px;
		}
		
		/*  jQuery CSS  */
		
		.site-layout .part.hide {
			//display:none;
			visibility:hidden;
			border:solid 1px #ccc;
		}
		
		.site-layout .part.show {
			background-color:#fff;
			visibility:visible;
			border:solid 1px #000;
		}
		
		.site-layout .part.highlight {
			//display:block;
			visibility:visible;
		}

		.tab-templates .site-layout .part.show div {
			visibility:visible;
		}

		.site-layout .highlight {
			background-color:#ffc !important;
		}

		.site-layout .widget.hide {
			visibility:hidden;
			border:solid 1px #ccc;
		}

		.site-layout .widget.show {
			visibility:visible;
			border:solid 1px #000;
		}

		.site-layout .widget.highlight {
			background-color:#ffc;
			visibility:visible !important;
		}

		.tab-widgets .site-layout .part {
			border:solid 1px #999;
		}
		
		</style>
  		<script type="text/javascript">
			jQuery(document).ready( function()
			{
				
				// 
				// Media Library Selector
				// 
				
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
				
				
				// 
				// Slider for number input fields
				// 
				
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
				
					jQuery(this).spinner( values );//.attr( 'readonly', true );
				});
				
				
				// 
				// Image selector
				// 
				
				jQuery('.image-selector').each( function()
				{
					var selector_id = jQuery(this).attr('selector-id');
					
					jQuery(this).find('.selection-type').change( function()
					{
						if( this.checked )
						{
							switch( jQuery(this).val() )
							{
								case 'relative':
									jQuery('.relative-path').filter('[selector-id='+selector_id+']').parent().parent().show();
									jQuery('.media-select').filter('[selector-id='+selector_id+']').parent().parent().hide();
									break;
								case 'media':
									jQuery('.relative-path').filter('[selector-id='+selector_id+']').parent().parent().hide();
									jQuery('.media-select').filter('[selector-id='+selector_id+']').parent().parent().show();
									break;
							}
						}
					}).change();
				});
				
				
				// 
				// Header title box position
				// 

				jQuery('.position').each( function()
				{
					var self = this;
					
					jQuery(self).find('input').attr('readonly', true).addClass('no-border');
					var position = jQuery(self).find('input').val();
					var container = jQuery('<div class="position-controller"></div>');
					
					var v = [ 'vtop', 'vcenter', 'vbottom' ];
					var h = [ 'hleft', 'hcenter', 'hright' ];
					
					for( var r = 0; r < 3; r++ )
					{
						for( var c = 0; c < 3; c++ )
						{
							var pos = h[c]+' '+v[r];
							var cls = pos;
							if( position == pos ) cls += ' selected';
							jQuery(container).append('<div position="'+pos+'" class="'+cls+'"></div>');
						}
						jQuery(container).append('<br/>');
					}
					
					jQuery(container).find('div').click( function()
					{
						jQuery(container).find('div').removeClass('selected');
						jQuery(this).addClass('selected');
						jQuery(self).find('input').val( jQuery(this).attr('position') );
					});

					jQuery(self).append(container);
				});
				
				
				// 
				// Checkbox controls hiding / showing div area.
				// Used for "Use site link" options.
				// 
				
				jQuery('input[type=checkbox][controls]').change( function()
				{
					var controls_div = '.'+this.attributes.controls.value;
					if( this.checked )
						jQuery(controls_div).hide();
					else
						jQuery(controls_div).show();
				}).change();
				
				
				// 
				// Site layout
				// 
				
				jQuery('input[site-layout]').each( function()
				{
					var self = this;
					var part = jQuery(this).attr('site-layout');
					var id = jQuery(this).attr('id');
					
					var add_highlight = function()
					{
						jQuery('.site-layout .'+part).addClass('highlight');
					}
					var remove_highlight = function()
					{
						jQuery('.site-layout .'+part).removeClass('highlight');
					}
					var show = function()
					{
						jQuery('.site-layout .'+part).removeClass('hide');
						jQuery('.site-layout .'+part).addClass('show');
					}
					var hide = function()
					{
						jQuery('.site-layout .'+part).addClass('hide');
						jQuery('.site-layout .'+part).removeClass('show');
					}
					
					jQuery(this)
						.change( 
							function() { if( this.checked ) show(); else hide(); })
						.change();
					
					jQuery(this).mouseenter( add_highlight );
					jQuery('label[for="'+id+'"]').mouseenter( add_highlight );

					jQuery(this).mouseleave( remove_highlight );
					jQuery('label[for="'+id+'"]').mouseleave( remove_highlight );
				});

			});
		</script>
		<?php
	}



//========================================================================================
//========================================================================= Settings =====


	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function register_settings()
	{
		add_filter( $this->slug.'-process-input', array($this, 'process_input'), 99, 5 );
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function add_settings_sections()
	{
		//
		// Variations
		//
		
		add_settings_section(
			'variations', 'Variations', array( $this, 'print_variations_section' ),
			$this->slug.':variations'
		);

		//
		// Templates
		//
		
		add_settings_section(
			'templates', 'Templates', array( $this, 'print_templates_section' ),
			$this->slug.':templates'
		);

		//
		// Widgets
		//
		
		add_settings_section(
			'widgets', 'Widgets', array( $this, 'print_widgets_section' ),
			$this->slug.':widgets'
		);

		//
		// General
		//
		
		add_settings_section(
			'general', 'General', array( $this, 'print_general_section' ),
			$this->slug.':general'
		);
				
		//
		// Header
		//
		
		add_settings_section(
			'header', 'Header', array( $this, 'print_header_section' ), 
			$this->slug.':header'
		);
		
		add_settings_section(
			'header-image', 'Header Image', array( $this, 'print_header_image' ), 
			$this->slug.':header:image'
		);

		add_settings_section(
			'header-title', 'Header Title', array( $this, 'print_header_title' ), 
			$this->slug.':header:title'
		);

		//
		// Banner
		//

		add_settings_section(
			'banner', 'Banner', array( $this, 'print_banner_section' ), 
			$this->slug.':banner'
		);
		
		//
		// Subheader
		//

		add_settings_section(
			'subheader', 'Subheader', array( $this, 'print_subheader_section' ), 
			$this->slug.':subheader'
		);

		add_settings_section(
			'subheader-image', 'Subheader Image', array( $this, 'print_subheader_image' ), 
			$this->slug.':subheader:image'
		);
		
		//
		// Mobile Menu
		//

		add_settings_section(
			'mobile-menu', 'Mobile Menu', array( $this, 'print_mobile_menu_section' ), 
			$this->slug.':mobile-menu'
		);

	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function add_settings_fields()
	{
		global $nh_config;
		
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
		// Header - Image
		//

		add_settings_field(
			'image-source', 'Image Source', array( $this, 'print_image_source' ),
			$this->slug.':header:image', 'header-image', array( 'header', 'image' )
		);
		add_settings_field(
			'image-media-library', 'Media Library', array( $this, 'print_image_source_media_library' ),
			$this->slug.':header:image', 'header-image', array( 'header', 'image' )
		);
		add_settings_field(
			'image-relative-path', 'Relative Path', array( $this, 'print_image_source_relative_path' ),
			$this->slug.':header:image', 'header-image', array( 'header', 'image' )
		);
		add_settings_field(
			'image-class', 'Image Class', array( $this, 'print_image_class' ),
			$this->slug.':header:image', 'header-image', array( 'header', 'image' )
		);
		add_settings_field(
			'image-title', 'Image Title', array( $this, 'print_image_title' ),
			$this->slug.':header:image', 'header-image', array( 'header', 'image' )
		);
		add_settings_field(
			'image-link', 'Image Link', array( $this, 'print_image_link' ),
			$this->slug.':header:image', 'header-image', array( 'header', 'image' )
		);
		
		
		//
		// Header - Title
		//

		add_settings_field(
			'image-title-position', 'Position', array( $this, 'print_header_title_position' ),
			$this->slug.':header:title', 'header-title', array( 'header', 'title' )
		);
		add_settings_field(
			'image-title-text', 'Title Text', array( $this, 'print_header_title_text' ),
			$this->slug.':header:title', 'header-title', array( 'header', 'title' )
		);
		add_settings_field(
			'image-title-link', 'Title Link', array( $this, 'print_header_title_link' ),
			$this->slug.':header:title', 'header-title', array( 'header', 'title' )
		);
		add_settings_field(
			'image-description-text', 'Description Text', array( $this, 'print_header_description_text' ),
			$this->slug.':header:title', 'header-title', array( 'header', 'title' )
		);
		add_settings_field(
			'image-description-link', 'Description Link', array( $this, 'print_header_description_link' ),
			$this->slug.':header:title', 'header-title', array( 'header', 'title' )
		);
		
		//
		// Banner
		//
		
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
		
		//
		// Header - Image
		//

		add_settings_field(
			'image-source', 'Image Source', array( $this, 'print_image_source' ),
			$this->slug.':subheader:image', 'subheader-image', array( 'subheader', 'image' )
		);
		add_settings_field(
			'image-media-library', 'Media Library', array( $this, 'print_image_source_media_library' ),
			$this->slug.':subheader:image', 'subheader-image', array( 'subheader', 'image' )
		);
		add_settings_field(
			'image-relative-path', 'Relative Path', array( $this, 'print_image_source_relative_path' ),
			$this->slug.':subheader:image', 'subheader-image', array( 'subheader', 'image' )
		);
		add_settings_field(
			'image-class', 'Image Class', array( $this, 'print_image_class' ),
			$this->slug.':subheader:image', 'subheader-image', array( 'subheader', 'image' )
		);
		add_settings_field(
			'image-title', 'Image Title', array( $this, 'print_image_title' ),
			$this->slug.':subheader:image', 'subheader-image', array( 'subheader', 'image' )
		);
		add_settings_field(
			'image-link', 'Image Link', array( $this, 'print_image_link' ),
			$this->slug.':subheader:image', 'subheader-image', array( 'subheader', 'image' )
		);
		
				
		//
		// Mobile Menu
		//
		
		add_settings_field( 
			'menu-tabs', 'Menu Tabs', array( $this, 'print_mobile_menu_widgets' ),
			$this->slug.':mobile-menu', 'mobile-menu', array( 'mobile-menu' )
		);
		
	}
	

//========================================================================================
//============================================================================= Save =====

	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
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

					$new_options = array();
					
					if( isset($tab_input['clear-sections']) )
					{
						add_settings_error( '', '', 'Clear sections', 'updated' );
						$tab_input['reset-layout'] = true;
						$tab_input['reset-stories'] = true;
					}
					else
					{
						$new_options['sections'] = $options['sections'];
					}
					
					if( isset($tab_input['reset-layout']) )
					{
						add_settings_error( '', '', 'Reset front page and sidebar layout', 'updated' );
					}
					else
					{
						$new_options['front-page-sections'] = $options['front-page-sections'];
						$new_options['sidebar-sections'] = $options['sidebar-sections'];
					}

					if( isset($tab_input['reset-stories']) )
					{
						add_settings_error( '', '', 'Reset selected stories', 'updated' );
					}
					else
					{
						$new_options['front-page-stories'] = $options['front-page-stories'];
						$new_options['sidebar-stories'] = $options['sidebar-stories'];
					}


					if( isset($tab_input['clear-banner-images']) )
					{
						add_settings_error( '', '', 'Cleared banner image selections', 'updated' );
					}
					else
					{
						$new_options['banner-images'] = $options['banner-images'];
					}

					add_settings_error( '', '', 'Reset options for variation: '.$chosen_variation, 'updated' );
					
					return $new_options;
					
				endif;
				
				break;
			
			case 'templates':
				// [template-part]
				if( array_key_exists('template-part', $tab_input) )
				{
					foreach( $tab_input['template-part'] as $template_part => $show )
					{
						if( !array_key_exists($template_part, $options) )
							$options[$template_part] = array();
						$options[$template_part]['show-part'] = $show;
					}
				}

				break;
				
			case 'widgets':
				// [widget]
				if( array_key_exists('widget', $tab_input) )
				{
					foreach( $tab_input['widget'] as $template_part => $widgets )
					{
						if( !array_key_exists($template_part, $options) )
							$options[$template_part] = array();
						
						foreach( $widgets as $area => $show )
						{
							$options[$template_part]['widget'][$area] = $show;
						}
					}
				}
				
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

		return array_merge( $options, array($tab => $tab_input) );
	}
	


//========================================================================================
//========================================================================== Display =====


	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function show()
	{
		global $nh_admin_pages, $wp_settings_sections;
		?>
		
		<div class="wrap tab-<?php echo $this->tab; ?>">
	 
			<div id="icon-themes" class="icon32"></div>
			<h2><?php echo $nh_admin_pages[$this->slug]['title']; ?></h2>
			<?php settings_errors(); ?>
		 
			<h2 class="nav-tab-wrapper">
				<?php foreach( $this->tabs as $k => $t ): ?>
					<a href="?page=<?php echo $this->slug; ?>&tab=<?php echo $k; ?>" class="nav-tab <?php if($k==$this->tab) echo 'active'; ?>"><?php echo $t; ?></a>
				<?php endforeach; ?>
			</h2>
		
			<form method="post" action="options.php">
				<div class="top-submit"><?php submit_button(); ?></div>
				<div style="clear:both"></div>
				<?php settings_fields( $this->slug ); ?>
				<input type="hidden" name="tab" value="<?php echo $this->tab; ?>" />
				
				<?php
				do_settings_sections( $this->slug.':'.$this->tab );
				
				$tab_section = $this->slug.':'.$this->tab.':';
				foreach( array_keys($wp_settings_sections) as $section_name )
				{
					if( substr($section_name, 0, strlen($tab_section)) === $tab_section )
					{
						do_settings_sections( $section_name );
					}
				}
				?>
				
				<div style="clear:both"></div>
				<div class="bottom-submit"><?php submit_button(); ?></div>
			</form>
		 
		</div><!-- /.wrap -->
		
		<?php
	}
	


//========================================================================================
//========================================================= Display Setting Sections =====

	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_variations_section()
	{
		echo '<p>print_variations_section</p>';
	}
	

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_templates_section()
	{
		echo '<p>print_templates_section</p>';
		
		$this->print_show_template_parts( array() );
		$this->create_site_layout( false );
	}
		

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_widgets_section()
	{
		echo '<p>print_widgets_section</p>';

		$this->print_show_widget_areas( array() );
		$this->create_site_layout( true );
	}
		

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_general_section()
	{
		echo '<p>print_general_section</p>';
	}
		

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_section()
	{
		echo '<p>print_header_section</p>';
	}
	

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------	
	public function print_header_image()
	{
		echo 'print_header_image';
		do_settings_sections( 'header-image' );
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_title()
	{
		echo 'print_header_title';
		do_settings_sections( 'header-title' );
	}


	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_banner_section()
	{
		echo '<p>print_banner_section</p>';
	}
		

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_subheader_section()
	{
		echo '<p>print_subheader_section</p>';
	}
		

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_subheader_image()
	{
		echo 'print_subheader_image';
		
		do_settings_sections( 'subheader-image' );
	}


	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_mobile_menu_section()
	{
		echo '<p>print_mobile_menu_section</p>';
	}


	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_main_section()
	{
		echo '<p>print_main_section</p>';
	}


	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_content_section()
	{
		echo '<p>print_content_section</p>';
	}


	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_sidebar_section()
	{
		echo '<p>print_sidebar_section</p>';
	}
		

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_footer_section()
	{
		echo '<p>print_footer_section</p>';
	}
	


//========================================================================================
//================================================================== Settings Fields =====



	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
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

		<div>
		<input type="checkbox" 
		       name="<?php nh_input_name_e( $this->tab, 'clear-sections' ); ?>" 
		       value="clear-sections" />
		Clear All Sections
		</div>

		<div>
		<input type="checkbox" 
		       name="<?php nh_input_name_e( $this->tab, 'reset-layout' ); ?>" 
		       value="reset-layout" />
		Reset Layout
		</div>

		<div>
		<input type="checkbox" 
		       name="<?php nh_input_name_e( $this->tab, 'reset-banner-images' ); ?>" 
		       value="reset-banner-images" />
		Reset Banner Images
		</div>

		<div>
		<input type="checkbox" 
		       name="<?php nh_input_name_e( $this->tab, 'reset-stories' ); ?>" 
		       value="reset-stories" />
		Reset Stories
		</div>
		
		<?php		
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
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
			           id="<?php nh_input_name_e( $this->tab, 'custom-post-type', $type ); ?>" 
			           name="<?php nh_input_name_e( $this->tab, 'custom-post-type', $type ); ?>" 
			           value="b:true" 
			           <?php checked( true, $nh_config->use_custom_post_type($type) ); ?> />
			    <label for="<?php nh_input_name_e( $this->tab, 'custom-post-type', $type ); ?>">
			    	<?php echo $type; ?>
			    </label>
		    </div>
	    <?php endforeach; ?>
		
		<?php
	}
	

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_show_template_parts( $args )
	{
		
		global $nh_config;
		
		$template_parts = array( 'header', 'banner', 'subheader', 'mobile-menu', 'footer' );
		
		?>
		
		<div class="template-parts">
		
		<?php foreach( $template_parts as $part ): ?>
		
			<div>
			<input type="hidden" 
				   name="<?php nh_input_name_e( $this->tab, 'template-part', $part ); ?>" 
				   value="b:false" />
			<input type="checkbox" 
			       id="<?php nh_input_name_e( $this->tab, 'template-part', $part ); ?>" 
				   name="<?php nh_input_name_e( $this->tab, 'template-part', $part ); ?>"
				   value="b:true" 
				   site-layout="<?php echo $part; ?>" 
				   <?php checked( true, $nh_config->show_template_part($part) ); ?> />
			<label for="<?php nh_input_name_e( $this->tab, 'template-part', $part ); ?>">
				<?php echo ucwords(str_replace('-', ' ', $part)); ?>
			</label>
			</div>
		
		<?php endforeach; ?>
		
		</div>
		
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_show_part( $args )
	{
		global $nh_config;
		?>
	    
	    <input type="hidden"   
	           name="<?php nh_input_name_e( $this->tab, 'show-part' ); ?>" 
	           value="b:false" />
	    <input type="checkbox" 
	           name="<?php nh_input_name_e( $this->tab, 'show-part' ); ?>" 
	           value="b:true" <?php checked( true, $nh_config->show_template_part($args[0]) ); ?> />
		
		<?php
	}
	
	

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_show_widget_areas( $args )
	{
		global $nh_config;
		
		$widget_areas = array();
		$widget_areas['header'] = array( 'top', 'bottom' );
		$widget_areas['banner'] = array( 'top', 'bottom' );
		$widget_areas['subheader'] = array( 'top', 'bottom' );
		$widget_areas['mobile-menu'] = array( 'top', 'bottom' );
		$widget_areas['main'] = array( 'top', 'bottom' );
		$widget_areas['content'] = array( 'top', 'bottom' );
		$widget_areas['sidebar'] = array( 'top', 'bottom' );
		$widget_areas['footer'] = array( 'top', 'bottom', 'column-1', 'column-2', 'column-3', 'column-4' );
		?>
		
		<div class="widget-areas">
		<?php foreach( $widget_areas as $template_part => $areas ): ?>
		
			<?php if( $nh_config->show_template_part($template_part) ): ?>
			
				<div style="display:inline-block;margin-right:10px;margin-bottom:30px;border-left:solid 1px #ccc;padding:10px;vertical-align:top;width:10em;">
				<h4 style="margin-top:0px;"><?php echo ucwords(str_replace('-', ' ', $template_part)); ?></h4>
				
				<?php foreach( $areas as $area ): ?>
				
					<div>
					<input type="hidden" 
						   name="<?php nh_input_name_e( $this->tab, 'widget', $template_part, $area ); ?>" 
						   value="b:false" />
					<input type="checkbox" 
					       id="<?php nh_input_name_e( $this->tab, 'widget', $template_part, $area ); ?>" 
						   name="<?php nh_input_name_e( $this->tab, 'widget', $template_part, $area ); ?>"
						   value="b:true" 
		   				   site-layout="<?php echo $template_part.'-'.$area; ?>" 
						   <?php checked( true, $nh_config->use_widget( $template_part, $area ) ); ?> />
					<label for="<?php nh_input_name_e( $this->tab, 'widget', $template_part, $area ); ?>">
						<?php echo ucwords(str_replace('-', ' ', $area)); ?>
					</label>
					</div>
				
				<?php endforeach; ?>
				
				</div>
			
			<?php endif; ?>
		
		<?php endforeach; ?>
		
		</div>
		
		<?php
	}
	

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_widgets( $args )
	{
		global $nh_config;
		
		if( $nh_config->show_template_part($args[0]) === false ) return;
		?>
	    
	    <input type="hidden" 
	           name="<?php nh_input_name_e( $this->tab, 'widget', 'top' ); ?>" 
	           value="b:false" />
	    <input type="checkbox" 
	           name="<?php nh_input_name_e( $this->tab, 'widget', 'top' ); ?>"
	           value="b:true" 
	           <?php checked( true, $nh_config->use_widget( $this->tab, 'top' ) ); ?> />
	    <label>Top</label>
	    
	    <input type="hidden" 
	           name="<?php nh_input_name_e( $this->tab, 'widget', 'bottom' ); ?>" 
	           value="b:false" />
	    <input type="checkbox" 
	           name="<?php nh_input_name_e( $this->tab, 'widget', 'bottom' ); ?>"
	           value="b:true" 
	           <?php checked( true, $nh_config->use_widget( $this->tab, 'bottom' ) ); ?> />
	    <label>Bottom</label>
		
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_image_source( $args )
	{
		global $nh_config;
		$image = $nh_config->get_image_data( $args );
	
		$image_type_selection = array(
			'relative' => 'Relative Path',
			'media' => 'Media Library Image',
		);
	
		if( !array_key_exists($image['selection-type'], $image_type_selection) )
			$image['selection-type'] = 'relative';
		?>
		
		<div class="image-selector" selector-id="<?php echo implode('-',$args); ?>">
		
		<?php foreach( $image_type_selection as $key => $value ): ?>
			<input type="radio"
				   class="selection-type" 
				   name="<?php nh_input_name_e( $args, 'selection-type' ); ?>"
				   value="<?php echo $key; ?>" 
				   <?php checked( $key, $image['selection-type'] ); ?> />
			<?php echo $value; ?>
			<br/>
		<?php endforeach; ?>
		
		</div>
		
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_image_source_media_library( $args )
	{
		global $nh_config;
		$image = $nh_config->get_image_data( $args );
		?>
		
		<div class="media-select" selector-id="<?php echo implode('-',$args); ?>">
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
		
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_image_source_relative_path( $args )
	{
		global $nh_config;
		$image = $nh_config->get_image_data( $args );
		?>
		
		<div class="relative-path" selector-id="<?php echo implode('-',$args); ?>">
			<input type="text" 
				   id="<?php nh_input_name_e( $args, 'path' ); ?>" 
				   name="<?php nh_input_name_e( $args, 'path' ); ?>" 
				   value="<?php echo $image['path']; ?>" />
		</div>
	
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_image_class( $args )
	{
		global $nh_config;
		$image = $nh_config->get_image_data( $args );
		?>

		<input type="text" 
			   id="<?php nh_input_name_e( $args, 'class' ); ?>" 
			   name="<?php nh_input_name_e( $args, 'class' ); ?>" 
			   value="<?php echo $image['class']; ?>" />

		
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_image_title( $args )
	{
		global $nh_config;
		$image = $nh_config->get_image_data( $args );
		?>

		<input type="text" 
			   name="<?php nh_input_name_e( $args, 'title' ); ?>" 
			   value="<?php echo $image['title']; ?>" />
		
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_image_link( $args )
	{
		global $nh_config;
		$image = $nh_config->get_image_data( $args );
		?>
		
		<input type="hidden" 
			   name="<?php nh_input_name_e( $args, 'use-site-link' ); ?>" 
			   value="b:false" />
		<input type="checkbox" 
		       id="<?php nh_input_name_e( $args, 'use-site-link' ); ?>"  
			   name="<?php nh_input_name_e( $args, 'use-site-link' ); ?>"  
			   class="use-site-url" 
			   value="b:true" 
			   <?php checked( true, $image['use-site-link'] ); ?>
			   controls="image-link-url" />
		<label for="<?php nh_input_name_e( $args, 'use-site-link' ); ?>">use site URL</label>
		
		<div class="image-link-url">
			<input type="text" 
				   name="<?php nh_input_name_e( $args, 'link' ); ?>" 
				   class="link-url" 
				   value="<?php echo $image['link']; ?>" />
		</div>

		<?php		
	}


	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_title_position( $args )
	{
		global $nh_config;
		$title = $nh_config->get_text_data( 'header', 'title' );
		$description = $nh_config->get_text_data( 'header', 'description' );
		?>
		
	    <div class="position">
			<input type="text"
			       name="<?php nh_input_name_e( $this->tab, 'title', 'position' ); ?>"
			       value="<?php echo $title['position']; ?>" />
		</div>
				
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_title_text( $args )
	{
		global $nh_config;
		$title = $nh_config->get_text_data( 'header','title' );
		$description = $nh_config->get_text_data( 'header','description' );
		?>

		<input type="text" 
		       id="title-text" 
		       name="<?php nh_input_name_e( $this->tab, 'title', 'text' ); ?>" 
		       value="<?php echo $title['text']; ?>" />
		
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_title_link( $args )
	{
		global $nh_config;
		$title = $nh_config->get_text_data( 'header','title' );
		$description = $nh_config->get_text_data( 'header','description' );
		?>

		<input type="hidden" 
			   name="<?php nh_input_name_e( $this->tab, 'title', 'use-site-link' ); ?>" 
			   value="b:false" />
		<input type="checkbox" 
			   id="<?php nh_input_name_e( $this->tab, 'title', 'use-site-link' ); ?>" 
			   name="<?php nh_input_name_e( $this->tab, 'title', 'use-site-link' ); ?>" 
			   class="use-site-url" 
			   value="b:true" 
			   <?php checked( true, $title['use-site-link'] ); ?>
			   controls="header-title-link" />
		<label for="<?php nh_input_name_e( $this->tab, 'title', 'use-site-link' ); ?>">use site URL</label>

		<div class="header-title-link">
			<input type="text"
				   name="<?php nh_input_name_e( $this->tab, 'title', 'link' ); ?>" 
				   value="<?php echo $title['link']; ?>" />
		</div>
		
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_description_text( $args )
	{
		global $nh_config;
		$title = $nh_config->get_text_data( 'header','title' );
		$description = $nh_config->get_text_data( 'header','description' );
		?>

		<input type="text" 
		       id="description-text" 
		       name="<?php nh_input_name_e( $this->tab, 'description', 'text' ); ?>" 
		       value="<?php echo $description['text']; ?>" />		

		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_header_description_link( $args )
	{
		global $nh_config;
		$title = $nh_config->get_text_data( 'header','title' );
		$description = $nh_config->get_text_data( 'header','description' );
		?>

		<input type="hidden" 
			   name="<?php nh_input_name_e( $this->tab, 'description', 'use-site-link' ); ?>" 
			   value="b:false" />
		<input type="checkbox" 
			   id="<?php nh_input_name_e( $this->tab, 'description', 'use-site-link' ); ?>" 
			   name="<?php nh_input_name_e( $this->tab, 'description', 'use-site-link' ); ?>" 
			   class="use-site-url" 
			   value="b:true" 
			   <?php checked( true, $description['use-site-link'] ); ?>
			   controls="description-link-url" />
		<label for="<?php nh_input_name_e( $this->tab, 'description', 'use-site-link' ); ?>">use site URL</label>

		<div class="description-link-url">
			<input type="text"
				   name="<?php nh_input_name_e( $this->tab, 'description', 'link' ); ?>" 
				   value="<?php echo $description['link']; ?>" />
		</div>
				
		<?php
	}
	

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
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
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_num_slides( $args )
	{
		global $nh_config;
		
		$num_slides = $nh_config->get_value( 'banner', 'max-slides' );
		
		?>
		<input type="text" 
		       class="number" 
		       name="<?php nh_input_name_e( $this->tab, 'max-slides' ); ?>"
		       value="<?php echo $num_slides; ?>" 
		       min="-1" />
		<?php
	}
	

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_size( $args )
	{
		global $nh_config;
		
		$width = $nh_config->get_value( 'banner', 'width' );
		$height = $nh_config->get_value( 'banner', 'height' );
		
		?>
		<input type="text" 
		       class="number" 
		       name="<?php nh_input_name_e( $this->tab, 'width' ); ?>" 
		       value="<?php echo $width; ?>" 
		       min="-1" />
		&nbsp;x&nbsp;
		<input type="text" 
		       class="number" 
		       name="<?php nh_input_name_e( $this->tab, 'height' ); ?>" 
		       value="<?php echo $height; ?>" 
		       min="-1" />
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
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
	

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	public function print_copyright( $args )
	{
		global $nh_config;
		
		$copyright = $nh_config->get_text_data( 'footer', 'copyright' );
		?>
		
		<input type="text" 
		       id="copyright" 
		       name="<?php nh_input_name_e( $this->tab, 'copyright' ); ?>" 
		       value="<?php echo $copyright; ?>" />
		
		<?php
	}
	
	

//========================================================================================
//============================================================================ Other =====



	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	private function create_site_layout()
	{
		global $nh_config;
		
		?>
		<div class="site-layout">

			<div class="part site">
				<div class="widget site-top <?php $this->use_widget_e('site','top'); ?>"></div>
				
				<div class="part header <?php $this->use_part_e('header'); ?>">
					<div class="widget header-top <?php $this->use_widget_e('header','top'); ?>"></div>
					<div class="title">header</div>
					<div class="widget header-bottom <?php $this->use_widget_e('header','bottom'); ?>"></div>
				</div>
				
				<div class="part banner <?php $this->use_part_e('banner'); ?>">
					<div class="widget banner-top <?php $this->use_widget_e('banner','top'); ?>"></div>
					<div class="title">banner</div>
					<div class="widget banner-bottom <?php $this->use_widget_e('banner','bottom'); ?>"></div>
				</div>

				<div class="part subheader <?php $this->use_part_e('subheader'); ?>">
					<div class="widget subheader-top <?php $this->use_widget_e('subheader','top'); ?>"></div>
					<div class="title">subheader</div>
					<div class="widget subheader-bottom <?php $this->use_widget_e('subheader','bottom'); ?>"></div>
				</div>

				<div class="part mobile-menu <?php $this->use_part_e('mobile-menu'); ?>">
					<div class="widget mobile-menu-top <?php $this->use_widget_e('mobile-menu','top'); ?>"></div>
					<div class="title">mobile menu</div>
					<div class="widget mobile-menu-bottom <?php $this->use_widget_e('mobile-menu','bottom'); ?>"></div>
				</div>

				<div class="part main">
					<div class="widget main-top <?php $this->use_widget_e('main','top'); ?>"></div>

					<div class="main-parts-container">
						
						<div class="part content">
							<div class="widget content-top <?php $this->use_widget_e('content','top'); ?>"></div>
							<div class="title">content</div>
							<div class="widget content-bottom <?php $this->use_widget_e('content','bottom'); ?>"></div>
						</div>
					
						<div class="part sidebar">
							<div class="widget sidebar-top <?php $this->use_widget_e('sidebar','top'); ?>"></div>
							<div class="title">sidebar</div>
							<div class="widget sidebar-bottom <?php $this->use_widget_e('sidebar','bottom'); ?>"></div>
						</div>
					
					</div>

					<div class="widget main-bottom <?php $this->use_widget_e('main','bottom'); ?>"></div>
				</div>

				<div class="part footer <?php $this->use_part_e('footer'); ?>">
					<div class="widget footer-top <?php $this->use_widget_e('subheader','top'); ?>"></div>
					<div class="title">footer</div>
					<div class="footer-columns-container">
						<div class="widget footer-column-1 <?php $this->use_widget_e('footer','column-1'); ?>"></div>
						<div class="widget footer-column-2 <?php $this->use_widget_e('footer','column-2'); ?>"></div>
						<div class="widget footer-column-3 <?php $this->use_widget_e('footer','column-3'); ?>"></div>
						<div class="widget footer-column-4 <?php $this->use_widget_e('footer','column-4'); ?>"></div>
					</div>
					<div class="widget footer-bottom <?php $this->use_widget_e('footer','bottom'); ?>"></div>					
				</div>

				<div class="widget site-bottom <?php $this->use_widget_e('site','bottom'); ?>"></div>
			</div>
			
		</div>
		<?php
	}
	
	
	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	private function use_part_e( $part )
	{
		global $nh_config;
		echo ( $nh_config->show_template_part( $part ) ? 'show' : 'hide' );
	}
	

	//------------------------------------------------------------------------------------
	//  
	//------------------------------------------------------------------------------------
	private function use_widget_e( $part, $placement )
	{
		global $nh_config;
		if( $nh_config->show_template_part( $part ) )
			echo ( $nh_config->use_widget( $part, $placement ) ? 'show' : 'hide' );
		else
			echo 'hide';
	}
	
}










