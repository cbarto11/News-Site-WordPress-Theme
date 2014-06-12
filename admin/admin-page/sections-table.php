<?php

if( !class_exists('WP_List_Table') )
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );


/**
 * 
 */
class NewsHub_Sections_Table extends WP_List_Table
{

	public $slug;
	
	
	/**
	 * 
	 */
	function prepare_items()
	{
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->get_items();
		usort( $this->items, array( &$this, 'sort_data' ) );
	}


	/**
	 * 
	 */
	function get_items()
	{
		global $nh_config;
		$this->items = $nh_config->get_sections();
	}
	
	
	/**
	 * 
	 */
	function get_columns()
	{
		return array(
			'name'   => 'Name',
			'filters' => 'Filters',
		);
	}

	
	/**
	 * 
	 */
	function get_hidden_columns()
	{
		return array();
	}

	
	/**
	 * 
	 */
	function get_sortable_columns()
	{
		return array(
			'name'  => array( 'name', false ),
		);
	}
	

	/**
	 * 
	 */
	function sort_data( $a, $b )
	{
		$orderby = ( !empty($_GET['orderby']) ? $_GET['orderby'] : 'name' );
		$order = ( !empty($_GET['order']) ? $_GET['order'] : 'asc' );

		switch( $orderby )
		{
			case 'name':
			default:
				$result = strcmp( $a->key, $b->key );
				break;
		}
		
		return ( $order === 'asc' ) ? $result : -$result;
	}


	/**
	 * 
	 */
	function column_default( $item, $column_name )
	{
		return '<strong>ERROR:</strong><br/>'.$column_name;
	}
	

	/**
	 * 
	 */
	function column_name( $item )
	{
		$actions = array(
            'edit' => sprintf( '<a href="%s">Edit</a>', '?page='.$this->slug.'&action=edit&key='.$item->key ),
            'delete' => sprintf( '<a href="%s">Delete</a>', '?page='.$this->slug.'&action=delete&key='.$item->key ),
        );

		return sprintf( '<div class="key">%1$s</div> <div class="name">%2$s</div> %3$s', $item->key, $item->name, $this->row_actions($actions) );
	}
	

	/**
	 * 
	 */
	function column_filters( $item )
	{
		// build up post types
		$text = '<div class="post-types"><strong>Post Types: </strong>';
		
		foreach( $item->post_types as $type )
		{
			$text .= '<div class="type">' . $type . '</div>';
		}
		
		$text .= '</div>';
		
		foreach( $item->taxonomies as $taxname => $terms )
		{
			$tax = get_taxonomy( $taxname ); $name = $tax->label;
			if( empty($name) ) $name = $taxname;
			
			$text .= '<div class="taxonomy"><strong>'.$name.': </strong>';
			foreach( $terms as $term )
			{
				$text .= '<div class="term">' . $term . '</div>';
			}
			$text .= '</div>';
		}

		
		return $text;
	}
	
}

