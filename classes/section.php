<?php

//========================================================================================
// 
// 
// 
//========================================================================================
class NS_Section
{
	public $key;
	public $name;
	public $type;
	public $category;
	public $tag;
	public $title;
	public $featured_image;
	public $thumbnail_image;
	public $num_stories;


	
	//------------------------------------------------------------------------------------
	// Default Constructor.
	// Setup the section's data.
	//------------------------------------------------------------------------------------
	public function __construct( $key, $section )
	{
		$this->key = $key;
		$this->name = $section['name'];
		$this->type = ( isset($section['type']) ? $section['type'] : 'post' );
		$this->category = ( isset($section['category']) ? $section['category'] : '' );
		$this->tag = ( isset($section['tag']) ? $section['tag'] : '' );
		$this->title = ( isset($section['title']) ? $section['title'] : strtoupper($this->name) );
		$this->featured_image = ( isset($section['featured-image']) ? $section['featured-image'] : 'none' );
		$this->thumbnail_image = ( isset($section['thumbnail-image']) ? $section['thumbnail-image'] : $this->featured_image );
		
		$this->num_stories = array();
		$this->num_stories['front-page'] = ( isset($section['front-page-num-stories']) ? intval($section['front-page-num-stories']) : 0 );
		$this->num_stories['archive-page'] = ( isset($section['archive-page-num-stories']) ? intval($section['archive-page-num-stories']) : 0 );
		$this->num_stories['rss-feed'] = ( isset($section['rss-feed-num-stories']) ? intval($section['rss-feed-num-stories']) : 0 );
	}


	//------------------------------------------------------------------------------------
	// Get a single post.
	// 
	// @param	$offset		int				The offset of the post.
	// @param	$omit_ids	array			A list of ids to include omit.
	// @return				WP_Post|null	The requested post or null if not found.
	//------------------------------------------------------------------------------------
	public function get_post( $offset = 0, $omit_ids = array() )
	{
		$query = array(
			'numberposts' => 1,
			'post_type' => $this->type,
			'category_name' => $this->category,
			'tag' => $this->tag,
			'offset' => $offset,
			'post__not_in' => $omit_ids,
			'section' => $this
		);
	
		$posts = get_posts( $query );
	
		if( is_array($posts) && (count($posts) > 0) )
			return $posts[0];

		return null;
	}


