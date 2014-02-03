<?php
//========================================================================================
// 
//
// @package WordPress
// @subpackage news-site
//========================================================================================
 

// 
// Set the log's file path.
//----------------------------------------------------------------------------------------
$ns_logfile = dirname(__FILE__).'/newssite.log';

// 
// Add the image sizes for thumbnails.
//----------------------------------------------------------------------------------------
add_image_size( 'thumbnail_portrait', 120 );
add_image_size( 'thumbnail_landscape', 324 );

// 
// Setup mobile support.
//----------------------------------------------------------------------------------------
require_once( get_template_directory().'/classes/mobile-support.php' );
$ns_mobile_support = new Mobile_Support;

// 
// Setup the config information.
//----------------------------------------------------------------------------------------
require_once( get_template_directory().'/classes/config.php' );
$ns_config = new NS_Config;
$ns_config->load_config();

// 
// Include the custom post types. 
//----------------------------------------------------------------------------------------
ns_include_custom_post_types();

//
// Include the admin backend. 
//----------------------------------------------------------------------------------------
require_once( dirname(__FILE__).'/plugins/admin/main.php' );

require_once( dirname(__FILE__).'/plugins/sections-widget.php' );


//========================================================================================
//====================================================== Default filters and actions =====


add_action( 'init', 'ns_setup_theme_files' );
add_action( 'init', 'ns_setup_widget_areas' );
add_action( 'after_setup_theme', 'ns_add_featured_image_support' );
add_action( 'wp_enqueue_scripts', 'ns_enqueue_scripts' );
add_action( 'admin_notices', 'ns_validate_categories_and_tags' );

add_filter( 'pre_get_posts', 'ns_alter_news_section_query' );
add_filter( 'the_posts', 'ns_alter_news_posts', 9999, 2 );


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
function ns_include_custom_post_types()
{
	global $ns_config;
	
	$custom_post_types = $ns_config->get_value('custom-post-type');
	
	foreach( $custom_post_types as $name => $use_custom_type )
	{
		if( $use_custom_type )
		{
			$filepath = ns_get_theme_file_path( '/plugins/custom-post-types/'.$name.'.php' );
			if( $filepath ) include_once( $filepath );
		}
	}
}


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
function ns_setup_theme_files()
{
	global $ns_config;
	
	// 
	// Include the Admin plugin.
	//
	//require_once( get_template_directory().'/plugins/admin/main.php' );

	// 
	// Include the custom post types. 
	//
	//require_once( get_template_directory().'/plugins/custom-post-types/main.php' );
}


//----------------------------------------------------------------------------------------
// Sets up the widget areas.
//----------------------------------------------------------------------------------------
function ns_setup_widget_areas()
{
	global $ns_config;
	
	$widgets = $ns_config->get_widget_areas();
	
	$widget_area = array();
	$widget_area['before_widget'] = '<div id="%1$s" class="widget section-box %2$s">';
	$widget_area['after_widget'] = '</div>';
	$widget_area['before_title'] = '<h2 class="widget-title">';
	$widget_area['after_title'] = '</h2>';

	//ns_print($widgets);

	foreach( $widgets as $widget )
	{
		$widget_area['name'] = $widget['name'];
		$widget_area['id'] = $widget['id'];
		register_sidebar( $widget_area );
	}
}


//----------------------------------------------------------------------------------------
// Enqueue any needed css or javascript files.
//----------------------------------------------------------------------------------------
function ns_enqueue_scripts()
{
	global $ns_mobile_support, $ns_config;
	
	wp_enqueue_script( 'jquery' );
	ns_enqueue_files( 'style', 'main-style', '/style.css' );
	
	if( $ns_mobile_support->use_mobile_site )
	{
		ns_enqueue_file( 'script', 'mobile-menu', '/scripts/mobile-menu.js' );
		ns_enqueue_files( 'style', 'mobile-site', '/styles/mobile-site.css');
	}
	else
	{
		ns_enqueue_files( 'style', 'full-site', '/styles/full-site.css');
	}
	
	if( is_front_page() )
	{
		ns_enqueue_file( 'script', 'nivo-slider', '/scripts/nivo-slider/jquery.nivo.slider.js' );
		ns_enqueue_file( 'style', 'nivo-slider', '/scripts/nivo-slider/nivo-slider.css' );
		ns_enqueue_file( 'style', 'nivo-slider-default-theme', '/scripts/nivo-slider/themes/default/default.css' );
	}
}


