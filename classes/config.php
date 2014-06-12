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


	const DB_VERSION = '1.1';
	const CONFIG_DEFAULT_INI_FILENAME = 'config/config-default.ini';
	const CONFIG_INI_FILENAME = 'config/config.ini';
	const OPTIONS_DEFAULT_INI_FILENAME = 'config/options-default.ini';
	const OPTIONS_INI_FILENAME = 'config/options.ini';
	private $config;
	private $options;
	private $sections;
	

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
// 		if( empty($_POST) ) nh_print(get_option('nh-options', array()));
		
		$this->check_db();
		$this->config = array();
		$this->options = array();
		
		$variation = $this->get_current_variation();
// 		nh_print($variation, 'VARIATION');
		
		if( $this->load_from_ini( $this->config, get_stylesheet_directory().'/variations/'.$variation.'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $this->config, get_template_directory().'/variations/'.$variation.'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $this->config, get_stylesheet_directory().'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $this->config, get_template_directory().'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $this->config, get_template_directory().'/'.self::CONFIG_DEFAULT_INI_FILENAME ) );
		else exit( 'Unable to locate theme '.self::CONFIG_INI_FILENAME.' file.' );

		if( $this->load_from_ini( $this->options, get_stylesheet_directory().'/variations/'.$variation.'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $this->options, get_template_directory().'/variations/'.$variation.'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $this->options, get_stylesheet_directory().'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $this->options, get_template_directory().'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $this->options, get_template_directory().'/'.self::OPTIONS_DEFAULT_INI_FILENAME ) );
		else exit( 'Unable to locate theme '.self::OPTIONS_INI_FILENAME.' file.' );

		$nh_options = get_option( 'nh-options', array() );
		if( empty($nh_options) || !is_array($nh_options) ) $nh_options = array();
		
// 		nh_print( get_stylesheet_directory().'/variations/'.$variation.'/'.self::OPTIONS_INI_FILENAME, 'options filename' );
// 		nh_print( $this->options, 'options' );
// 		nh_print( $nh_options, 'nh-options' );
		
		$this->options = $this->merge_arrays( $this->options, $nh_options );
		$this->fix_keys($this->options);
		$this->config = $this->merge_arrays( $this->options, $this->config );
		
		$this->create_sections();
		$this->populate_widget_areas();
		
