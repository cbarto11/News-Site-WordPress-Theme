<?php

add_action('widgets_init',
     create_function('', 'return register_widget("NH_SectionsWidget");')
);

class NH_SectionsWidget extends WP_Widget
{

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct()
	{
		// widget actual processes
		
		//nh_print('construct');
		
		parent::__construct(
			'nh_sectionh_widget', // Base ID
			__("Section List", 'text_domain'), // Name
			array( 
				'description' => __( 'Displays a list of news sections.', 'text_domain' ), 
			) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance )
	{
		global $nh_config;
		
		echo $args['before_widget'];

		if( !empty($instance['title']) ):
			echo $args['before_title'].$instance['title'].$args['after_title'];
		endif;

		$exclude_list = array();
		if( !empty($instance['exclude-list']) ):
			$exclude_list = array_map( 'trim', explode( ',', $instance['exclude-list'] ) );
		endif;

		$sections = $nh_config->get_value('sections');
		usort($sections, function($a, $b)
		{
			return strcmp($a->name, $b->name);
		});
		
		//nh_print($sections);
		
		?><ul><?php
		foreach( $sections as $s ):

			if( !in_array($s->key, $exclude_list) ):
			?>
			<li><a href="<?php echo $s->get_section_link(); ?>"><?php echo $s->name; ?></a></li>
			<?php
			endif;
		
		endforeach;
		?></ul><?php

		echo $args['after_widget'];
	}

	/**
	 * Ouputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance )
	{
		// outputs the options form on admin

		//nh_print('options of the widget');
		
		if( isset($instance['title']) )
			$title = $instance['title'];
			
		if( isset($instance['exclude-list']) )
			$exclude_list = $instance['exclude-list'];
		?>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'exclude-list' ); ?>"><?php _e( 'Exclude List:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'exclude-list' ); ?>" name="<?php echo $this->get_field_name( 'exclude-list' ); ?>" type="text" value="<?php echo esc_attr( $exclude_list ); ?>">
		</p>
		
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance )
	{
		// processes widget options to be saved
		
		//nh_print($new_instance);
		//nh_print($old_instance);
		
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['exclude-list'] = ( ! empty( $new_instance['exclude-list'] ) ) ? strip_tags( $new_instance['exclude-list'] ) : '';

		return $instance;		
	}

}