//----------------------------------------------------------------------------------------
// Enqueues the theme version of the the file specified.
// 
// @param	$type		string		The type of file to enqueue (script or style).
// @param	$name		string		The name to give te file.
// @param	$filepath	string		The relative path to filename.
//----------------------------------------------------------------------------------------
function ns_enqueue_files( $type, $name, $filepath )
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


//----------------------------------------------------------------------------------------
// Enqueues the theme version of the the file specified.
// 
// @param	$type		string		The type of file to enqueue (script or style).
// @param	$name		string		The name to give te file.
// @param	$filepath	string		The relative path to filename.
//----------------------------------------------------------------------------------------
function ns_enqueue_file( $type, $name, $filepath )
{
	if( $type !== 'script' && $type !== 'style' ) return;
	
	$theme_filepath = ns_get_theme_file_url($filepath);
	
	if( $theme_filepath !== null )
	{
		call_user_func( 'wp_register_'.$type, $name, $theme_filepath );
		call_user_func( 'wp_enqueue_'.$type, $name );
	}
}


//----------------------------------------------------------------------------------------
// Adds support for featured images.
//----------------------------------------------------------------------------------------
function ns_add_featured_image_support()
{
	add_theme_support( 'post-thumbnails' );
}


//----------------------------------------------------------------------------------------
// Validates that the correct categories and tags are present in the WordPress site.
//----------------------------------------------------------------------------------------
function ns_validate_categories_and_tags()
{
	global $ns_config;
	
	$terms = $ns_config->get_categories();
	foreach( $terms as $slug => $name )
	{
		$term = get_category_by_slug( $slug );
		if( $term == null || $term == false )
		{
			?>
			<div class="error"><p>No category found with <b><?php echo $slug; ?></b> slug.</p></div>
			<?php
			continue;
		}
		
		if( htmlspecialchars_decode($term->name) != $name )
		{
			?>
			<div class="updated"><p>The <b><?php echo $slug; ?></b> category is not named <b><?php echo $name; ?></b>.</p></div>
			<?php
			continue;
		}
	}

	$terms = $ns_config->get_tags();
	foreach( $terms as $slug => $name )
	{
		$term = ns_get_tag_by_slug( $slug );
		if( $term == null || $term == false )
		{
			?>
			<div class="error"><p>No tag found with <b><?php echo $slug; ?></b> slug.</p></div>
			<?php
			continue;
		}
		
		if( htmlspecialchars_decode($term->name) != $name )
		{
			?>
			<div class="updated"><p>The <b><?php echo $slug; ?></b> tag is not named <b><?php echo $name; ?></b>.</p></div>
			<?php
			continue;
		}
	}
}


//----------------------------------------------------------------------------------------
// Clears the log file.
//----------------------------------------------------------------------------------------
function ns_clear_log()
{
	file_put_contents( $ns_logfile, '' );
}


//----------------------------------------------------------------------------------------
// Writes a line into the log file.
// 
// @param	$line		string		A line of text to write into a file.
//----------------------------------------------------------------------------------------
function ns_write_to_log( $line )
{
	file_put_contents( $ns_logfile, $line."\n", FILE_APPEND );
}


//----------------------------------------------------------------------------------------
// Writes an object to the page with <pre> tags.
// 
// @param	$var		mixed		An object to var_dump.
//----------------------------------------------------------------------------------------
function ns_print( $var, $label = '' )
{
	echo '<pre style="display:block; clear:both;">';
	if( $label !== '' ) echo $label.": \n";
	var_dump($var);
	echo '</pre>';
}


