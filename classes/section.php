<?php

//========================================================================================
// 
// 
// 
//========================================================================================
class NH_Section
{
	public $key;
	public $name;
	public $post_types;
	public $taxonomies;
	public $title;
	public $featured_image;
	public $thumbnail_image;
	public $num_stories;
	public $num_columns;


	
	//------------------------------------------------------------------------------------
	// Default Constructor.
	// Setup the section's data.
	//------------------------------------------------------------------------------------
	public function __construct( $key, $section )
	{
		global $nh_config;
		
// 		nh_print($section, 'section');
		
		$this->key = $key;
		$this->name = $section['name'];
		$this->title = ( isset($section['title']) ? $section['title'] : strtoupper($this->name) );
		$this->featured_image = ( isset($section['featured-image']) ? $section['featured-image'] : 'none' );
		$this->thumbnail_image = ( isset($section['thumbnail-image']) ? $section['thumbnail-image'] : $this->featured_image );

		if( isset($section['type']) )
		{
			if( is_array($section['type']) ) $this->post_types = $section['type'];
			else $this->post_types = array_unique(array_filter(explode(',',$section['type'])));
		}
		else
		{
			$this->post_types = array( 'post' );
		}
		if( count($this->post_types) == 0 ) $this->post_types = array( 'post' );
		
		$this->taxonomies = array();
		if( isset($section['taxonomies']) )
		{
			if( !is_array($section['taxonomies']) )
			{
				$section['taxonomies'] = array_filter( explode(',', $section['taxonomies']) );
			}
			
			foreach( $section['taxonomies'] as $taxname )
			{
				if( isset($section[$taxname]) )
				{
					if( !is_array($section[$taxname]) )
					{
						$section[$taxname] = array_unique( array_filter(explode(',', $section[$taxname])) );
					}
					$this->taxonomies[$taxname] = $section[$taxname];
				}
				else
				{
					$this->taxonomies[$taxname] = array();
				}
			}
		}
		else
		{
			if( isset($section['category']) )
			{
				if( !is_array($section['category']) )
				{
					$section['category'] =  array_filter( explode(',',$section['category']) );
				}
				$this->taxonomies['category'] = $section['category'];
			}
		}
		
		foreach( $this->taxonomies as $taxname => $terms )
		{
			if( count($terms) == 0 ) unset($this->taxonomies[$taxname]);
		}
		
		$this->num_stories = array();
		$this->num_stories['front-page'] = ( isset($section['front-page-num-stories']) ? $section['front-page-num-stories'] : 0 );
		$this->num_stories['sidebar'] = ( isset($section['sidebar-num-stories']) ? $section['sidebar-num-stories'] : 0 );
		$this->num_stories['listing'] = ( isset($section['listing-num-stories']) ? $section['listing-num-stories'] : 0 );
		$this->num_stories['rss-feed'] = ( isset($section['rss-feed-num-stories']) ? $section['rss-feed-num-stories'] : 0 );
		
		$this->num_columns = array();
		if( isset($section['listing-num-columns']) )
			$this->num_columns['listing'] = $section['listing-num-columns'];
	}
	
	
	public function get_number_of_columns( $page )
	{
		global $nh_config;
		
		if( array_key_exists($page, $this->num_columns) )
			return $this->num_columns[$page];
		
		if( $page === 'listing' )
			return $nh_config->get_number_of_columns($this->thumbnail_image);
		else
			return $nh_config->get_number_of_columns($page);
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
		$posts = $this->get_post_list( $offset, $omit_ids );
		if( count($posts) > 0 ) return $posts[0];
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
		$posts = array();
		
		$args = array(
			'posts_per_page' => $limit,
			'post_type' => $this->post_types,
			'post_status' => 'publish',
			'offset' => $offset,
			'post__not_in' => $omit_ids,
			'tax_query' => $this->create_tax_query(),
			'section' => $this
		);
		
		$query = new WP_Query( $args );
		
		if( $query->have_posts() )
			$posts = $query->get_posts();
		
		wp_reset_postdata();
		
		return $posts;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function create_tax_query()
	{
		$count = 0;
		$tax_query = array();
		foreach( $this->taxonomies as $taxname => $terms )
		{
			if( count($terms) > 0 )
			{
				$count++;
				array_push(
					$tax_query,
					array(
						'taxonomy' => $taxname,
						'field' => 'slug',
						'terms' => $terms,
						'operator' => 'IN',
					)
				);
			}
		}
		if( $count > 1 )
		{
			$tax_query['relation'] = 'OR';
		}
		
		return $tax_query;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_stories( $type = 'front-page', $recent_posts = null, $post_process = true )
	{
		global $nh_config;
		
		$stories_ids = $nh_config->get_value( $type.'-stories', $this->key );
		
		if( $recent_posts == null )
			$recent_posts = $this->get_post_list( 0, $this->num_stories[$type] );
		
// 		nh_print($stories_ids);
		
		$story_posts = array();
		
		if( $stories_ids == null )
		{
			$story_posts = $recent_posts;
		}
		else
		{
			foreach( $stories_ids as $post_id )
			{
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
		
		if( $post_process ):
		switch( $type )
		{
			case 'front-page':
			case 'sidebar':
				foreach( $story_posts as &$sp )
				{
					$sp = $this->get_featured_story( $sp );
				}
				break;

			case 'listing':
				foreach( $story_posts as &$sp )
				{
					$sp = $this->get_listing_story( $sp );
				}
				break;

			case 'rss-feed':
				foreach( $story_posts as &$sp )
				{
					$sp = $this->get_rss_story( $sp );
				}
				break;
		}
		endif;
		
		return $story_posts;
	}


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	 public function get_section_link()
	 {
	 	$link = null;
		
		if( $this->key == 'none' || $this->key == 'multi' )
			return null;
		
		if( count($this->post_types) == 1 )
		{
			switch( count($this->taxonomies) )
			{
				case 0:
					// post page
					if( $this->post_types[0] != 'post' )
						$link = get_site_url().'/'.$this->post_types[0];
					break;
					
				case 1:
					// taxonomy page?
					reset($this->taxonomies); $taxname = key($this->taxonomies);
					if( count($this->taxonomies[$taxname]) == 1 )
						$link = get_term_link( $this->taxonomies[$taxname][0], $taxname );
					break;
			}
		}
		
		return apply_filters( 'section-link', $link, $this );
	}
	

	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	private function apply_filters( $name, $story, $post )
	{
		//nh_print( 'nh-'.$post->post_type.'-'.$name, 'apply-filters' );
		
		$story = apply_filters( 'nh-'.$name, $story, $post );
		$story = apply_filters( 'nh-'.$this->key.'-'.$name, $story, $post );
		$story = apply_filters( 'nh-'.$post->post_type.'-'.$name, $story, $post );
		
		return $story;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_featured_story( $post )
	{
// 		nh_print($this);
		
		if( empty($post) ) return null;

		$story = array();
		$story['title'] = $this->get_title( $post );
		$story['link'] = $this->get_link( $post );
		$story['target'] = $this->get_link_target( $story['link'] );

		if( $this->thumbnail_image == 'embed' )
			$story['embed'] = $this->get_embed_code( $post->post_content );
		else
			$story['image'] = $this->get_image( $post->ID, 'thumbnail' );

		$story['description'] = array();
		$story['description']['excerpt'] = $this->get_excerpt( $post );
		
		//nh_print($story);
		
		return $this->apply_filters( 'featured-story', $story, $post );		
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
		$story['target'] = $this->get_link_target( $story['link'] );
		
		if( $this->thumbnail_image == 'embed' )
			$story['embed'] = $this->get_embed_code( $post->post_content );
		else
			$story['image'] = $this->get_image( $post->ID, 'thumbnail' );

		$story['description'] = array();
		$story['description']['excerpt'] = $this->get_excerpt( $post );

		return $this->apply_filters( 'listing-story', $story, $post );		
	}
	
		
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_rss_story( $post )
	{
		$story = $post;
		
		return $this->apply_filters( 'rss-story', $story, $post );		
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

		return $this->apply_filters( 'single-story', $story, $post );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_title( $post )
	{
		$title = $post->post_title;
		return $this->apply_filters( 'story-title', $title, $post );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_link( $post )
	{
		$link = get_permalink( $post->ID );
		return $this->apply_filters( 'story-link', $link, $post );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_link_target( $link )
	{
		$target = '';
		return $this->apply_filters( 'story-link-target', $target, $post );
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
		
		return $this->apply_filters( 'story-excerpt', $excerpt, $post );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_content( $post )
	{
		$content = $post->post_content;

		$matches = null;
		$num_matches = preg_match_all( "/(\[embed\].+?)+(\[\/embed\])/i", $content, $matches, PREG_SET_ORDER );
		
		if( ($num_matches !== FALSE) && ($num_matches > 0) )
		{
			for( $i = 0; $i < $num_matches; $i++ )
			{
				$content = str_replace( $matches[$i][0], '<div class="embed">'.$matches[$i][0].'</div>', $content );
			}
		}
		
		$content = apply_filters( 'the_content', $content );
		
		return $this->apply_filters( 'story-content', $content, $post );
	}
	
	

	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_search_results( $search_text )
	{
		$stories = array();
		
		$args = array(
			's' => $search_text,
			'posts_per_page' => 10,
			'post_type' => $this->post_types,
			'post_status' => 'publish',
			'tax_query' => $this->create_tax_query(),
			'section' => $this
		);
		
		$query = new WP_Query( $args );
		
		while( $query->have_posts() )
		{
			$query->the_post();
			array_push(
				$stories,
				array(
					'id' => get_the_ID(),
					'title' => get_the_title(),
				)
			);
		}
		
		wp_reset_postdata();
		
		return $stories;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_image( $post_id, $type )
	{
		global $nh_mobile_support;
		
		if( $this->key == 'news' )
		{
			nh_write_to_log( $type.': '.$this->thumbnail_image );
		}
		
		switch( $type )
		{
			case 'featured':
				if( $this->featured_image == 'none' ) return null;
				if( $nh_mobile_support->use_mobile_site )
					$image_type = 'thumbnail_'.$this->featured_image;
				else
					$image_type = 'featured_'.$this->featured_image;
				break;
				
			case 'thumbnail':
				if( $this->thumbnail_image == 'none' ) return null;
				if( $nh_mobile_support->use_mobile_site )
					$image_type = 'thumbnail';
				else
					$image_type = 'thumbnail_'.$this->featured_image;
				break;
			
			case 'featured_portrait':
			case 'featured_landscape':
			case 'thumbnail_portrait':
			case 'thumbnail_landscape':
				$image_type = $type;
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


	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	function get_embed_code( $content )
	{
		$embed = '';
		
		$matches = null;
		$num_matches = preg_match_all( "/(\[embed\].+?)+(\[\/embed\])/i", $content, $matches, PREG_SET_ORDER );
		
		if( ($num_matches !== FALSE) && ($num_matches > 0) )
		{
			global $wp_embed;
			return $wp_embed->run_shortcode( $matches[0][0] );
		}
		
		return $embed;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function is_post_type( $post_type )
	{
		if( is_array($post_type) )
		{
			foreach( $post_type as $type )
			{
				if( in_array($type, $this->post_types) ) return true;
			}
			return false;
		}
		return ( in_array($post_type, $this->post_types) );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function has_taxonomies()
	{
		return( count($this->taxonomies) > 0 );
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function has_term( $taxonomy, $term )
	{
		if( array_key_exists($taxonomy, $this->taxonomies) )
			return in_array( $term, $this->taxonomies[$taxonomy] );
		return false;
	}
	
	
	//------------------------------------------------------------------------------------
	// 
	//------------------------------------------------------------------------------------
	public function get_taxonomy_count()
	{
		$count = 0;
		
		foreach( $this->taxonomies as $taxname => $terms )
		{
			$count += count($this->taxonomies[$taxname]);
		}
		
		return $count;
	}

}

