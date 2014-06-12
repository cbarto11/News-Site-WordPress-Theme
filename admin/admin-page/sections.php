<?php

/**
 *
 */
class NH_AdminPage_Sections extends NH_AdminPage
{

	private static $_instance = null;
	private static $section = null;



	/* Default private constructor. */
	private function __construct( $slug )
	{
		$this->slug = $slug;
	}	
	
	
	/**
	 *
	 */	
	public static function get_instance( $slug )
	{
		if( self::$_instance === null )
		{
			self::$_instance = new NH_AdminPage_Sections( $slug );
		}
		
		return self::$_instance;
	}



	/**
	 * 
	 */
	public function enqueue_scripts()
	{
		wp_deregister_script('jquery');
		wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js');
	}
	
	
	
	/**
	 * 
	 */
	public function add_head_script()
	{
		?>
		<style>
		
		.nav-tab.active {
			color:#cbb563;
			background-color:#006633;
		}
		
		.taxonomy .term { margin-left:10px; }
		
		.key { font-weight:bold; }
		
		.name { margin-left:10px; }
		
		.post-types { display:block; }
		.post-types .type { display:inline-block; margin-left:10px; }
		.taxonomy { display:block; }
		.taxonomy .term { display:inline-block; margin-left:10px; }
		
		#no-taxonomies { display:none; }
				
		</style>
  		<script type="text/javascript">
  		
  			function getUnique(a)
  			{
				var b = [a[0]], i, j, tmp;
				
				for (i = 1; i < a.length; i++)
				{
					tmp = 1;
					for (j = 0; j < b.length; j++)
					{
						if (a[i] == b[j])
						{
							tmp = 0;
							break;
						}
					}
					if (tmp) { b.push(a[i]); }
				}
				
				return b;
			}
  		
			jQuery(document).ready( function()
			{
				
				jQuery('#post-type-selection').each( function()
				{
					var self = this;
					
					jQuery(self).find('.post-type input[type=checkbox]')
						.change( function()
						{
							// compile list of taxonomies
							var pt = jQuery(self).find('.post-type');
							var taxonomies = [];
														
							for( var i = 0; i < pt.length; i++ )
							{
								if( jQuery(pt[i]).find('input[type=checkbox]').is(":checked") )
								{
									var taxs = jQuery(pt[i]).find('input[type=hidden]').val();
									taxs = taxs.split(',');
									for( var j = 0; j < taxs.length; j++ )
									{
										if( taxs[j] !== '' ) taxonomies.push( taxs[j] );
									}
								}
							}
							
							jQuery('#taxonomy-selection input[type=checkbox]').change();
							if( taxonomies.length == 0 )
							{
								jQuery('#no-taxonomies').show();
								jQuery('#taxonomy-selection').find('.taxonomy')
									.each( function()
									{
										jQuery(this).hide();
										jQuery('#taxonomies-selection .taxonomy').hide();
									});
							}
							else
							{
								taxonomies = getUnique( taxonomies );
								jQuery('#no-taxonomies').hide();

								jQuery('#taxonomy-selection').find('.taxonomy')
									.each( function()
									{
										var taxname = jQuery(this).attr('class').substring(9);
										if( taxonomies.indexOf( taxname ) < 0 )
										{
											jQuery(this).hide();
											jQuery('#taxonomies-selection .taxonomy.'+taxname).hide();
										}
										else
										{
											jQuery(this).show();
										}
									});
							}
						})
						.change();
				});
				
				jQuery('#taxonomy-selection input[type=checkbox]')
					.change( function()
					{
						var taxname = this.value;
						if( this.checked )
						{
							jQuery('#taxonomies-selection .taxonomy.'+taxname).show();
						}
						else
						{
							jQuery('#taxonomies-selection .taxonomy.'+taxname).hide();
						}
					})
					.change();
			});
		</script>
		<?php
	}

	

