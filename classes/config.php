<?php

require_once( dirname(__FILE__).'/section.php' );

//========================================================================================
// 
// 
// 
//========================================================================================
class NH_Config
{

	//====================================================================================
	//============================================================= Class Properties =====


	const CONFIG_DEFAULT_INI_FILENAME = 'config/config-default.ini';
	const CONFIG_INI_FILENAME = 'config/config.ini';
	const OPTIONH_DEFAULT_INI_FILENAME = 'config/optionh-default.ini';
	const OPTIONH_INI_FILENAME = 'config/options.ini';
	private $config;
	private $options;
	

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
		
		if( file_exists(get_template_directory().'/'.self::CONFIG_DEFAULT_INI_FILENAME) )
			$this->load_from_ini( $this->config, get_template_directory().'/'.self::CONFIG_DEFAULT_INI_FILENAME );
		elseif( file_exists(get_template_directory().'/'.self::CONFIG_INI_FILENAME) )
			$this->load_from_ini( $this->config, get_template_directory().'/'.self::CONFIG_INI_FILENAME );
		else
			exit( 'Unable to locate theme '.self::CONFIG_DEFAULT_INI_FILENAME.' file.' );

		if( is_child_theme() )
		{
			if( file_exists(get_stylesheet_directory().'/'.self::CONFIG_INI_FILENAME) )
				$this->load_from_ini( $this->config, get_stylesheet_directory().'/'.self::CONFIG_INI_FILENAME );
		}


		$this->options = array();

		if( file_exists(get_template_directory().'/'.self::OPTIONH_DEFAULT_INI_FILENAME) )
			$this->load_from_ini( $this->options, get_template_directory().'/'.self::OPTIONH_DEFAULT_INI_FILENAME );
		elseif( file_exists(get_template_directory().'/'.self::OPTIONH_INI_FILENAME) )
			$this->load_from_ini( $this->options, get_template_directory().'/'.self::OPTIONH_INI_FILENAME );
		else
			exit( 'Unable to locate theme '.self::OPTIONH_DEFAULT_INI_FILENAME.' file.' );
		
		if( is_child_theme() )
		{
			/* RESTORE THIS CODE FOR PRODUCTION:
			if( file_exists(get_stylesheet_directory().'/'.self::OPTIONH_INI_FILENAME) )
				$this->load_from_ini( $this->options, get_stylesheet_directory().'/'.self::OPTIONH_INI_FILENAME );
			*/
			
			$optionh_filename = NH_BLOG_NAME;
// 			nh_print( $optionh_filename, 'OPTIONS FILENAME' );

			if( file_exists(get_stylesheet_directory().'/config/optionh-'.$optionh_filename.'.ini') )
			{
				$this->load_from_ini( $this->options, get_stylesheet_directory().'/config/optionh-'.$optionh_filename.'.ini' );
			}
			elseif( file_exists(get_stylesheet_directory().'/'.self::OPTIONH_INI_FILENAME) )
			{
				$this->load_from_ini( $this->options, get_stylesheet_directory().'/'.self::OPTIONH_INI_FILENAME );
			}
		}
		
		$this->options = array_replace_recursive( $this->options, get_option('nh-theme-options', array()) );

		$this->config = array_replace_recursive($this->options, $this->config);

