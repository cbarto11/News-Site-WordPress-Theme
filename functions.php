<?php
//========================================================================================
// 
//
// @package WordPress
// @subpackage news-site
//----------------------------------------------------------------------------------------
// Main setup at bottom of file.
//========================================================================================


//========================================================================================
//====================================================== Default filters and actions =====

add_action( 'init', 'nh_setup_widget_areas' );
add_action( 'after_setup_theme', 'nh_add_featured_image_support' );
add_action( 'wp_enqueue_scripts', 'nh_enqueue_scripts', 0 );

add_filter( 'pre_get_posts', 'nh_alter_main_query' );
add_filter( 'the_posts', 'nh_alter_main_posts', 10, 2 );

//----------------------------------------------------------------------------------------
// Debugging WP_Query
//----------------------------------------------------------------------------------------
/*
add_filter( 'posts_request', 'dump_request' );
if( !function_exists('dump_request') ):
function dump_request( $input )
{
	echo '<pre>';
    var_dump($input);
	echo '</pre>';

    return $input;
}
endif;
*/



//----------------------------------------------------------------------------------------
// Sets up the widget areas.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_setup_widget_areas') ):
function nh_setup_widget_areas()
{
	global $nh_config;
	
	$widgets = $nh_config->get_widget_areas();
	
	$widget_area = array();
	$widget_area['before_widget'] = '<div id="%1$s" class="widget section-box %2$s">';
	$widget_area['after_widget'] = '</div>';
	$widget_area['before_title'] = '<h2 class="widget-title">';
	$widget_area['after_title'] = '</h2>';

	//nh_print($widgets);

	foreach( $widgets as $widget )
	{
		$widget_area['name'] = $widget['name'];
		$widget_area['id'] = $widget['id'];
		register_sidebar( $widget_area );
	}
}
endif;



//----------------------------------------------------------------------------------------
// Enqueue any needed css or javascript files.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_enqueue_scripts') ):
function nh_enqueue_scripts()
{
	global $nh_mobile_support, $nh_config;
	$name = $nh_config->get_current_variation();
	$folder = 'variations/'.$name;
	
	wp_enqueue_script( 'jquery' );
	nh_enqueue_files( 'style', 'main-style', 'style.css' );
	nh_enqueue_files( 'style', 'main-style-'.$name, $folder.'/style.css' );
	
	if( $nh_mobile_support->use_mobile_site )
	{
		nh_enqueue_file( 'script', 'mobile-menu', 'scripts/mobile-menu.js' );
		nh_enqueue_files( 'style', 'mobile-site', 'styles/mobile-site.css');
		nh_enqueue_files( 'style', 'mobile-site-'.$name, $folder.'/styles/mobile-site.css');
	}
	else
	{
		nh_enqueue_files( 'style', 'full-site', 'styles/full-site.css');
		nh_enqueue_files( 'style', 'full-site-'.$name, $folder.'/styles/full-site.css');
	}
	
	if( is_front_page() )
	{
		nh_enqueue_file( 'script', 'nivo-slider', 'scripts/nivo-slider/jquery.nivo.slider.js' );
		nh_enqueue_file( 'style', 'nivo-slider', 'scripts/nivo-slider/nivo-slider.css' );
		nh_enqueue_file( 'style', 'nivo-slider-default-theme', 'scripts/nivo-slider/themes/default/default.css' );
	}
}
endif;



//----------------------------------------------------------------------------------------
// Enqueues the theme version of the the file specified.
// 
// @param	$type		string		The type of file to enqueue (script or style).
// @param	$name		string		The name to give te file.
// @param	$filepath	string		The relative path to filename.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_enqueue_files') ):
function nh_enqueue_files( $type, $name, $filepath )
{
	if( $type !== 'script' && $type !== 'style' ) return;

	$paths = array();
	
	if( file_exists(get_template_directory().'/'.$filepath) )
		$paths['p'] = get_template_directory_uri().'/'.$filepath;

	if( (is_child_theme()) && (file_exists(get_stylesheet_directory().'/'.$filepath)) )
		$paths['c'] = get_stylesheet_directory_uri().'/'.$filepath;
	
	foreach( $paths as $key => $theme_filepath )
	{	
		if( $theme_filepath !== null )
		{
			call_user_func( 'wp_register_'.$type, $name.'-'.$key, $theme_filepath );
			call_user_func( 'wp_enqueue_'.$type, $name.'-'.$key );
		}
	}
}
endif;