	/**
	 *
	 */
	public function register_settings()
	{
		add_filter( $this->slug.'-process-input', array($this, 'process_input'), 99, 5 );
	}
	
	
	/**
	 *
	 */
	public function add_settings_sections()
	{
		add_settings_section(
			'edit', 'Edit Section', array( get_class(), 'print_edit_section' ),
			$this->slug.':edit'
		);

		add_settings_section(
			'delete', 'Delete', array( get_class(), 'print_delete_section' ),
			$this->slug.':delete'
		);
	}
	
	
	/**
	 *
	 */
	public function add_settings_fields()
	{
		//
		// Add / Edit Section
		//
		
		add_settings_field( 
			'key', 'Key', array( get_class(), 'print_section_key' ),
			$this->slug.':edit', 'edit', array(  )
		);

		add_settings_field( 
			'name', 'Name', array( get_class(), 'print_section_name' ),
			$this->slug.':edit', 'edit', array(  )
		);

		add_settings_field( 
			'title', 'Title', array( get_class(), 'print_section_title' ),
			$this->slug.':edit', 'edit', array(  )
		);

		add_settings_field( 
			'post-types', 'Post Types', array( get_class(), 'print_section_post_types' ),
			$this->slug.':edit', 'edit', array(  )
		);

		add_settings_field( 
			'taxonomies', 'Taxonomies', array( get_class(), 'print_section_taxonomies' ),
			$this->slug.':edit', 'edit', array(  )
		);

		add_settings_field( 
			'orientations', 'Orientations', array( get_class(), 'print_section_image_orientations' ),
			$this->slug.':edit', 'edit', array(  )
		);

		add_settings_field( 
			'layout', 'Layout', array( get_class(), 'print_section_layout' ),
			$this->slug.':edit', 'edit', array(  )
		);
		
	}
	
	
	/**
	 *
	 */
	public function process_input( $options, $page, $tab, $option, $input )
	{

// nh_print($page, 'page');
// nh_print($tab, 'tab');
// nh_print($option, 'name');
// nh_print($options, 'options');
// nh_print($input, 'input');
	
		if( $option !== 'nh-options' ) return $options;
		if( !isset($input['action']) ) return $options;
		if( !isset($input['section-key']) ) return $options;

		global $nh_config;
		$referer_action = '';
		
		switch( $input['action'] )
		{
			case 'add':
			case 'edit':
			
				if( !isset($input['section']) ) return $options;
				$section = $input['section'];
				
				if( empty($section['name']) )
				{
					add_settings_error( '', 'name', 'Name is required.' );
					set_transient( 'section', $section );
					return $options;
				}
				
				if( empty($section['title']) )
					$section['title'] = strtoupper( $section['name'] );
				
				if( empty($section['key']) )
					$section['key'] = sanitize_title( $section['name'], '' );
				else
					$section['key'] = sanitize_title( $section['key'], '' );
				if( empty($section['key']) )
				{
					add_settings_error( '', 'key', 'Error creating key.' );
					set_transient( 'section', $section );
					return $options;
				}
				
				$check_key = $section['key'];
				if( ($input['action'] === 'edit') && ($input['section-key'] === $section['key']) )
					$check_key = false;
				
				if( ($check_key !== false) && (null !== $nh_config->get_section_by_key($check_key, true)) )
				{
					add_settings_error( '', 'key', 'Key already in use.' );
					set_transient( 'section', $section );
					return $options;
				}
				
				$section_post_types = array( 'post' );
				if( isset($section['post-types']) ) $section_post_types = $section['post-types'];
				
				$valid_taxonomies = array();
				foreach( $section_post_types as $post_type )
				{
					$valid_taxonomies = array_merge( $valid_taxonomies, get_object_taxonomies($post_type) );
				}
				$valid_taxonomies = array_unique( $valid_taxonomies );
				
				$section_taxonomies = array();
				if( isset($section['taxonomies']) ) 
					$section_taxonomies = array_intersect( $section['taxonomies'], $valid_taxonomies );
				
				$all_taxonomies = array_keys( get_taxonomies(array(), 'objects') );
				$remove_taxonomies = array_diff( $section_taxonomies, $all_taxonomies );
				
				foreach( $remove_taxonomies as $taxonomy )
				{
					if( isset($section[$taxonomy]) ) unset($section[$taxonomy]);
				}
				$section['taxonomies'] = $section_taxonomies;
				
				$section['listing-num-stories'] = intval($section['num-stories']['listing']);
				$section['rss-feed-num-stories'] = intval($section['num-stories']['rss-feed']);
				unset( $section['num-stories'] );
				
				$old_section = array();
				if( ($input['action'] === 'edit') && (isset($options['sections'][$input['section-key']])) )
					$old_section = $options['sections'][$input['section-key']];

				$section = array_replace_recursive( $old_section, $section );

				if( ($check_key !== false) && ($input['action'] === 'edit') )
					unset( $options['sections'][$check_key] );
				
				$options['sections'][$section['key']] = $section;
// 				nh_print( $section, 'after' );
				
				$referer_action = 'edit';
				$referer_key = $section['key'];

				break;
			
			case 'delete':
				if( $input['submit'] === 'YES' )
				{
					if( isset($options['sections'][$input['section-key']]) )
					{
						unset( $options['sections'][$input['section-key']] );
						add_settings_error( '', '', 'Section "'.$input['section-key'].'" was deleted.', 'updated' );
					}
					else
					{
						add_settings_error( '', '', 'Section "'.$input['section-key'].'" not found.' );
					}
				}
				else
				{
					add_settings_error( '', '', 'Section "'.$input['section-key'].'" was NOT deleted.', 'updated' );
				}
				$referer_action = 'list';

				break;
		}

		// Change referer.
		$parts = parse_url( wp_get_referer() );
		parse_str( $parts['query'], $query );

		$new_query = '';
		foreach( $query as $name => $value )
		{
			switch( $name )
			{
				case 'action':
					$new_query = add_query_arg( 'action', $referer_action, $new_query );
					break;
				case 'key':
					$new_query = add_query_arg( 'key', $referer_key, $new_query );
					break;
				default:
					$new_query = add_query_arg( $name, $value, $new_query );
					break;
			}
		}
		if( $referer_action == 'edit' ) $new_query = add_query_arg( 'key', $referer_key, $new_query );
		$_REQUEST['_wp_http_referer'] = wp_slash( $parts['path'].$new_query );
		
		return $options;
	}