		$this->verify_settings();
		$this->populate_widget_areas();
	}
	

	//------------------------------------------------------------------------------------
	// 
	// 
	// @param	$config_filname	string		The path to the config INI file.
	//------------------------------------------------------------------------------------
	private function load_from_ini( &$config, $config_filename )
	{
		$ini_config = parse_ini_file( $config_filename, true);
		$this->fix_keys( $ini_config );
		$config = array_replace_recursive($config, $ini_config);
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
			{
				$this->fix_keys( $value );
				continue;
			}
			
			if( (strlen($value) > 2) && ($value[1] == ':') )
			{
				switch( $value[0] )
				{
					case 'b':
						if( substr($value, 2) == 'true' )
							$value = true;
						else
							$value = false;
						break;
						
					case 'i':
						$value = intval( substr($value, 2) );
						break;
				}
			}
		}
	}
    

	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function verify_settings()
	{
		foreach( $this->config['sections'] as $key => &$section_data )
		{
			if( is_array($section_data) ) 
				$section_data = new NH_Section( $key, $section_data );
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
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
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
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
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
	public function get_number_of_columns( $name, $section = null )
	{
		if( ($section !== null) && (array_key_exists($name, $section->num_columns)) )
			return $section->num_columns[$name];
		
		if( isset($this->config['content']['num-columns'][$name]) )
			return $this->config['content']['num-columns'][$name];
			
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
		return new NH_Section( 'none', array( 'name' => 'None' ) );
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
		if( $post_type !== 'post' && $post_type !== 'page' )
		{
			foreach( $this->config['sections'] as $section )
			{
				if( ($section->type == $post_type) && 
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
		global $nh_mobile_support;
		
		$options = $this->get_value( 'banner', 'images' );
		if( $options == null ) $options = array();
		
		$image_type = 'full';
		if( $nh_mobile_support->use_mobile_site )
			$image_type = 'thumbnail_landscape';

		$images = array();
		foreach( $options as $option )
		{
			$src = wp_get_attachment_image_src(
				intval($option['id']), $image_type
			);
			if( !$src ) continue;
			$src = $src[0];
			
			$images[] = array(
				'id'  => $option['id'],
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
	public function get_admin_options( $page )
	{
		$options = array();
		
		switch( $page )
		{
			case( 'front-page' ):
				
				$options['num-columns'] = $this->config['content']['num-columns']['front-page'];
				
				$options['sections'] = array();
				for( $i = 0; $i < $options['num-columns']; $i++ )
				{
					$column_name = 'column-'.($i+1);
					if( isset($this->config['front-page-sections'][$column_name]) )
						$options['sections'][$column_name] = $this->config['front-page-sections'][$column_name];
					else
						$options['sections'][$column_name] = array();
				}
				
				$options['stories'] = array();
				if( isset($this->config['front-page-stories']) )
					$options['stories'] = $this->config['front-page-stories'];
				
				break;
				
			case( 'sidebar' ):

				$options['sections'] = array();
				if( isset($this->config['sidebar-sections']['column-1']) )
					$options['sections']['column-1'] = $this->config['sidebar-sections']['column-1'];

				$options['stories'] = array();
				if( isset($this->config['sidebar-stories']) )
					$options['stories'] = $this->config['sidebar-stories'];
					
				break;
				
			case( 'news' ):
				
				if( isset($this->config['news-stories']) )
					$options['stories'] = $this->config['news-stories'];
				else
					$options['stories'] = array();
				
				break;
		}
		
		$options = apply_filters( 'nh-get-admin-options', $options, $page );
		return $options;
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
	

	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_option_value()
	{
		$args = func_get_args();
		
		$config = $this->options;
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
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function set_option_value()
	{
		$args = func_get_args();
		$value = array_pop($args);
		$key = array_pop($args);
		
		if( ($key === null) || ($value === null) ) return;
		
		$config = &$this->options;		
		foreach( $args as $arg )
		{
			if( !array_key_exists($arg, $config) )
			{
				$config[$arg] = array();
			}

			$config = &$config[$arg];
		}
		
		$config[$key] = $value;
	}
		
		
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function save_options()
	{
		update_option( 'nh-theme-options', $this->options );
		$this->config = array_replace_recursive($this->config, $this->options);
		$this->verify_settings();
		//nh_print($this->config);
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function set_stories( $page, $stories )
	{
		$this->options[$page.'-stories'] = $stories;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function set_num_stories( $page, $num_stories )
	{
		foreach( $num_stories as $key => $num )
		{
			if( !empty($this->options['sections'][$key]) )
				$this->options['sections'][$key][$page.'-num-stories'] = $num;
		}
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_todays_datetime()
	{
		if( isset($this->config['timezone']) )
			date_default_timezone_set('America/New_York');
		$todays_datetime = new DateTime;
		return $todays_datetime;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function reset_options()
	{
		delete_option( 'nh-theme-options' );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function set_header( $title, $description )
	{
		$this->options['header']['title'] = $title;
		$this->options['header']['description'] = $description;
	}

}