	//------------------------------------------------------------------------------------
	// Get a list of posts.
	// 
	// @param	$offset		int				The offset of the post.
	// @param	$limit		int				The number of posts to retrieve.
	// @param	$omit_ids	array			A list of ids to include omit.
	// @return				array			A array of WP_Post objects.
	//------------------------------------------------------------------------------------
	public function get_post_list( $offset = 0, $limit = 10, $omit_ids = array() )
	{
		$category = null;
		if( $this->category !== '' )
		{
			$category = get_category_by_slug( $this->category );
	
			if( ($category === null) || ($category == false) ) 
				return array();
				
			$category = $category->term_id;
		}

		$query = array(
			'numberposts' => $limit,
			'post_type' => $this->type,
			'category' => $category,
			'tag' => $this->tag,
			'offset' => $offset,
			'post__not_in' => $omit_ids,
			'post_status' => 'publish',
			'section' => $this,
			'suppress_filters' => false
		);
	
		return get_posts( $query );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_stories( $type = 'front-page' )
	{
		$stories_option = get_option( 'ns-'.$type.'-stories', array() );
		$recent_posts = $this->get_post_list( 0, $this->num_stories[$type] );
		
		if( array_key_exists($this->key, $stories_option) )
		{
			$stories_ids = $stories_option[$this->key];
			$story_posts = array();
			
			foreach( $stories_ids as $post_id )
			{
				$post_id = -1;
				if( count($stories_ids) > $i )
					$post_id = $stories_ids[$i];

				$post = null;
				if( $post_id !== -1 )
				{
					for( $p = 0; $p < count($recent_posts); $p++ )
					{
						if( $recent_posts[$p]->ID === $post_id )
						{
							$post = $recent_posts[$p];
							unset($recent_posts[$p]);
							break;
						}
					}
					
					if( $post === null )
						$post = get_post( $post_id );
				}
				elseif( count($recent_posts) > 0 )
				{
					$post = $recent_posts[0];
					unset($recent_posts[0]);
				}
				
				if( $post === null ) continue;
				
				$story_posts[] = $post;
			}

			$story_posts = array_slice( array_merge($story_posts, $recent_posts), 0, $this->num_stories[$type] );
		}
		else
		{
			$story_posts = $recent_posts;
		}
		
		$stories = array();
		switch( $type )
		{
			case 'front-page':
				foreach( $story_posts as $post )
				{
					$stories[] = $this->get_featured_story( $post );
				}
				break;
				
			case 'archive-page':
				foreach( $story_posts as $post )
				{
					$stories[] = $this->get_archive_story( $post );
				}
				break;
				
			case 'rss-feed':
				$stories = $story_posts;
				foreach( $story_posts as $post )
				{
					$stories[] = $this->get_rss_story( $post );
				}
				break;
		}
		
		
		return $stories;
	}


	//------------------------------------------------------------------------------------
	// TODO: alter this...
	//------------------------------------------------------------------------------------
	 public function get_section_link()
	 {
	 	$link = '';
		
		if( $this->key == 'none' || $this->key == 'multi' )
			return null;
		
		if( $this->type === 'post' )
		{
			if( !empty($this->category) )
			{
				$category = get_category_by_slug( $this->category );
				$link = get_category_link( $category->term_id );
			}
			else
			{
				$link = '';
			}
		}
		else
		{
			if( !empty($this->category) )
			{
				$link = '';
			}
			else
			{
				$link = get_site_url().'/'.$this->type;
			}
		}
		
		return $link;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_featured_story( $post )
	{
		if( empty($post) ) return null;

		$story = array();
		$story['title'] = $this->get_title( $post );
		$story['link'] = $this->get_link( $post );
		//$story['target'] = $this->get_target( $story['link'] );
		$story['image'] = $this->get_image( $post->ID, 'thumbnail' );
		$story['description'] = array();
		$story['description']['excerpt'] = $this->get_excerpt( $post );
		
		$story = apply_filters( 'ns-excerpt-story', $story, $post );
		$story = apply_filters( 'ns-'.$this->key.'-excerpt-story', $story, $post );

		return $story;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_listing_story( $post )
	{
		if( empty($post) ) return null;
	
		$story = array();
		$story['title'] = $this->get_title( $post );
		$story['link'] = $this->get_link( $post );
		//$story['target'] = $this->get_target( $story['link'] );
		$story['image'] = $this->get_image( $post->ID, 'thumbnail' );
		$story['description'] = array();
		$story['description']['excerpt'] = $this->get_excerpt( $post );

		$story = apply_filters( 'ns-archive-story', $story, $post );
		$story = apply_filters( 'ns-'.$this->key.'-archive-story', $story, $post );
		
		return $story;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_rss_story( $post )
	{
		$story = $post;
		
		$story = apply_filters( 'ns-rss-story', $story, $post );
		$story = apply_filters( 'ns-'.$this->key.'-rss-story', $story, $post );

		return $story;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_single_story( $post )
	{
		$story = array();
		$story['title'] = $this->get_title( $post );
		$story['image'] = $this->get_image( $post->ID, 'featured' );
		$story['description'] = array();
		$story['description']['text'] = $this->get_content( $post );

		$story = apply_filters( 'ns-single-story', $story, $post );
		$story = apply_filters( 'ns-'.$this->key.'-single-story', $story, $post );

		return $story;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_title( $post )
	{
		$title = $post->post_title;
		
		$title = apply_filters( 'ns-story-title', $title, $post );
		$title = apply_filters( 'ns-'.$this->key.'-story-title', $title, $post );

		return $title;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_link( $post )
	{
		$link = get_permalink( $post->ID );
		
		$link = apply_filters( 'ns-story-link', $link, $post );
		$link = apply_filters( 'ns-'.$this->key.'-story-link', $link, $post );
		
		return $link;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_link_target( $link )
	{
		$target = '';

		$target = apply_filters( 'ns-story-link-target', $target, $link );
		$target = apply_filters( 'ns-'.$this->key.'-story-link-target', $target, $link );

		return $target;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_excerpt( $post )
	{
		if( !empty($post->post_excerpt) )
		{
			$excerpt = $post->post_excerpt;
		}
		else
		{
			$excerpt = $this->get_content( $post );
			$excerpt = strip_tags($excerpt);
			if( strlen($excerpt) > 140 )
			{
				$excerpt = substr($excerpt, 0, 140);
				$excerpt = substr($excerpt, 0, strripos($excerpt, " "));
				$excerpt .= ' [&hellip;]';
			}
		}
		
		$excerpt = apply_filters( 'ns-story-excerpt', $excerpt, $post );
		$excerpt = apply_filters( 'ns-'.$this->key.'-story-excerpt', $excerpt, $post );

		return $excerpt;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_content( $post )
	{
		$content = apply_filters( 'the_content', $post->post_content );
		
		$content = apply_filters( 'ns-story-content', $content, $post );
		$content = apply_filters( 'ns-'.$this->key.'-story-content', $content, $post );

		return $content;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_featured_image( $post_id, $image_type )
	{
		global $ns_mobile_support;
		
		switch( $image_type )
		{
			case 'featured':
				if( $this->featured_image == 'none' ) return null;
				if( $ns_mobile_support->use_mobile_site )
					$image_type = 'thumbnail_'.$this->featured_image;
				else
					$image_type = 'featured_'.$this->featured_image;
				break;
				
			case 'thumbnail':
				if( $this->thumbnail_image == 'none' ) return null;
				if( $ns_mobile_support->use_mobile_site )
					$image_type = 'thumbnail';
				else
					$image_type = 'thumbnail_'.$this->featured_image;
				break;
				
			default:
				return null;
				break;
		}
		
		$imageinfo = wp_get_attachment_image_src(
			get_post_thumbnail_id( $post_id ), $image_type
		);
		
		if( $imageinfo ) 
			return $imageinfo[0];
		
		return null;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_search_results( $search_text )
	{
		global $wpdb;

		$category = null;
		$category_sql = '';
		if( $this->category !== '' )
		{
			$category = get_category_by_slug( $this->category );
	
			if( ($category === null) || ($category == false) ) 
				return array();
				
			$category = $category->term_id;
			
			$category_sql = "
				INNER JOIN $wpdb->term_relationships 
				  ON (ptable.ID = $wpdb->term_relationships.object_id)
				INNER JOIN $wpdb->term_taxonomy
				  ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) 
				    AND $wpdb->term_taxonomy.taxonomy = 'category'
				    AND $wpdb->term_taxonomy.term_id = $category
			";
		}
		
		$sql = "
			SELECT ID, post_title
			FROM $wpdb->posts as ptable
			$category_sql
			WHERE ptable.post_status = 'publish'
			  AND ptable.post_type = '$this->type'
			  AND ptable.post_title LIKE '%$search_text%'
			LIMIT 0, 10
			";
		$results = $wpdb->get_results( $sql, ARRAY_A );
		
		$stories = array();
		foreach( $results as $row )
		{
			$stories[] = array(
				'id' => $row['ID'],
				'title' => $row['post_title']
			);
		}
		
		return $stories;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_image( $post_id, $type )
	{
		global $ns_mobile_support;
		
		switch( $type )
		{
			case 'featured':
				if( $this->featured_image == 'none' ) return null;
				if( $ns_mobile_support->use_mobile_site )
					$image_type = 'thumbnail_'.$this->featured_image;
				else
					$image_type = 'featured_'.$this->featured_image;
				break;
				
			case 'thumbnail':
				if( $this->thumbnail_image == 'none' ) return null;
				if( $ns_mobile_support->use_mobile_site )
					$image_type = 'thumbnail';
				else
					$image_type = 'thumbnail_'.$this->featured_image;
				break;
				
			default:
				return null;
				break;
		}
		
		$image = wp_get_attachment_image_src(
			get_post_thumbnail_id( $post_id ), $image_type
		);
		
		if( $image ) 
			return $image[0];
		
		return null;
	}

}