//----------------------------------------------------------------------------------------
// Retreives the absolute path to a file within the theme.
// 
// @param	$filepath	string		The relative path within the theme to the file.
// @return				string|null	The absolute path to the file in the theme.
//----------------------------------------------------------------------------------------
function ns_get_theme_file_path( $filepath )
{
	if( file_exists(get_stylesheet_directory().'/'.$filepath) )
		return get_stylesheet_directory().'/'.$filepath;

	if( file_exists(get_template_directory().'/'.$filepath) )
		return get_template_directory().'/'.$filepath;
	
	return null;
}


//----------------------------------------------------------------------------------------
// Retreives the absolute url to a file within the theme.
// 
// @param	$filepath	string		The relative path within the theme to the file.
// @return				string|null	The absolute path to the file in the theme.
//----------------------------------------------------------------------------------------
function ns_get_theme_file_url( $filepath )
{
	if( file_exists(get_stylesheet_directory().'/'.$filepath) )
		return get_stylesheet_directory_uri().'/'.$filepath;

	if( file_exists(get_template_directory().'/'.$filepath) )
		return get_template_directory_uri().'/'.$filepath;
	
	return null;
}


//----------------------------------------------------------------------------------------
// Find, then includes the template part.
// 
// @param	$name		string		The name of the template part.
//----------------------------------------------------------------------------------------
function ns_get_template_part( $name, $folder = '', $key = '' )
{
	$folder = 'templates/'.$folder.'/';
	
	if( $key )
	{
		$filepath = ns_get_theme_file_path( $folder.$name.'-'.$key.'.php' );
	}
	
	if( $filepath === null )
	{
		$filepath = ns_get_theme_file_path( $folder.$name.'.php' );
	}
	
	if( $filepath !== null )
		include( $filepath );
}


//----------------------------------------------------------------------------------------
// Retreives a tag object based on the slug.
//
// @param	$slug		string		The slug/name of the tag.
// @return				mixed		Term Row (array) or false if not found.
//----------------------------------------------------------------------------------------
function ns_get_tag_by_slug( $slug )
{
	return get_term_by( 'slug', $slug, 'post_tag' );
}



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
function ns_get_anchor( $url, $title, $class = null, $contents = null )
{
	$anchor = '<a href="'.$url.'" title="'.htmlentities($title).'"';
	//if( strpos( $url, 'uncc.edu' ) === false ) $anchor .= ' target="_blank"';
	if( $class ) $anchor .= ' class="'.$class.'"';
	$anchor .= '>';

	if( $contents !== null )
		$anchor .= $contents.'</a>';

	return $anchor;
}


//----------------------------------------------------------------------------------------
// Gets the current datetime for the current timezone.
//
// @return				DateTime	The current datetime.
//----------------------------------------------------------------------------------------
function ns_get_current_datetime()
{
	global $ns_config;
	$timezone = $ns_config->get_timezone();
	date_default_timezone_set($timezone);
	return ( new Datetime() );
}


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
function ns_use_widget( $part, $placement )
{
	global $ns_config;
	if( $ns_config->use_widget($part, $placement) )
	{
		if( !function_exists('dynamic_sidebar') || !dynamic_sidebar($part.'-'.$placement) ): endif;
	}
}


//----------------------------------------------------------------------------------------
// 
//----------------------------------------------------------------------------------------
function ns_image( $image_info, $echo = true )
{
	if( empty($image_info) ) return;
	
	global $ns_mobile_support;

	$image_info = ns_get_image_info( $image_info );
	
	$html = '<img src="'.$image_info['url'].'" alt="'.$image_info['title'].'" class="'.$image_info['key'].'" />';

	if( !empty($image_info['link']) )
		$html = ns_get_anchor( $image_info['link'], $image_info['title'], $image_info['key'], $html );

	if( $echo ) echo $html;
	else return $html;
}