//----------------------------------------------------------------------------------------
// Enqueues the theme version of the the file specified.
// 
// @param	$type		string		The type of file to enqueue (script or style).
// @param	$name		string		The name to give te file.
// @param	$filepath	string		The relative path to filename.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_enqueue_file') ):
function nh_enqueue_file( $type, $name, $filepath )
{
	if( $type !== 'script' && $type !== 'style' ) return;
	
	$theme_filepath = nh_get_theme_file_url($filepath);
	
	if( $theme_filepath !== null )
	{
		call_user_func( 'wp_register_'.$type, $name, $theme_filepath );
		call_user_func( 'wp_enqueue_'.$type, $name );
	}
}
endif;



//----------------------------------------------------------------------------------------
// Adds support for featured images.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_add_featured_image_support') ):
function nh_add_featured_image_support()
{
	add_theme_support( 'post-thumbnails' );
}
endif;



//----------------------------------------------------------------------------------------
// Clears the log file.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_clear_log') ):
function nh_clear_log()
{
	global $nh_logfile;
	file_put_contents( $nh_logfile, '' );
}
endif;



//----------------------------------------------------------------------------------------
// Writes a line into the log file.
// 
// @param	$line		string		A line of text to write into a file.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_write_to_log') ):
function nh_write_to_log( $line )
{
	global $nh_logfile;
	file_put_contents( $nh_logfile, print_r($line, true)."\n", FILE_APPEND );
}
endif;



//----------------------------------------------------------------------------------------
// Writes an object to the page with <pre> tags.
// 
// @param	$var		mixed		An object to var_dump.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_print') ):
function nh_print( $var, $label = '' )
{
	echo '<pre style="display:block; clear:both;">';
	if( $label !== '' ) echo $label.": \n";
	var_dump($var);
	echo '</pre>';
}
endif;



//----------------------------------------------------------------------------------------
// Retreives the absolute path to a file within the theme.
// 
// @param	$filepath	string		The relative path within the theme to the file.
// @return				string|null	The absolute path to the file in the theme.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_get_theme_file_path') ):
function nh_get_theme_file_path( $filepath, $search_type = 'both', $return_null = true )
{
	global $nh_config;
	
	if( (strlen($filepath) > 0) && ($filepath[0] === '/') ) $filepath = substr( $filepath, 1 );
	
	if( $search_type === 'both' || $search_type === 'variation' ):
	
	if( file_exists(get_stylesheet_directory().'/variations/'.$nh_config->get_current_variation().'/'.$filepath) )
		return get_stylesheet_directory().'/variations/'.$nh_config->get_current_variation().'/'.$filepath;
	
	if( file_exists(get_template_directory().'/variations/'.$nh_config->get_current_variation().'/'.$filepath) )
		return get_template_directory().'/variations/'.$nh_config->get_current_variation().'/'.$filepath;
	
	endif;
	
	if( $search_type === 'both' || $search_type === 'theme' ):
	
	if( file_exists(get_stylesheet_directory().'/'.$filepath) )
		return get_stylesheet_directory().'/'.$filepath;
	
	if( file_exists(get_template_directory().'/'.$filepath) )
		return get_template_directory().'/'.$filepath;

	endif;
		
	if( $return_null ) return null;
	return '';
}
endif;



//----------------------------------------------------------------------------------------
// Retreives the absolute url to a file within the theme.
// 
// @param	$filepath	string		The relative path within the theme to the file.
// @return				string|null	The absolute path to the file in the theme.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_get_theme_file_url') ):
function nh_get_theme_file_url( $filepath, $return_null = true )
{
	global $nh_config;
	
	if( file_exists(get_stylesheet_directory().'/'.$nh_config->get_current_variation().'/'.$filepath) )
		return get_stylesheet_directory_uri().'/'.$nh_config->get_current_variation().'/'.$filepath;
	
	if( file_exists(get_template_directory().'/'.$nh_config->get_current_variation().'/'.$filepath) )
		return get_template_directory_uri().'/'.$nh_config->get_current_variation().'/'.$filepath;

	if( file_exists(get_stylesheet_directory().'/'.$filepath) )
		return get_stylesheet_directory_uri().'/'.$filepath;
	
	if( file_exists(get_template_directory().'/'.$filepath) )
		return get_template_directory_uri().'/'.$filepath;
	
	if( $return_null ) return null;
	return '';
}
endif;