// 		nh_print( $this->config, 'CONFIG' );
// 		nh_print( $this->options, 'OPTIONS' );
		
		update_option('nh-db-version', self::DB_VERSION);
	}
	

	//------------------------------------------------------------------------------------
	// 
	// 
	// @param	$config_filname	string		The path to the config INI file.
	//------------------------------------------------------------------------------------
	private function load_from_ini( &$config, $config_filename )
	{
		if( !file_exists($config_filename) ) return false;
		
		$ini_config = parse_ini_file( $config_filename, true);		
		if( $ini_config === false ) return false;
		
		$this->fix_keys( $ini_config );
		$config = $this->merge_arrays( $config, $ini_config );
		
		return true;
    }
    
    
    public function merge_arrays( $array1, $array2, $levels = 2 )
    {
		foreach( $array2 as $key => $value )
		{
			if( !isset($array1[$key]) )
			{
				$array1[$key] = $value;
				continue;
			}
			
			if( $levels > 0 )
			{
				$array1[$key] = $this->merge_arrays( $array1[$key], $value, $levels-1 );
			}
			else
			{
				$array1[$key] = $value;
			}
		}
		
		return $array1;
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
	private function create_sections()
	{
		$this->sections = array();
		foreach( $this->config['sections'] as $key => $section_data )
		{
			if( is_array($section_data) )
				$this->sections[$key] = new NH_Section( $key, $section_data );
		}
	}
	
	
	
	public function get_sections()
	{
		return $this->sections;
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
		
		if( $part == 'mobile-menu' && is_numeric($placement) )
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
	
	
	public function get_empty_section()
	{
		return new NH_Section( '', array( 'name' => '' ) );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_section( $post_types, $taxonomies = array(), $return_null = false, $exclude_sections = array() )
	{
		
		$type_match = null;
		$partial_match = null;
		$exact_match = null;
		$best_count = 0;
		
		
		// 
		// 
		// 
		if( empty($post_types) ) $post_types = array( 'post' );
		if( !is_array($post_types) ) $post_types = array( $post_types );
		if( empty($taxonomies) ) $taxonomies = array();


		// 
		// 
		// 
		// cycle through each section looking for exact taxonomy and post type match
		foreach( $this->sections as $key => $section )
		{
			if( !$section->is_post_type($post_types) ) continue;
			
			if( !$section->has_taxonomies() )
			{
				$type_match = $section;
				continue;
			}
			
			$section_count = $section->get_taxonomy_count();
			$taxonomy_count = 0;
			$match_count = 0;
			foreach( $taxonomies as $taxname => $terms )
			{
				if( is_array($terms) )
				{
					foreach( $terms as $term )
					{
						if( $section->has_term($taxname, $term) )
						{
							$match_count++;
						}
						$taxonomy_count++;
					}
				}
				else
				{
					if( $section->has_term($taxname, $terms) )
					{
						$match_count++;
					}
					$taxonomy_count++;
				}
			}
			
			if( ($taxonomy_count == $match_count) && ($taxonomy_count == $section_count) )
			{
				$exact_match = $section;
				break;
			}
			
			if( $match_count > $best_count )
			{
				$partial_match = $section;
				$best_count = $match_count;
			}
		}
		
		
		if( $exact_match !== null ) return $exact_match;
		if( $partial_match !== null ) return $partial_match;
		if( $type_match !== null ) return $type_match;
		
		
		// 
		// Done.
		// 
		if( $return_null ) return null;
		return $this->get_default_section();
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_section_by_key( $key, $return_null = false )
	{
		if( array_key_exists($key, $this->sections) )
			return $this->sections[$key];
			
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
		
		if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
		
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
		update_option( 'nh-options', $this->options );
		$this->config = array_replace_recursive($this->config, $this->options);
		$this->create_sections();
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
		update_option( 'nh-options', array() );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function set_header( $title, $description )
	{
		$this->options['header']['title'] = $title;
		$this->options['header']['description'] = $description;
	}
	
	
	
	public function get_current_variation()
	{
		$variation = get_option( 'nh-variation', false );
		
		if( $variation === false ) return $this->reset_variation();
		
		$variations = $this->get_variations();
		foreach( $variations as $var )
		{
			if( $variation == $var ) return $variation;
		}
		
		return $this->set_variation();
	}
	
	
	public function set_variation( $name = 'default' )
	{
		update_option( 'nh-variation', $name );
		return $name;
	}
	
	public function get_variations()
	{
		$folders = array( get_template_directory().'/variations' );
		if( is_child_theme() )
			array_push( $folders, get_stylesheet_directory().'/variations' );

		return $this->get_directories( $folders );
	}


	public function get_custom_post_types()
	{
		$folders = array( get_template_directory().'/custom-post-types' );
		if( is_child_theme() )
			array_push( $folders, get_stylesheet_directory().'/custom-post-types' );

		return $this->get_directories( $folders );
	}
	
	public function use_custom_post_type( $type )
	{
		if( array_key_exists($type, $this->config['custom-post-type']) )
			return $this->config['custom-post-type'][$type];
		return false;
	}
	
	private function get_directories( $folders )
	{
		$directories = array();		
		foreach( $folders as $folder )
		{
			$files = scandir( $folder );
			foreach( $files as $file )
			{
				if( (!in_array($file, array('.','..'))) && 
				    (is_dir($folder.DIRECTORY_SEPARATOR.$file)) )
				{
					array_push( $directories, $file );
				}
			}
		}
		
		return array_unique( $directories );
	}
	
	
	public function options()
	{
		return $this->options;
	}
	
	
	
	private function check_db()
	{
// 		nh_print('check_db');

		$db_version = get_option( 'nh-db-version', '1.0' );
// 		nh_print( $db_version );
		if( $db_version == self::DB_VERSION ) return;
		
		switch( $db_version )
		{
			case '1.0':
				$this->convert_db_from_10_to_11();
			case '1.1':
				$this->convert_db_from_11_to_12();
		}
	}
	
	
	private function convert_db_from_10_to_11()
	{
// 		nh_print( 'convert_db_from_10_to_11' );

		$options = get_option('nh-theme-options', false );
		update_option('nh-options', $options);
	}
	
	
	private function convert_db_from_11_to_12()
	{
		// future use...
// 		nh_print( 'convert_db_from_11_to_12' );
	}

}