//----------------------------------------------------------------------------------------
// Retreives an image's url.
// 
// @param	$path		string		The absolute or relative path to the image.
// @return				string|null	The absolute url to the image.
//----------------------------------------------------------------------------------------
function ns_get_image_url( $path )
{
	if( is_array($path) ) $path = $path['url'];
	
	$url = '';
	if( $ns_mobile_support->use_mobile_site )
	{
		$pathinfo = pathinfo( $path );
		$url = ns_get_theme_file_url( $pathinfo['dirname'].'/'.$pathinfo['filename'].'-mobile.'.$pathinfo['extension'] );
	}
	
	if( $url ) return $url;
	return ns_get_theme_file_url($path);
}


function ns_get_image_info( $image_info )
{
	if( !$image_info ) return $image_info;
	
	$image_info['height'] = 'auto';
	$image_info['width'] = 'auto';
	$image_info['path'] = '';

	$pathinfo = pathinfo( $image_info['url'] );
	if( !$pathinfo ) return $image_info;	
	
	$full_path = ''; $path = ''; $url = '';
	if( $ns_mobile_support->use_mobile_site )
	{
		$path = $pathinfo['dirname'].'/'.$pathinfo['filename'].'-mobile.'.$pathinfo['extension'];
		$full_path = ns_get_theme_file_path( $path );
		
		if( $path !== null ) 
			$url = ns_get_theme_file_url( $path );
	}

	if( !$url )
	{
		$path = $pathinfo['dirname'].'/'.$pathinfo['filename'].'.'.$pathinfo['extension'];
		$full_path = ns_get_theme_file_path( $path );

		if( $path !== null ) 
			$url = ns_get_theme_file_url( $path );
	}
	
	if( !$url ) return $image_info;
	
	$image_info['path'] = $full_path;
	$image_info['url'] = $url;

	$image_size = getimagesize( $image_info['path'] );
	$image_info['width'] = $image_size[0];
	$image_info['height'] = $image_size[1];
	
	return $image_info;
}


function ns_str_starts_with($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function ns_str_ends_with($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}


/**
 * Alters the default query made when querying the News section.
 */
function ns_alter_news_section_query( $wp_query )
{
	if( (!isset($wp_query->query['category_name'])) || 
	    ($wp_query->query['category_name'] !== 'news') ||
	    (!isset($wp_query->query['category'])) )
	{
		return;
	}
	
	$news_id = get_cat_ID( 'news' );
	if( ($wp_query->query['category'] !== $news_id) ||
	    (!is_array($wp_query->query['category'])) || 
	    (!in_array($news_id, $wp_query->query['category'])) )
	{
		return;
	}

	if( is_feed() )
	{
		$wp_query->query_vars['posts_per_page'] = 5;
	}
}



/**
 * 
 */
function ns_alter_news_posts( $posts, $wp_query )
{
	global $ns_config;

	if( (!isset($wp_query->query['category_name'])) || 
	    ($wp_query->query['category_name'] !== 'news') ||
	    (!isset($wp_query->query['category'])) )
	{
		return $posts;
	}
	
	$news_id = get_cat_ID( 'news' );
	if( ($wp_query->query['category'] !== $news_id) ||
	    (!is_array($wp_query->query['category'])) || 
	    (!in_array($news_id, $wp_query->query['category'])) )
	{
		return $posts;
	}

	$section = $ns_config->get_section_by_key( 'news' );

	if( is_feed() )
		$posts = $section->get_stories('rss-feed', $posts);
	else if( is_front_page() )
		$posts = $section->get_stories('front-page', $posts);
	else
		$posts = $section->get_stories('listing', $posts);

	if( is_feed() )
	{
		for( $i = 0; $i < count($posts); $i++ )
		{
			$publication_date = date( 'Y-m-d H:i:s', time() - ($i * 86400) );
			$posts[$i]->post_date = $posts[$i]->post_date_gmt = $posts[$i]->post_modified = $posts[$i]->post_modified_gmt = $publication_date;
		}
	}
	
	return $posts;
}

