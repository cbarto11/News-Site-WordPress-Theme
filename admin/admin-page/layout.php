<?php

/**
 *
 */
class NH_AdminPage_Layout extends NH_AdminPage
{

	private static $_instance = null;
	
	
	public $slug = null;
	public $tabs = array();
	public $tag = null;
	

	/* Default private constructor. */
	private function __construct( $slug )
	{
		$this->slug = $slug;
		
		$this->tabs = array(
			'columns' => 'Columns',
			'front-page' => 'Front Page',
			'sidebar' => 'Sidebar',
		);
		$this->tabs = apply_filters( $this->slug.'-tabs', $this->tabs );
		
        $this->tab = ( !empty($_GET['tab']) && array_key_exists($_GET['tab'], $this->tabs) ? $_GET['tab'] : apply_filters( $this->slug.'-default-tab', 'columns' ) );		
	}
	
	
	
	/**
	 *
	 */	
	public static function get_instance( $slug )
	{
		if( self::$_instance === null )
		{
			self::$_instance = new NH_AdminPage_Layout( $slug );
		}
		
		return self::$_instance;
	}



	
	/**
	 * 
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_style( 'google-jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css' );
		wp_enqueue_script( 'google-jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js' );
		wp_enqueue_media();
	}
	
	
	
	/**
	 * 
	 */
	public function add_head_script()
	{
		?>
		<style>
		
		.nav-tab.active {
			color:#000;
			background-color:#fff;
		}
		
		.column {
			padding:6px;
			background-color:#eee;
			border:solid 1px #ccc;
			width:25%;
			float:left;
			margin-right:10px;
		}
		
		.column-title {
			text-align:center;
			border-bottom:solid 1px #ccc;
		}
		
		.section {
			padding:3px;
			background-color:#fff;
			border:solid 1px #000;
			margin:3px;
			font-size:10px;
			height:40px;
		}
		
		.section .key {
			font-weight:bold;
			white-space:nowrap;
			text-overflow:ellipsis;
			overflow:hidden;
			background-color:#f6f6f6;
			border-bottom:solid 1px #bbb;
			color:#006633;
			cursor:move;
		}
		
		.section select {
			font-size:10px;
			line-height:1.4em;
			height:1.4em;
			width:100%;
			margin-top:2px;
		}
		
		.section-placeholder {
			padding:3px;
			background-color:#eee;
			border:solid 1px #ccc;
			margin:3px;
			height:40px;
		}
		
		.column-list {
			padding:10px 0px;
		}

		.column-sections {
			margin-right:15%;
		}
		
		.column-sections .section select {
			display:none;
		}
		
		.column-sections .section {
			height:auto !important;
		}
				
		
		</style>
  		<script type="text/javascript">
			jQuery(document).ready( function()
			{
			
				jQuery( '.column-selection' ).change(
					function()
					{
						var num_columns = parseInt( this.value );
						for( var i = 1; i <= num_columns; i++ )
						{
							jQuery('.column-'+i).show();
						}
						for( var i = num_columns+1; i <= 2; i++ )
						{
							var column = jQuery('.column-'+i);
							if( !column ) break;
							
							jQuery(column).hide();
							jQuery('.column-sections .column-list')
								.append( jQuery(column).find('.column-list').html() );
							jQuery(column).find('.column-list').html('');
						}
					})
					.change();
				
				jQuery( ".column-list" ).sortable({
					connectWith: ".column-list",
					handle: ".key",
					placeholder: "section-placeholder"
				});
				
				jQuery( ".section" ).each( function()
				{
				
					var self = this;
					
					jQuery(self).find(".num-stories").change( function()
					{
						var num_stories = parseInt( jQuery(this).val() );
						var height = num_stories * 40;
						jQuery(self).css( 'height', height+'px' );
					})
					.change();
				
				});
				
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
			'columns', 'Columns', array( $this, 'print_columns_section' ),
			$this->slug.':columns'
		);

		add_settings_section(
			'front-page', 'Front Page', array( $this, 'print_front_page_section' ),
			$this->slug.':front-page'
		);

		add_settings_section(
			'sidebar', 'Sidebar', array( $this, 'print_sidebar_section' ),
			$this->slug.':sidebar'
		);
	}
	
	
	/**
	 *
	 */
	public function add_settings_fields()
	{
		//
		// Columns
		//
		
		add_settings_field( 
			'front-page', 'Front Page', array( $this, 'print_columns_selection' ),
			$this->slug.':columns', 'columns', array( 'front-page' )
		);

		add_settings_field( 
			'landscape', 'Landscape', array( $this, 'print_columns_selection' ),
			$this->slug.':columns', 'columns', array( 'landscape' )
		);

		add_settings_field( 
			'portrait', 'Portrait', array( $this, 'print_columns_selection' ),
			$this->slug.':columns', 'columns', array( 'portrait' )
		);

		add_settings_field( 
			'embed', 'Embed', array( $this, 'print_columns_selection' ),
			$this->slug.':columns', 'columns', array( 'embed' )
		);

		add_settings_field( 
			'none', 'None', array( $this, 'print_columns_selection' ),
			$this->slug.':columns', 'columns', array( 'none' )
		);
		
		add_settings_field( 
			'author', 'Author', array( $this, 'print_columns_selection' ),
			$this->slug.':columns', 'columns', array( 'author' )
		);
		
		//
		// Front Page
		//
		
		add_settings_field( 
			'layout', 'Layout', array( $this, 'print_layout' ),
			$this->slug.':front-page', 'front-page', array( 'front-page' )
		);
		
		//
		// Sidebar
		//
		
		add_settings_field( 
			'layout', 'Layout', array( $this, 'print_layout' ),
			$this->slug.':sidebar', 'sidebar', array( 'sidebar' )
		);
		
	}
	
	
	/**
	 *
	 */
	public function process_input( $options, $page, $tab, $option, $input )
	{
		if( $option !== 'nh-options' ) return $options;
		
// 		nh_print($page);
// 		nh_print($tab);
// 		nh_print($option);
// 		nh_print($input);
// 		nh_print($options);

		
		global $nh_config;
		
		if( !array_key_exists($tab, $input) ) return $options;
		$tab_input = $input[$tab];		
// 		nh_print($tab_input);
	
		switch( $tab )
		{
			case 'columns':
				if( isset($tab_input['num-columns']) ):
				
				if( empty($options['content']['num-columns']) )
					$options['content']['num-columns'] = $tab_input['num-columns'];
				else
					$options['content']['num-columns'] = array_merge( 
						$options['content']['num-columns'], $tab_input['num-columns'] );
				
				$options['content']['num-columns'] = array_map( 'intval', $options['content']['num-columns'] );
				endif;
				break;
			
			case 'front-page':
			case 'sidebar':
				if( isset($tab_input['layout']) ):
				
				$current_layout = array();
				$current_column_index = 0;
				$current_column_sections = array();
				foreach( $tab_input['layout']['key'] as $key )
				{
					if( $key === 'nh-column-'.($current_column_index+1) )
					{
						if( $current_column_index > 0 )
							$current_layout['column-'.$current_column_index] = $current_column_sections;
						$current_column_index++;
						$current_column_sections = array();
						continue;
					}
					
					array_push( $current_column_sections, $key );
				}
				$current_layout['column-'.$current_column_index] = $current_column_sections;
				$options[$tab.'-sections'] = $current_layout;
				
				foreach( $tab_input['layout']['column'] as $key => $columns )
				{
					if( isset($options['sections'][$key]) )
						$options['sections'][$key][$tab.'-num-stories'] = intval($columns);
				}

				endif;
				
				break;
				
			default: break;
		}		
		
		return $options;
	}
	

	/**
	 *
	 */
	public function show()
	{
		global $nh_admin_pages;
		?>
		
		<div class="wrap">
	 
			<div id="icon-themes" class="icon32"></div>
			<h2><?php echo $nh_admin_pages[$this->slug]['title']; ?></h2>
			<?php settings_errors(); ?>
		 
			<h2 class="nav-tab-wrapper">
				<?php foreach( $this->tabs as $k => $t ): ?>
					<a href="?page=<?php echo $this->slug; ?>&tab=<?php echo $k; ?>" class="nav-tab <?php if($k==$this->tab) echo 'active'; ?>"><?php echo $t; ?></a>
				<?php endforeach; ?>
			</h2>
		
			<form method="post" action="options.php">
				<?php submit_button(); ?>
				<?php settings_fields( $this->slug ); ?>
				<input type="hidden" name="tab" value="<?php echo $this->tab; ?>" />
				<?php do_settings_sections( $this->slug.':'.$this->tab ); ?>
				<?php submit_button(); ?>
			</form>
		 
		</div><!-- /.wrap -->
		
		<?php
	}
	



	public function print_columns_section()
	{
		echo '<p>print_columns_section</p>';
	}
	
	public function print_front_page_section()
	{
		echo '<p>print_front_page_section</p>';
	}
	
	public function print_sidebar_section()
	{
		echo '<p>print_sidebar_section</p>';
	}



	public function print_columns_selection( $args )
	{
		global $nh_config;
		$num_columns = $nh_config->get_number_of_columns( $args[0] );
		$nc = array( 1, 2 );
		?>
		
		<select class="column-selection" 
		        name="<?php nh_input_name_e( $this->tab, 'num-columns', $args[0] ); ?>">

			<?php for( $i = 1; $i <= 2; $i++ ): ?>
				<option value="<?php echo $i; ?>" 
						<?php selected( $i, $num_columns ); ?>>
					<?php echo $i; ?>
				</option>
			<?php endfor; ?>

		</select>
		
		<?php		
	}

	public function print_columns( $args )
	{
		global $nh_config;
		$num_columns = $nh_config->get_number_of_columns( $args[0] );
		$nc = array( 1, 2 );
		?>
		
		<select class="column-selection" 
		        name="<?php nh_input_name_e( $this->tab, 'num-columns', $args[0] ); ?>">

			<?php foreach( $nc as $n ): ?>
				<option value="<?php echo $n; ?>" 
						<?php selected( $n, $num_columns ); ?>>
					<?php echo $n; ?>
				</option>
			<?php endforeach; ?>

		</select>
		
		<?php		
	}
	
	public function print_layout( $args )
	{
		global $nh_config;
		$num_columns = $nh_config->get_number_of_columns( $args[0] );
		$layout = $nh_config->get_value( $args[0].'-sections' );
		$sections = $nh_config->get_sections();
		
		$used_section_keys = array();
		foreach( $layout as $column => $keys )
		{
			foreach( $keys as $key )
			{
				array_push( $used_section_keys, $key );
			}
		}
		?>
		
		<div class="column column-sections">
		
			<div class="column-title">Sections</div>
		
			<div class="column-list">
		
			<?php foreach( $sections as $section ): ?>
				
				<?php if( in_array($section->key, $used_section_keys) ) continue; ?>
				
				<div class="section">
					<input type="hidden" 
					       name="<?php nh_input_name_e( $this->tab, 'layout', 'key', '' ); ?>" 
					       value="<?php echo $section->key; ?>" />
					<div class="key"><?php echo $section->name; ?></div>
					
					<select class="num-stories" 
					        name="<?php nh_input_name_e( $this->tab, 'layout', 'column', $section->key ); ?>">
						<?php for( $j = 0; $j < 10; $j++ ): ?>
							<option value="<?php echo ($j+1); ?>" 
							        <?php selected( $j+1, $section->num_stories[$args[0]] ); ?>>
							    <?php echo ($j+1); ?>
							</option>
						<?php endfor; ?>
					</select>
				</div>
			<?php endforeach; ?>
		
			</div>
		
		</div>
		
		<?php for( $i = 0; $i < $num_columns; $i++ ): ?>
			
			<div class="column column-<?php echo ($i+1); ?>">
			
				<div class="column-title"><?php echo ($i+1); ?></div>
				<input type="hidden" 
				       name="<?php nh_input_name_e( $this->tab, 'layout', 'key', '' ); ?>" 
				       value="nh-column-<?php echo ($i+1); ?>" />
		
				<div class="column-list">
				
					<?php if( isset($layout['column-'.($i+1)]) ): ?>
					<?php foreach( $layout['column-'.($i+1)] as $key ): ?>
					
						<?php $section = $nh_config->get_section_by_key($key, true); ?>
						<?php if( !$section ) continue; ?>

						<div class="section">
							<input type="hidden" 
							       name="<?php nh_input_name_e( $this->tab, 'layout', 'key', '' ); ?>" 
							       value="<?php echo $section->key; ?>" />
							<div class="key"><?php echo $section->name; ?></div>

							<select class="num-stories" 
							        name="<?php nh_input_name_e( $this->tab, 'layout', 'column', $section->key ); ?>">
								<?php for( $j = 0; $j < 10; $j++ ): ?>
									<option value="<?php echo ($j+1); ?>" 
									        <?php selected( $j+1, $section->num_stories[$args[0]] ); ?>>
									    <?php echo ($j+1); ?>
									</option>
								<?php endfor; ?>
							</select>
						</div>
					
					<?php endforeach; ?>
					<?php endif; ?>
				
				</div>
		
			</div>
			
		<?php endfor; ?>
		
		<?php
	}
		
}


