

<?php global $ns_config, $ns_mobile_support, $ns_template_vars; ?>
<?php $ns_section = $ns_template_vars['section']; ?>

<h1><?php echo $ns_template_vars['page-title']; ?></h1>

<?php
//------------------------------------------------------------------------------------
// Print of the stories for this archive listing.
//------------------------------------------------------------------------------------
if( !have_posts() ):

	?>
	<p>No stories found.</p>
	<?php

else:

	$num_columns = $ns_config->get_num_columns($ns_section->thumbnail_image);
	if( $ns_mobile_support->use_mobile_site ) $num_columns = 1;

	$post_list = array();
	for( $i = 0; $i < $num_columns; $i++ )
		$post_list[] = array();
		
	$i = 0;
	while( have_posts() )
	{
		the_post();
		$post_list[$i % $num_columns][] = get_post();
		$i++;
	}

	$column_label = 'odd';

	foreach( $post_list as $column_posts ):

		?>
		<div class="column <?php echo $column_label; ?>">
		<?php

		foreach( $column_posts as $post ):
		
			//----------------------------------------------------------------------------
			// Get the story to be displayed, based on the section of the page.
			//----------------------------------------------------------------------------
			if( $ns_section->key == 'none' ):

				$type = get_post_type( $post->ID );
				$categories = get_the_category( $post->ID );
				$tags = get_the_tags( $post->ID );
				$story_section = $ns_config->get_section( $type, $category->slug, $tags );
				$story = $story_section->get_listing_story( $post );

			else:

				$story = $ns_section->get_listing_story( $post );

			endif; // if( $ns_section->key == 'none' )

			?>

			<?php
			$ns_template_vars['story'] = $story;
			ns_get_template_part( 'listing', 'story', $ns_section->key );
	
		endforeach; // foreach( $column_posts as $post )

		?>
		</div><!-- .column -->
		<?php

		if( $column_label == 'odd' ) 
			$column_label = 'even'; 
		else 
			$column_label = 'odd';

	endforeach; // foreach( $posts as $column_posts )
 
	//--------------------------------------------------------------------------------
	// Page Navigation.
	//--------------------------------------------------------------------------------
	if( $wp_query->max_num_pages > 1 ):

		?>
		<div id="page-navigation" class="clearfix" role="navigation">
			<div class="nav-older">
				<?php next_posts_link( '&laquo; Older posts' ); ?>
			</div>
			<div class="nav-newer">
				<?php previous_posts_link( 'Newer posts &raquo;' ); ?>
			</div>
		</div>
		<?php

	endif; // if( $wp_query->max_num_pages > 1 )

endif; // if( !have_posts() )
?>