//----------------------------------------------------------------------------------------
// 
// 
// @param	$filepath	string		The relative path within the theme to the file.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_include_files') ):
function nh_include_files( $filepath )
{
	if( is_child_theme() && file_exists(get_stylesheet_directory().'/'.$filepath) )
		include_once( get_stylesheet_directory().'/'.$filepath );
	
	if( file_exists(get_template_directory().'/'.$filepath) )
		include_once( get_template_directory().'/'.$filepath );
}
endif;



//----------------------------------------------------------------------------------------
// Find, then includes the template part.
// TODO: alter this!!
// 
// @param	$name		string		The name of the template part.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_get_template_part') ):
function nh_get_template_part( $name, $folder = '', $key = '' )
{
	global $nh_config;
	if( $folder ) $folder = 'templates/'.$folder.'/'; else $folder = 'templates/';
	
	$filepath = null;
	if( $key )
		$filepath = nh_get_theme_file_path( $folder.$name.'-'.$key.'.php' );
	
	if( $filepath === null )
		$filepath = nh_get_theme_file_path( $folder.$name.'.php' );
	
	if( $filepath !== null )
	{
		include( $filepath );
		return true;
	}
	
	return false;
}
endif;



//----------------------------------------------------------------------------------------
// Retreives a tag object based on the slug.
//
// @param	$slug		string		The slug/name of the tag.
// @return				mixed		Term Row (array) or false if not found.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_get_tag_by_slug') ):
function nh_get_tag_by_slug( $slug )
{
	return get_term_by( 'slug', $slug, 'post_tag' );
}
endif;



//----------------------------------------------------------------------------------------
// Creates the HTML for the an anchor.  If contents are provided, then the anchor will
// wrap the contents, else only the beginning anchor tag will be returned.
// 
// @param	$url		string		The url of the anchor.
// @param	$title		string		The title for the anchor.
// @param	$class		string|null	The class for the anchor, if any.
// @param	$contents	string|null	The contents wrapped by the anchor.
// @return				string		The created anchor tag.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_get_anchor') ):
function nh_get_anchor( $url, $title, $class = null, $contents = null )
{
	if( $url === null ) return $contents;
	
	$anchor = '<a href="'.$url.'" title="'.htmlentities($title).'"';
	if( $class ) $anchor .= ' class="'.$class.'"';
	$anchor .= '>';

	if( $contents !== null )
		$anchor .= $contents.'</a>';

	return $anchor;
}
endif;



//----------------------------------------------------------------------------------------
// Gets the current datetime for the current timezone.
//
// @return				DateTime	The current datetime.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_get_current_datetime') ):
function nh_get_current_datetime()
{
	global $nh_config;
	$timezone = $nh_config->get_timezone();
	date_default_timezone_set($timezone);
	return ( new Datetime() );
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('nh_use_widget') ):
function nh_use_widget( $part, $placement )
{
	global $nh_config;
	
	if( !function_exists('dynamic_sidebar') ) return;
	
	if( is_front_page() )
	{
		$p = 'front-page-'.$placement;
		if( $nh_config->use_widget($part, $p) ) dynamic_sidebar( $part.'-'.$p );
	}
	
	if( $nh_config->use_widget($part, $placement) ) dynamic_sidebar( $part.'-'.$placement );
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('nh_image') ):
function nh_image( $image_info, $echo = true )
{
	global $nh_mobile_support;

	if( empty($image_info) ) return;
	
	if( !isset($image_info['selection-type']) ) $image_info['selection-type'] = 'relative';
	if( !isset($image_info['use-site-link']) ) $image_info['use-site-link'] = false;
	
	switch( $image_info['selection-type'] )
	{
		case 'relative':
			$image_info['path'] = nh_get_theme_file_url( $image_info['path'] );
			break;

		case 'media':
			$image_info['path'] = wp_get_attachment_url( $image_info['attachment-id'] );
			break;
		
		default: return;
	}
	
	$html = '<img src="'.$image_info['path'].'" alt="'.$image_info['title'].'" class="'.$image_info['class'].'" />';

	if( isset($image_info['use-site-link']) && ($image_info['use-site-link'] === true) )
	{
		$image_info['link'] = get_home_url();
	}
	
	if( !empty($image_info['link']) )
		$html = nh_get_anchor( $image_info['link'], $image_info['title'], $image_info['class'], $html );

	if( $echo ) echo $html;
	else return $html;
}
endif;