	/**
	 *
	 */
	public function show()
	{
		if( !isset($_GET['settings-updated']) )
		{
			delete_transient( 'section' );
		}
		
		switch( $_GET['action'] )
		{
			case 'add':
				$this->display_add_section();
				break;

			case 'edit':
				$this->display_edit_section();
				break;
			
			case 'delete':
				$this->display_delete_section();
				break;
			
			default:
				$this->display_list();
				break;
		}
	}
	
	

	private function display_list()
	{
		global $nh_config;
		global $nh_admin_pages;
		
		delete_transient( 'section' );

		require_once( dirname(__FILE__).'/sections-table.php' );
		$sections_table = new NewsHub_Sections_Table();
		$sections_table->prepare_items(); 
		$sections_table->slug = $this->slug;
		?>		

		<div class="wrap">
		
		<div id="icon-themes" class="icon32"></div>
		<h2><?php echo $nh_admin_pages[$this->slug]['title']; ?></h2>
		<?php settings_errors(); ?>

		<a href="?page=<?php echo $this->slug; ?>&action=add"><button class="button-primary synch-connections">Add Section</button></a>
		<?php $sections_table->display(); ?>
		
		</div>
		
		<?php
// 		nh_print($nh_config->get_sections());
	}
	
	
	
	private function display_add_section()
	{
// 		nh_print( 'display_add_section' );
		
		global $nh_config;
		
		if( get_transient( 'section' ) !== false )
		{
			$section = get_transient('section');
			$section = new NH_Section( $section['key'], $section );
		}
		else
		{
			$section = $nh_config->get_empty_section();
		}
			
// 		nh_print($section);
		$this->display_form( 'add', $section );
	}
	
	
	
