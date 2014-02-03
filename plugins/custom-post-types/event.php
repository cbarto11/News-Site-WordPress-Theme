<?php
/**
 * 
 */
 
require_once( dirname(__FILE__).'/event-widget.php' );

add_action( 'init', array('NS_CustomEventPostType', 'create_custom_post') );
add_filter( 'post_updated_messages', array('NS_CustomEventPostType', 'update_messages') );
add_action( 'add_meta_boxes', array('NS_CustomEventPostType', 'info_box') );
add_action( 'save_post', array('NS_CustomEventPostType', 'info_box_save') );

add_filter( 'ns-event-featured-story', array('NS_CustomEventPostType', 'get_featured_story'), 99, 2 );
add_filter( 'ns-event-listing-story', array('NS_CustomEventPostType', 'get_listing_story'), 99, 2 );

add_filter( 'pre_get_posts', array('NS_CustomEventPostType', 'alter_event_query') );
add_filter( 'get_post_time', array('NS_CustomEventPostType', 'update_event_publication_date'), 9999, 3 );

class NS_CustomEventPostType
{
	/**
	 * Constructor.
	 * Private.  Class only has static members.
	 * TODO: look up PHP abstract class implementation.
	 */
	private function __construct() { }


	/**
	 * Creates the custom Event post type.
	 */	
	public static function create_custom_post()
	{
		$labels = array(
			'name'               => 'Events',
			'singular_name'      => 'Event',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Event',
			'edit_item'          => 'Edit Event',
			'new_item'           => 'New Event',
			'all_items'          => 'All Events',
			'view_item'          => 'View Event',
			'search_items'       => 'Search Events',
			'not_found'          => 'No events found',
			'not_found_in_trash' => 'No events found in the Trash',
			'parent_item_colon'  => '',
			'menu_name'          => 'Events'
		);
		
		$args = array(
			'labels'        => $labels,
			'description'   => 'Holds our events and event specific data',
			'public'        => true,
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
			'taxonomies'	=> array('category', 'post_tag'),
			'has_archive'   => true,
		);
		
		register_post_type( 'event', $args );	
	}
	
	
	/**
	 * Updates the messages displayed by the custom Event post type.
	 */
	public static function update_messages( $messages )
	{
		global $post, $post_ID;
		$messages['event'] = array(
			0 => '', 
			1 => sprintf( __('Event updated. <a href="%s">View event</a>'), esc_url( get_permalink($post_ID) ) ),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('Event updated.'),
			5 => isset($_GET['revision']) ? sprintf( __('Event restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Event published. <a href="%s">View event</a>'), esc_url( get_permalink($post_ID) ) ),
			7 => __('Event saved.'),
			8 => sprintf( __('Event submitted. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __('Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview event</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __('Event draft updated. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		);
		return $messages;
	}
	
	
	/**
	 * Sets up the custom meta box with special Event meta data tags.
	 */
	public static function info_box()
	{
		add_meta_box( 
			'event_info_box',
			'Event Info',
			array( 'NS_CustomEventPostType', 'info_box_content' ),
			'event',
			'side',
			'high'
		);
	}
	
	
	/**
	 * Writes the HTML code used to create the contents of the Event meta box.
	 * @param WP_Post The current post being displayed.
	 */
	public static function info_box_content( $post )
	{
		wp_nonce_field( plugin_basename( __FILE__ ), 'ns-custom-event-post' );

		$datetime = get_post_meta( $post->ID, 'datetime', true );
		if( !empty($datetime) )
		{
			$datetime = DateTime::createFromFormat( 'Y-m-d H:i:s', $datetime );
			$date = $datetime->format('Y-m-d');
			$time = $datetime->format('h:i A');
		}
		
		$location = get_post_meta( $post->ID, 'location', true );

		?>
		<label for="clas-event-date">Date</label><br/>
		<input type="text" id="ns-event-date" name="ns-event-date" value="<?php echo esc_attr($date); ?>" size="32" /><br/>
		<label for="clas-event-time">Time</label><br/>
		<input type="text" id="ns-event-time" name="ns-event-time" value="<?php echo esc_attr($time); ?>" size="32" /><br/>
		<label for="clas-event-location">Location</label><br/>
		<input type="text" id="ns-event-location" name="ns-event-location" value="<?php echo esc_attr($location); ?>" size="32" /><br/>
		<?php
	}
	
	
	/**
	 * Saves the Event's custom meta data.
	 * @param int The current post's id.
	 */
	public static function info_box_save( $post_id )
	{
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;
		
		if ( !wp_verify_nonce( $_POST['ns-custom-event-post'], plugin_basename( __FILE__ ) ) )
		return;
		
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;

		$datetime = DateTime::createFromFormat( 'Y-m-d h:i A', $_POST['ns-event-date'].' '.$_POST['ns-event-time'] );
		update_post_meta( $post_id, 'datetime', $datetime->format('Y-m-d H:i:s') );

		$location = $_POST['ns-event-location'];
		update_post_meta( $post_id, 'location', $location );
	}
	
	
	
	public static function get_featured_story( $story, $post )
	{
		unset($story['description']['excerpt']);

		$datetime = self::get_datetime( $post->ID );
		$story['datetime'] = $datetime['datetime'];
		$story['description']['datetime'] = $datetime['date'].', '.$datetime['time'];
		$story['description']['location'] = self::get_location( $post->ID );
		
		return $story;
	}

	public static function get_listing_story( $story, $post )
	{
		$datetime = self::get_datetime( $post->ID );
		$story['datetime'] = $datetime['datetime'];
		$story['description']['event-info'] = array();
		$story['description']['event-info']['datetime'] = $datetime['date'].', '.$datetime['time'];
		$story['description']['event-info']['location'] = self::get_location( $post->ID );
		
		return $story;
	}
	
	public static function get_datetime( $post_id )
	{
		$datetime = ns_event_get_datetime( $post_id );
		if( $datetime === null )
		{
			$date = 'No date provided.';
			$time = '';
			$datetime = null;
 		}
		else
		{
			$date = $datetime->format('F d, Y');
			$time = $datetime->format('g:i A');
		}
		
		return array( 'datetime' => $datetime, 'date' => $date, 'time' => $time );
	}

	public static function get_location( $post_id )
	{
		return get_post_meta( $post_id, 'location', true );
	}
	
	public static function alter_event_query( $wp_query )
	{
		if( ($wp_query->query['post_type'] == 'event') && 
			(!isset($wp_query->query_vars['event'])) && 
			(!is_admin()) )
		{
			//echo '<pre>';
			//var_dump($wp_query);
			//echo '</pre>';
		
			global $ns_config;
			$todays_date = $ns_config->get_todays_datetime()->format('Y-m-d');
		
			$wp_query->query_vars['meta_key'] = 'datetime';
			$wp_query->query_vars['meta_compare'] = '>=';
			$wp_query->query_vars['meta_value'] = $todays_date.' 00:00:00';
			$wp_query->query_vars['orderby'] = 'meta_value';
			$wp_query->query_vars['order'] = 'ASC';

			$wp_query->query_vars['where'] .= " AND datetime >= '" . $todays_date . " 00:00:00'";
		
			if( is_feed() )
			{
				$wp_query->query_vars['posts_per_page'] = 5;
			}
		
			if( is_post_type_archive('event') && !isset($wp_query->query_vars['section']) )
			{
				$wp_query->query_vars['posts_per_page'] = -1;
			}
		}
	}
	
	
	public static function update_event_publication_date( $time, $d, $gmt )
	{
		global $post;

		if( is_feed() && $post->post_type === 'event' )
		{
			$datetime = get_post_meta( $post->ID, 'datetime', true );
		
			if( $datetime != '' ) $time = $datetime;
		}
	
		return $time;
	}

	


}


function ns_event_get_datetime( $post_id, $format = false )
{
	$datetime = '';
	$datetime = get_post_meta( $post_id, 'datetime', true );
	if( !empty($datetime) )
	{
		$datetime = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
		if( $format ) $datetime = $datetime->format('F d, Y g:i A');
	}
	else
	{
		$datetime = null;
		if( $format ) $datetime = 'No date provided.';
	}
	
	return $datetime;
}