//----------------------------------------------------------------------------------------
// Retreives an image's url.
// 
// @param	$path		string		The absolute or relative path to the image.
// @return				string|null	The absolute url to the image.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_get_image_url') ):
function nh_get_image_url( $path )
{
	global $nh_mobile_support;
	
	if( is_array($path) ) $path = $path['url'];
	
	$url = '';
	if( $nh_mobile_support->use_mobile_site )
	{
		$pathinfo = pathinfo( $path );
		$url = nh_get_theme_file_url( $pathinfo['dirname'].'/'.$pathinfo['filename'].'-mobile.'.$pathinfo['extension'] );
	}
	
	if( $url ) return $url;
	return nh_get_theme_file_url($path);
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('nh_get_image_info') ):
function nh_get_image_info( $image_info )
{
	global $nh_mobile_support;
	
	if( !$image_info ) return $image_info;
	
	$image_info['height'] = 'auto';
	$image_info['width'] = 'auto';
	$image_info['path'] = '';

	$pathinfo = pathinfo( $image_info['path'] );
	if( !$pathinfo ) return $image_info;	
	
	$full_path = ''; $path = ''; $url = '';
	if( $nh_mobile_support->use_mobile_site )
	{
		$path = $pathinfo['dirname'].'/'.$pathinfo['filename'].'-mobile.'.$pathinfo['extension'];
		$full_path = nh_get_theme_file_path( $path );
		
		if( $path !== null ) 
			$url = nh_get_theme_file_url( $path );
	}

	if( !$url )
	{
		$path = $pathinfo['dirname'].'/'.$pathinfo['filename'].'.'.$pathinfo['extension'];
		$full_path = nh_get_theme_file_path( $path );

		if( $path !== null ) 
			$url = nh_get_theme_file_url( $path );
	}
	
	if( !$url ) return $image_info;
	
	$image_info['path'] = $full_path;
	$image_info['url'] = $url;

	$image_size = getimagesize( $image_info['path'] );
	$image_info['width'] = $image_size[0];
	$image_info['height'] = $image_size[1];
	
	return $image_info;
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('nh_str_starts_with') ):
function nh_str_starts_with($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('nh_str_ends_with') ):
function nh_str_ends_with($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('nh_get_section') ):
function nh_get_section( $wpquery = null )
{
	global $wp_query, $nh_config;

	if( $wpquery === null ) $wpquery = $wp_query;
	if( $wpquery->get('section') ) return $wpquery->get('section');
	
	$qo = $wpquery->get_queried_object();
	
	if( $wpquery->is_archive() )
	{
		if( $wpquery->is_tax() || $wpquery->is_tag() || $wpquery->is_category() )
		{
			$section = $nh_config->get_section( null, array( $qo->taxonomy => $qo->slug ), false );
			$wpquery->set( 'section', $section );
			return $section;
		}
		elseif( $wpquery->is_post_type_archive() )
		{
			$section = $nh_config->get_section( $qo->name, null, false );
			$wpquery->set( 'section', $section );
			return $section;
		}

		return $nh_config->get_default_section();
	}
	
	if( $wpquery->is_single() )
	{
		if( $qo === null )
		{
			$post_id = $wp_query->get( 'p' );

			if( !$post_id )
			{
				global $wpdb;
				
				$post_type = $wp_query->get( 'post_type', false );
				if( !$post_type ) $post_type = 'post';
				
				$post_slug = $wp_query->get( 'name', false );
				
				if( $post_slug !== false )
					$post_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_type = '$post_type' AND post_name = '$post_slug'" );
			}
		}
		else
		{
			$post_id = $qo->ID;
		}
		
		if( $post_id )
		{
			$post_type = get_post_type( $post_id );
			$taxonomies = nh_get_taxonomies( $post_id );
			$section = $nh_config->get_section( $post_type, $taxonomies, false, array('news') );
		}
		else
		{
			$section = $nh_config->get_default_section();
		}
		
		$wpquery->set( 'section', $section );
		return $section;
	}
	
	return $nh_config->get_default_section();
}
endif;



