<?php

require_once( dirname(__FILE__).'/section.php' );

//========================================================================================
// 
// 
// 
//========================================================================================
class NS_Config
{

	//====================================================================================
	//============================================================= Class Properties =====


	const CONFIG_INI_FILENAME = 'config.ini';
	private $config;
	

	//====================================================================================
	//=============================================================== Class Behavior =====


	//------------------------------------------------------------------------------------
	// Default Constructor.
	//------------------------------------------------------------------------------------
	public function __construct() { }
	
	
	//------------------------------------------------------------------------------------
	// Loads the default configuration, then configuration file, if exists.
	//------------------------------------------------------------------------------------
	public function load_config()
	{
		$this->config = array();

		if( file_exists(get_template_directory().'/'.self::CONFIG_INI_FILENAME) )
			$this->load_from_ini( get_template_directory().'/'.self::CONFIG_INI_FILENAME );
		else
			exit( 'Unable to locate theme config.ini file.' );

		if( is_child_theme() )
		{
			if( file_exists(get_stylesheet_directory().'/'.self::CONFIG_INI_FILENAME) )
				$this->load_from_ini( get_stylesheet_directory().'/'.self::CONFIG_INI_FILENAME );
		}
		
		$this->verify_settings();
		$this->populate_widget_areas();
		
		//ns_print($this->config);
	}
	

	//------------------------------------------------------------------------------------
	// 
	// 
	// @param	$config_filname	string		The path to the config INI file.
	//------------------------------------------------------------------------------------
	private function load_from_ini( $config_filename )
	{
		$config = parse_ini_file( $config_filename, true);
		$this->fix_keys( $config );
		$this->config = array_replace_recursive($this->config, $config);
    }
    

