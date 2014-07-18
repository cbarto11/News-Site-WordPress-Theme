<?php

require_once( dirname(__FILE__).'/section.php' );

//========================================================================================
// 
// 
// 
//========================================================================================
class NH_Config
{

//========================================================================================
//======================================================================= Properties =====
	
	
	// current database version
	const DB_VERSION = '1.4';
	
	// relative paths to the config and otions files.
	const CONFIG_DEFAULT_INI_FILENAME = 'config/config-default.ini';
	const CONFIG_INI_FILENAME = 'config/config.ini';
	const OPTIONS_DEFAULT_INI_FILENAME = 'config/options-default.ini';
	const OPTIONS_INI_FILENAME = 'config/options.ini';
	
	// complete set of data with config and options
	private $data;
	
	// config from config.ini
	private $config;
	
	// options from options.ini and database options
	private $options;
	
	// converted section info from data into section objects
	private $sections;
	

//========================================================================================
//====================================================================== Constructor =====


	//------------------------------------------------------------------------------------
	// Default Constructor.
	//------------------------------------------------------------------------------------
	public function __construct() { }
	

//========================================================================================
//================================================================ Load Configuration ====

	
	//------------------------------------------------------------------------------------
	// Loads the default configuration, then configuration file, if exists.
	//------------------------------------------------------------------------------------
	public function load_config()
	{
		$this->check_db();

		$variation = $this->get_current_variation();
		
		$config_ini = array();
		$options_ini = array();
		$db_options = array();
		
		// 
		// load config.ini data.
		// 
		if( $this->load_from_ini( $config_ini, get_stylesheet_directory().'/variations/'.$variation.'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $config_ini, get_template_directory().'/variations/'.$variation.'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $config_ini, get_stylesheet_directory().'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $config_ini, get_template_directory().'/'.self::CONFIG_INI_FILENAME ) );
		elseif( $this->load_from_ini( $config_ini, get_template_directory().'/'.self::CONFIG_DEFAULT_INI_FILENAME ) );
		else exit( 'Unable to locate theme '.self::CONFIG_INI_FILENAME.' file.' );
		
		// 
		// load options.ini data.
		// 
		if( $this->load_from_ini( $options_ini, get_stylesheet_directory().'/variations/'.$variation.'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $options_ini, get_template_directory().'/variations/'.$variation.'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $options_ini, get_stylesheet_directory().'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $options_ini, get_template_directory().'/'.self::OPTIONS_INI_FILENAME ) );
		elseif( $this->load_from_ini( $options_ini, get_template_directory().'/'.self::OPTIONS_DEFAULT_INI_FILENAME ) );
		else exit( 'Unable to locate theme '.self::OPTIONS_INI_FILENAME.' file.' );
		
		// 
		// load database options.
		// 
		$db_options = get_option( 'nh-options', array() );
		if( empty($db_options) || !is_array($db_options) ) $db_options = array();

		$replace = array( 
			'sections',
			'front-page-sections', 'sidebar-sections', 
			'front-page-stories' , 'sidebar-stories' , 'listing-stories', 'rss-feed-stories',
			'banner-slides',
		);
		
// 		if( !isset($_POST) || empty($_POST) )
// 		{
// 		nh_print( $options_ini, 'options ini' );
// 		nh_print( $db_options, 'db options' );
// 		}

		//
		// set config data.
		//
		$this->config = $config_ini;
				
		// 
		// merge options.ini and database options.
		// 
		$this->options = array_replace_recursive( $options_ini, $db_options );
		foreach( $replace as $key )
		{
			if( isset($db_options[$key]) ) { $this->options[$key] = $db_options[$key]; continue; }
			if( isset($options_ini[$key]) ) { $this->options[$key] = $options_ini[$key]; continue; }
			$this->options[$key] = array();
		}
		
		// 
		// merge config.ini with complete options.
		// 
		$this->data = array_replace_recursive( $this->options, $config_ini );
		foreach( $replace as $key )
		{
			if( isset($this->options[$key]) ) { $this->data[$key] = $this->options[$key]; continue; }
			$this->data[$key] = array();
		}

		$this->data = apply_filters( 'nh-config-merge-data', $this->data );

		//
		// convert values.
		//
		$this->convert_values( $this->data );
		$this->create_sections();
		$this->populate_widget_areas();
		
		//
		// Update the database version.
		//
		update_option( 'nh-db-version', self::DB_VERSION );
		
// 		nh_print( $config_ini, 'config-ini' );
// 		nh_print( $options_ini, 'options-ini' );
// 		nh_print( $db_options, 'db-options' );
// 		nh_print( $this->data, 'data' );
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
		
		$this->convert_values( $ini_config );
		
		if( !empty($config) ) $config = array_replace_recursive( $config, $ini_config );
		else $config = $ini_config;
		
		return true;
    }
    

	//------------------------------------------------------------------------------------
	// 
	// 
	// @param	
	//------------------------------------------------------------------------------------
    private function convert_values( &$array )
    {
		foreach( $array as $key => &$value )
		{
			if( is_array($value) )
			{
				$this->convert_values( $value );
				continue;
			}
			
			if( (strlen($value) > 2) && ($value[1] === ':') )
			{
				switch( $value[0] )
				{
					case 'b':
						$value = ( substr($value, 2) === 'true' ? true : false );
						break;
						
					case 'i':
						$value = intval( substr($value, 2) );
						break;
					
					case 'd':
						$value = doubleval( substr($value, 2) );
						break;
					
					case 'a':
						$value = substr($value, 2);
						// TODO...
						break;
				}
			}
		}
	}
    
	
//========================================================================================
//==================================================================== Template Parts ====


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function show_template_part( $template_part )
	{
		if( (array_key_exists($template_part, $this->data)) &&
		    (array_key_exists('show-part', $this->data[$template_part])) )
			return $this->data[$template_part]['show-part'];
		
		if( $this->is_optional_template_part($template_part) ) return false;
		return true;
	}


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function is_optional_template_part( $template_part )
	{
		return in_array( $template_part, $this->get_optional_template_parts() );
	}


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_optional_template_parts()
	{
		$optional_template_parts = array( 'header', 'banner', 'subheader', 'mobile-menu', 'footer' );
		$optional_template_parts = apply_filters( 'nh-config-optional-template-parts', $optional_template_parts );
		return $optional_template_parts;
	}

//========================================================================================
//=========================================================================== Widgets ====

	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function populate_widget_areas()
	{
		$widget_areas = array();
		
		foreach( $this->data as $template_part => $settings )
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
				foreach( $this->data['mobile-menu']['menu-widget'] as $widget => $name )
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
		
				
		$this->data['widget-areas'] = $widget_areas;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function use_widget( $part, $placement )
	{
		if( !array_key_exists($part, $this->data) ) return false;
		if( !$this->show_template_part($part) ) return false;
		
		if( $part == 'mobile-menu' && is_numeric($placement) )
		{
			if( (array_key_exists('menu-widget', $this->data[$part])) &&
			    (isset($this->data[$part]['menu-widget'][$placement])) )
			    return $this->data[$part]['menu-widget'][$placement];
		}
		else
		{
			if( array_key_exists($placement, $this->data[$part]['widget']) )
				return $this->data[$part]['widget'][$placement];
		}
		
		return false;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_widget_areas()
	{
		return $this->data['widget-areas'];
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_mobile_widget_areas()
	{
		$mobile_widgets = array();
		
		foreach( $this->data['mobile-menu']['menu-widget'] as $widget => $name )
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
			if( $this->data['footer']['widget']['column-'.($i+1)] )
				$footer_widgets[] = 'column-'.($i+1);
		}
		
		return $footer_widgets;
	}


//========================================================================================
//=========================================================================== Columns ====


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_column( $part, $name )
	{
		if( !array_key_exists($part, $this->data) )
			return null;
			
		if( !array_key_exists($name, $this->data[$part]) )
			return null;
			
		return $this->data[$part][$name];
	}


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_number_of_columns( $name )
	{
		if( isset($this->data['content']['num-columns'][$name]) )
			return $this->data['content']['num-columns'][$name];
			
		return 1;
	}


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_timezone()
	{
		return $this->data['timezone'];
	}


//========================================================================================
//========================================================================== Sections ====


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function create_sections()
	{
		$this->sections = array();
		foreach( $this->data['sections'] as $key => $section_data )
		{
			if( is_array($section_data) )
				$this->sections[$key] = new NH_Section( $key, $section_data );
		}
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_sections()
	{
		return $this->sections;
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
			if( in_array($key, $exclude_sections) ) continue;
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
	
	
//========================================================================================
//=========================================================================== Options ====


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_value()
	{
		$args = func_get_args();
		if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
		
		$config = $this->data;
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
	public function get_image_data()
	{
		$args = func_get_args();
		if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
		
		$image_data = $this->get_value( $args );
		if( $image_data === null ) return null;
		
		$defaults = array(
			'selection-type' => 'relative',
			'attachment-id' => -1,
			'path' => '',
			'use-site-link' => false,
			'link' => '',
		);
		
		$image_data = array_merge( $defaults, $image_data );
		return $image_data;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_text_data()
	{
		$args = func_get_args();
		if( count($args) == 1 && is_array($args[0]) ) $args = $args[0];
		
		$text_data = $this->get_value( $args );
		if( $text_data === null ) return null;
		
		$defaults = array(
			'text' => null,
			'use-site-link' => false,
			'link' => '',
		);
		
		$text_data = array_merge( $defaults, $text_data );
		return $text_data;
	}
	

	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_options()
	{
		return $this->options;
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
	public function get_banner_slides()
	{
		global $nh_mobile_support;
		
		$options = $this->get_value( 'banner-slides' );
		if( $options === null ) return array();
		
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
				'path' => $src,
				'link' => $option['link'],
				'title' => stripslashes($option['title'])
			);
		}
		
		return $images;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_todays_datetime()
	{
		if( isset($this->data['timezone']) )
			date_default_timezone_set( $this->data['timezone'] );
		$todays_datetime = new DateTime;
		return $todays_datetime;
	}
	
	
//========================================================================================
//======================================================================== Variations ====


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_current_variation()
	{
		$variation = get_option( 'nh-variation', false );
		
		if( $variation === false ) return $this->set_variation();
		
		$variations = $this->get_variations();
		foreach( $variations as $var )
		{
			if( $variation === $var ) return $variation;
		}
		
		return $this->set_variation();
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function set_variation( $name = 'default' )
	{
		update_option( 'nh-variation', $name );
		return $name;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_variations()
	{
		$folders = array( get_template_directory().'/variations' );
		if( is_child_theme() )
			array_push( $folders, get_stylesheet_directory().'/variations' );

		return $this->get_directories( $folders );
	}
	
	
//========================================================================================
//================================================================= Custom Post Types ====
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_custom_post_types()
	{
		$folders = array( get_template_directory().'/custom-post-types' );
		if( is_child_theme() )
			array_push( $folders, get_stylesheet_directory().'/custom-post-types' );

		return $this->get_directories( $folders );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function use_custom_post_type( $type )
	{
		if( array_key_exists($type, $this->data['custom-post-type']) )
			return $this->data['custom-post-type'][$type];
		return false;
	}
	

//========================================================================================
//======================================================================= Directories ====

	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
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


//========================================================================================
//========================================================================== Database ====
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function check_db()
	{
		$db_version = get_option( 'nh-db-version', false );
		if( ($db_version === false) || ($db_version === self::DB_VERSION) ) return;
		
		switch( $db_version )
		{
			case '1.0':
				$this->convert_db_from_10_to_11();
			case '1.1':
				$this->convert_db_from_11_to_12();
			case '1.2':
				$this->convert_db_from_12_to_13();
			case '1.3':
				$this->convert_db_from_13_to_14();
			case '1.4':
				// new version function here...
			default:
				break;
		}
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function convert_db_from_10_to_11()
	{
		$options = get_option( 'nh-theme-options', array() );
		update_option( 'nh-options', $options );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function convert_db_from_11_to_12()
	{
		$options = get_option( 'nh-options', array() );
		if( empty($options['banner-images']) )
		{ 
			if( !empty($options['banner']['images']) ) 
				$options['banner-images'] = $options['banner']['images'];
			else
				$options['banner-images'] = array();
		}
		unset( $options['banner']['images'] );
		update_option( 'nh-options', $options );
	}

	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function convert_db_from_12_to_13()
	{
		$options = get_option( 'nh-options', array() );
		if( isset($options['news-stories']) )
		{
			if( !isset($options['rss-feed-stories']) ) $options['rss-feed-stories'] = array();
			$options['rss-feed-stories']['news'] = $options['news-stories'];
			unset($options['news-stories']);
		}
		update_option( 'nh-options', $options );
	}
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function convert_db_from_13_to_14()
	{
		$options = get_option( 'nh-options', array() );
		if( isset($options['banner-images']) )
		{
			$options['banner-slides'] = $options['banner-images'];
			unset($options['banner-images']);
		}
		update_option( 'nh-options', $options );
	}
	
}