//----------------------------------------------------------------------------------------
// Alters the default query made when querying the News section.
//----------------------------------------------------------------------------------------
if( !function_exists('nh_alter_main_query') ):
function nh_alter_main_query( $wp_query )
{
	if( is_admin() || !$wp_query->is_main_query() ) return;

	$section = nh_get_section();
	if( $section->key === 'none' ) return;

	if( is_archive() )
	{
		if( is_feed() )
			$wp_query->set( 'posts_per_page', $section->num_stories['rss-feed'] );
		else
			$wp_query->set( 'posts_per_page', $section->num_stories['listing'] );
	}
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('nh_alter_main_posts') ):
function nh_alter_main_posts( $posts, $wp_query )
{
	if( is_admin() || !$wp_query->is_main_query() ) return $posts;
	
	$section = nh_get_section();
	if( $section->key === 'none' ) return $posts;
	
	if( is_archive() )
	{
		if( is_feed() )
			$posts = $section->get_stories( 'rss-feed', $posts, false );
		else
			$posts = $section->get_stories( 'listing', $posts, false );
	}
	
	return $posts;
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('nh_get_categories') ):
function nh_get_categories( $categories = null )
{
	if( $categories == null )
		$categories = get_the_category();

	$category = array();
	if( $categories )
	{
		foreach( $categories as $c ) $category[] = $c->slug;
	}
	
	return $category;
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('nh_get_tags') ):
function nh_get_tags( $tags = null )
{
	if( $tags == null )
		$tags = get_the_tags();
	
	$tag = array();	
	if( $tags )
	{
		foreach( $tags as $t ) $tag[] = $t->slug;
	}
	
	return $tag;
}
endif;



//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
if( !function_exists('nh_get_taxonomies') ):
function nh_get_taxonomies( $post_id = -1 )
{
	global $post;
	
	if( $post_id == -1 )
		$post_id = $post->ID;
		
	$all_taxonomies = get_taxonomies( '', 'names' );
	
	$taxonomies = array();
	foreach( $all_taxonomies as $taxname )
	{
		$terms = wp_get_post_terms( $post_id, $taxname, array('fields' => 'slugs') );
		if( count($terms) > 0 )
			$taxonomies[$taxname] = $terms;
	}
	
	return $taxonomies;
}
endif;



//========================================================================================
//======================================================================= Main Setup =====

// 
// Set the log's file path.
//----------------------------------------------------------------------------------------
$nh_logfile = dirname(__FILE__).'/newshub.log';

// 
// Set blog name.
//----------------------------------------------------------------------------------------
define( 'NH_BLOG_NAME', trim( preg_replace("/[^A-Za-z0-9 ]/", '-', get_blog_details()->path), '-' ) );

// 
// Add the image sizes for thumbnails.
//----------------------------------------------------------------------------------------
add_image_size( 'thumbnail_portrait', 120 );
add_image_size( 'thumbnail_landscape', 324 );

// 
// Setup mobile support.
//----------------------------------------------------------------------------------------
require_once( get_template_directory().'/classes/mobile-support.php' );
$nh_mobile_support = new Mobile_Support;

// 
// Setup the config information.
//----------------------------------------------------------------------------------------
require_once( get_template_directory().'/classes/config.php' );
$nh_config = new NH_Config;
$nh_config->load_config();

//
// Include the admin backend. 
//----------------------------------------------------------------------------------------
if( is_admin() ):

require_once( get_template_directory().'/admin/main.php' );
if( (is_child_theme()) && (file_exists(get_stylesheet_directory().'/admin/main.php')) ) 
	require_once( get_stylesheet_directory().'/admin/main.php' );

$filepath = nh_get_theme_file_path( '/admin/main.php', 'variation' );
if( $filepath ) require_once( $filepath );

endif;

//
// Include widgets.
//----------------------------------------------------------------------------------------
require_once( dirname(__FILE__).'/widgets/sections-widget.php' );

// 
// Include custom post types.
//----------------------------------------------------------------------------------------
$custom_post_types = $nh_config->get_value( 'custom-post-type' );
if( $custom_post_types !== null ):
foreach( $custom_post_types as $name => $use_custom_type )
{
	if( $use_custom_type )
	{
		$filepath = nh_get_theme_file_path( 'custom-post-types/'.$name.'/'.$name.'.php' );
		if( $filepath ) include_once( $filepath );
	}
}
endif;

// 
// Include variation's functions.php
//----------------------------------------------------------------------------------------
$filepath = nh_get_theme_file_path( 'variations/'.$nh_config->get_current_variation().'/functions.php' );
if( $filepath ) require_once( $filepath );