	//------------------------------------------------------------------------------------
	// 
	// 
	// @param	
	//------------------------------------------------------------------------------------
    private function fix_keys( &$array )
    {
		foreach( $array AS $key => &$value )
		{
			if( is_array($value) )
				$this->fix_keys( $value );
			
			if( $value === "yes" )
				$value = true;
			elseif( $value === "no" )
				$value = false;
			elseif( is_numeric($value) )
				$value = intval($value);
		}
	}
    

	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function verify_settings()
	{
		foreach( $this->config['sections'] as $key => &$section_data )
		{
			$section_data = new NS_Section( $key, $section_data );
		}
	}
	

	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function populate_widget_areas()
	{
		$widget_areas = array();
		
		foreach( $this->config as $template_part => $settings )
		{
			if( (is_array($settings)) && (array_key_exists('widget', $settings)) )
			{
				foreach( $settings['widget'] as $name => $view )
				{
					if( $view )
					{
						$widget_areas[] = array(
							'name' => ucwords(str_replace('-', ' ', $template_part)).': '.ucwords(str_replace('-', ' ', $name)),
							'id' => $template_part.'-'.$name,
						);
					}
				}
			}
			
			if( $template_part === 'mobile-menu' )
			{
				foreach( $this->config['mobile-menu']['menu-widget'] as $widget => $name )
				{
					if( !empty($name) )
					{
						$widget_areas[] = array(
							'name' => 'Mobile Menu: '.ucwords($name),
							'id' => 'mobile-menu-'.$widget,
						);
					}
				}
			}
		}
		
				
		$this->config['widget-areas'] = $widget_areas;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function show_template_part( $template_part )
	{
		if( (array_key_exists($template_part, $this->config)) &&
		    (array_key_exists('show-part', $this->config[$template_part])) )
			return $this->config[$template_part]['show-part'];
		
		return false;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_widget_areas()
	{
		return $this->config['widget-areas'];
	}
	
	
	public function get_mobile_widget_areas()
	{
		$mobile_widgets = array();
		
		foreach( $this->config['mobile-menu']['menu-widget'] as $widget => $name )
		{
			if( !empty($name) )
			{
				$mobile_widgets[] = array(
					'index' => $widget,
					'name' => $name,
					'id' => 'mobile-menu-'.$widget,
				);
			}
		}
		
		return $mobile_widgets;
	}
	
	
	public function get_footer_widget_areas()
	{
		$footer_widgets = array();
		
		for( $i = 0; $i < 4; $i++ )
		{
			if( $this->config['footer']['widget']['column-'.($i+1)] )
				$footer_widgets[] = 'column-'.($i+1);
		}
		
		return $footer_widgets;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function use_widget( $part, $placement )
	{
		if( !array_key_exists($part, $this->config) )
			return false;
		
		if( $part == 'mobile-menu' )
		{
			if( (array_key_exists('menu-widget', $this->config[$part])) &&
			    (isset($this->config[$part]['menu-widget'][$placement])) )
			    return $this->config[$part]['menu-widget'][$placement];
		}
		else
		{
			if( array_key_exists($placement, $this->config[$part]['widget']) )
				return $this->config[$part]['widget'][$placement];
		}
		
		return false;
	}


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_column( $part, $name )
	{
		if( !array_key_exists($part, $this->config) )
			return null;
			
		if( !array_key_exists($name, $this->config[$part]) )
			return null;
			
		return $this->config[$part][$name];
	}


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_num_columns( $name )
	{
		switch( $name )
		{
			case( 'front-page' ):
				return $this->config['content']['num-columns']['front'];
				break;
				
			case( 'portrait' ):
				return $this->config['content']['num-columns']['portrait'];
				break;
				
			case( 'landscape' ):
				return $this->config['content']['num-columns']['landscape'];
				break;
				
			case( 'none' ):
				return $this->config['content']['num-columns']['none'];
				break;
				
			default:
				return 1;
				break;
		}
		
		return 1;
	}


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_timezone()
	{
		return $this->config['timezone'];
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_categories()
	{
		return $this->config['categories'];
	}
	

	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_tags()
	{
		return $this->config['tags'];
	}


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_default_section()
	{
		return new NS_Section( 'none', array( 'name' => 'None' ) );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_section( $post_type, $category = array(), $tag = array(), $return_null = false, $exclude_sections = array() )
	{
		$section = null;
		
		// 
		// 
		// 
		if( $category === null ) $category = array();
		elseif( !is_array($category) ) $category = array( $category );
		
		if( $tag === null ) $tag = array();
		elseif( !is_array($tag) ) $tag = array( $tag );
		
		// 
		// 
		// 
		foreach( $category as $c )
		{
			foreach( $tag as $t )
			{
				foreach( $this->config['sections'] as $section )
				{
					if( ($section->type == $post_type) && 
						($section->category == $c) && 
						($section->tag == $t) &&
						(!in_array($section->key, $exclude_sections)) )
					{
						ns_print('match');
						return $section;
					}
				}
			}
		}
		
		// 
		// 
		// 
		$i = 1;
		foreach( $category as $c )
		{
			foreach( $this->config['sections'] as $section )
			{
				if( ($section->type == $post_type) && 
					($section->category == $c) &&
					(!in_array($section->key, $exclude_sections)) )
				{
					return $section;
				}
			}
			$i++;
		}

		// 
		// 
		// 
		foreach( $tag as $t )
		{
			foreach( $this->config['sections'] as $section )
			{
				if( ($section->type == $post_type) && 
					($section->tag == $t) &&
					(!in_array($section->key, $exclude_sections)) )
				{
					return $section;
				}
			}
		}
		
		// 
		// 
		// 
		if( $return_null ) return null;
		return $this->get_default_section();
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_section_by_key( $key, $return_null = false )
	{
		if( array_key_exists($key, $this->config['sections']) )
			return $this->config['sections'][$key];
			
		if( $return_null ) return null;
		return $this->get_default_section();
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_options( $key, $default = false )
	{
		if( array_key_exists($key, $this->config) )
			return $this->config[$key];
			
		return $default;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_banner_images()
	{
		global $ns_mobile_support;
		
		$options = $this->get_options( 'banner_images', array() );

		$image_type = 'full';
		if( $ns_mobile_support->use_mobile_site )
			$image_type = 'thumbnail_landscape';

		$images = array();
		foreach( $options as $image_id )
		{
			$src = wp_get_attachment_image_src(
				intval($image_id), $image_type
			);
			if( !$src ) continue;
			$src = $src[0];
			
			$images[] = array(
				'src' => $src,
				'url' => $option['url'],
				'alt' => stripslashes($option['alt'])
			);
		}
		
		return $images;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_value()
	{
		$args = func_get_args();
		
		$config = $this->config;
		foreach( $args as $arg )
		{
			if( array_key_exists($arg, $config) )
			{
				$config = $config[$arg];
			}
			else
			{
				$config = null;
				break;
			}
		}

		return $config;
	}
	
}