	private function display_edit_section()
	{
// 		nh_print( 'display_edit_section' );
		
		$section = self::get_section();
		if( $section === null )
		{
			nh_print('error'); return;
		}
		$this->display_form( 'edit', $section );
	}
	
	
	private function display_form( $type, $section )
	{
		global $nh_config;
		self::$section = $section;
		
		?>		

		<div class="wrap">

		<?php //nh_print($type); ?>
		<?php //nh_print($section); ?>
		
		<div id="icon-themes" class="icon32"></div>
		<h2><?php echo ( $type == 'add' ? 'Add' : 'Edit' ); ?> Section</h2>
		<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php settings_fields( $this->slug ); ?>
				<input type="hidden" 
				       name="<?php nh_input_name_e( 'action' ); ?>" 
				       value="<?php echo $type; ?>" />
				<input type="hidden" 
				       name="<?php nh_input_name_e( 'section-key' ); ?>" 
				       value="<?php echo $section->key; ?>" />
				<?php do_settings_sections( $this->slug.':edit' ); ?>
				<?php submit_button(); ?>
			</form>
		
		</div>
		
		<?php
// 		nh_print($section);
// 		nh_print(array_keys($GLOBALS));
// 		nh_print($_SERVER);
// 		nh_print($_GET);
	}




	//
	// TODO: Change to non-static
	//

		

	
	public static function print_edit_section()
	{
		echo '<p>print_edit_section</p>';
	}
	
	public static function print_delete_section()
	{
		echo '<p>print_delete_section</p>';
	}
	
	public static function print_section_key( $args )
	{
		?>
		<input type="text" 
		       id="section-key" 
		       name="<?php nh_input_name_e( 'section', 'key' ); ?>" 
		       value="<?php echo self::$section->key; ?>" />
		<?php
	}

	public static function print_section_name( $args )
	{
		?>
		<input type="text" 
		       name="<?php nh_input_name_e( 'section', 'name' ); ?>" 
		       value="<?php echo self::$section->name; ?>" />
		<?php
	}

	public static function print_section_title( $args )
	{
		?>
		<input type="text" 
		       name="<?php nh_input_name_e( 'section', 'title' ); ?>" 
		       value="<?php echo self::$section->title; ?>" />
		<?php
	}

	public static function print_section_post_types( $args )
	{
		$all_post_types = get_post_types( array('public' => true), 'objects' );
		$section_post_types = self::$section->post_types;
		?>
		
		<div id="post-type-selection">
		
		<?php foreach( $all_post_types as $post_type ): ?>
		
			<div class="post-type <?php echo $post_type->name; ?>">
			<input type="checkbox" 
			       name="<?php nh_input_name_e( 'section', 'post-types', '' ); ?>"
			       value="<?php echo $post_type->name; ?>" 
			       <?php checked(true, in_array($post_type->name, $section_post_types, true)); ?> />
			<input type="hidden" 
			       value="<?php echo implode(',',get_object_taxonomies($post_type->name)); ?>" />
			<label><?php echo $post_type->label; ?></label>
			</div>
		
		<?php endforeach; ?>
		
		</div>
		
		<?php
	}
	
	public static function print_section_taxonomies( $args )
	{
		$all_taxonomies = get_taxonomies( array(), 'objects' );
		$section_taxonomies = self::$section->taxonomies;
		?>
		
		<div id="no-taxonomies">No taxonomies for selected post types.</div>
		
		<div id="taxonomy-selection">
		
		<?php foreach( $all_taxonomies as $taxname => $taxonomy ): ?>
		
			<?php
			$tax = get_taxonomy( $taxname ); $name = $tax->label;
			if( empty($name) ) $name = $taxname;
			?>

			<div class="taxonomy <?php echo $taxname; ?>">
			<input type="checkbox" 
			       name="<?php nh_input_name_e( 'section', 'taxonomies', '' ); ?>" 
			       value="<?php echo $taxname; ?>" 
			       <?php checked(true, array_key_exists($taxname, $section_taxonomies)); ?> />
			<label><?php echo $name; ?></label>
			</div>
					
		<?php endforeach; ?>
		
		</div>
		
		<div id="taxonomies-selection">
		
		<?php foreach( $all_taxonomies as $taxname => $taxonomy ): ?>
		
			<div class="taxonomy <?php echo $taxname; ?>">

			<?php
			$tax = get_taxonomy( $taxname ); $name = $tax->label;
			if( empty($name) ) $name = $taxname;
			?>
			
			<div class="taxname"><strong><?php echo $name; ?></strong></div>
			
			<?php 
			$terms = ( array_key_exists($taxname, $section_taxonomies) ? $section_taxonomies[$taxname] : array() );
			?>
			
			<?php foreach( get_terms( $taxname ) as $term ): ?>
		
			<div>
			<input type="checkbox" 
			       name="<?php nh_input_name_e( 'section', $taxname, '' ); ?>" 
			       value="<?php echo $term->slug; ?>" 
			       <?php checked(true, in_array($term->slug, $terms), true); ?> />
			<label><?php echo $term->name; ?></label>
			</div>
			
			<?php endforeach; ?>
			
			</div>
		
		<?php endforeach; ?>
		
		</div>
		
		<?php
	}
	
	public static function print_section_image_orientations( $args )
	{
		echo '<p>print_section_image_orientations</p>';
		?><strong>Featured</strong><?php
		self::print_image_selection( 'featured-image', self::$section->featured_image );

		?><br/><strong>Thumbnail</strong><?php
		self::print_image_selection( 'thumbnail-image', self::$section->thumbnail_image );
	}
	
	public static function print_image_selection( $name, $current_value )
	{
		?>
		<select name="<?php nh_input_name_e( 'section', $name ); ?>">

		<?php foreach( array( 'none', 'landscape', 'portrait', 'embed' ) as $image_type ): ?>
			<option value="<?php echo $image_type; ?>" 
			        <?php selected( $current_value, $image_type ); ?>>
			    <?php echo $image_type; ?>
			</option>
		<?php endforeach; ?>
		
		</select>
		<?php
	}
	
	public static function print_section_layout( $args )
	{
		?>
		
		<label>Number of stories on archive page</label>
		<select name="<?php nh_input_name_e( 'section', 'num-stories', 'listing' ); ?>">
			<?php for( $i = 9; $i < 100; $i += 10 ): ?>
				<?php $val = $i+1; ?>
				<option value="<?php echo $val; ?>" 
				        <?php selected( $val, self::$section->num_stories['listing'] ); ?>>
				    <?php echo $val; ?>
				</option>
			<?php endfor; ?>
		</select>

		<label>Number of stories on rss feed</label>
		<select name="<?php nh_input_name_e( 'section', 'num-stories', 'rss-feed' ); ?>">
			<?php for( $i = 9; $i < 100; $i += 10 ): ?>
				<?php $val = $i+1; ?>
				<option value="<?php echo $val; ?>" 
				        <?php selected( $val, self::$section->num_stories['rss-feed'] ); ?>>
					<?php echo $val; ?>
				</option>
			<?php endfor; ?>
		</select>
		
		<?php
	}
	


	
	
	
	private function display_delete_section()
	{
// 		nh_print( 'display_delete_section' );
		
		$section = self::get_section();
		if( $section === null )
		{
			nh_print('error'); return;
		}
		
		?>
		
		<div class="wrap">

		<?php //nh_print($type); ?>
		<?php //nh_print($section); ?>
		
		<div id="icon-themes" class="icon32"></div>
		<h2>Delete Section</h2>

			<h3><?php echo $section->key; ?> : <?php echo $section->name; ?></h3>
			<form method="post" action="options.php">
				<?php settings_fields( $this->slug ); ?>
				<input type="hidden" 
				       name="<?php nh_input_name_e( 'action' ); ?>" 
				       value="delete" />
				<input type="hidden" 
				       name="<?php nh_input_name_e( 'section-key' ); ?>" 
				       value="<?php echo $section->key; ?>" />
				<div>Are you sure you wish to delete this section?<br/>This CANNOT be undone.</div>
				<?php submit_button( 'YES', 'primary', 'nh-options[submit]', false ); ?>
				<?php submit_button( 'NO', 'primary', 'nh-options[submit]', false ); ?>
			</form>
		
		</div>
		
		<?php
	}
	
	
	
	private static function get_section()
	{
		global $nh_config;
		
		$section_key = ( isset($_GET['key']) ? $_GET['key'] : null );
		if( $section_key === null ) return null;
		
		return $nh_config->get_section_by_key( $section_key, true );
	}
	
	/**
	 * 
	 */
	public static function nh_sections()
	{
		echo '<p>nh_sections</p>';
	}
	
}


