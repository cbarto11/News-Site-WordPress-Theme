
<?php //nh_print('listing.php'); ?>
<?php global $nh_config, $nh_mobile_support, $nh_template_vars, $wp_query; ?>
<?php $nh_section = $nh_template_vars['section']; ?>

<?php if( isset($nh_template_vars['listing-name']) ): ?>
	<div class="listing-name"><?php echo $nh_template_vars['listing-name']; ?></div>
<?php endif; ?>

<h1><?php echo $nh_template_vars['page-title']; ?></h1>

<?php if( isset($nh_template_vars['description']) ): ?>
	<div class="description"><?php echo $nh_template_vars['description']; ?></div>
<?php endif; ?>

<?php
//------------------------------------------------------------------------------------
// Print of the stories for this archive listing.
//------------------------------------------------------------------------------------
if( !have_posts() ):

	?>
	<p>No stories found.</p>
	<?php

else:

	if( $nh_mobile_support->use_mobile_site )
		$num_columns = 1;
	else
		$num_columns = $nh_section->get_number_of_columns('listing');
	
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
			if( ($nh_section->key == 'none') || ($nh_section->thumbnail_image == 'multi') ):

				$type = get_post_type( $post->ID );

				$taxonomies = nh_get_taxonomies( $post->ID );
				$story_section = $nh_config->get_section( $type, $taxonomies, false, array( 'news' ) );
				
				$story = $story_section->get_listing_story( $post );
				$nh_template_vars['section'] = $story_section;
				$key = $nh_template_vars['section']->key;
				
			else:

				$story = $nh_section->get_listing_story( $post );
				$key = $nh_section->key;

			endif; // if( $nh_section->key == 'none' )

			?>

			<?php
			
			//nh_print( 'listing : story : '.$key );
			$nh_template_vars['story'] = $story;
			nh_get_template_part( 'listing', 'story', $key );
	
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
			<div class="nav-next">
				<?php next_posts_link( $nh_section->get_listing_label('next') ); ?>
			</div>
			<div class="nav-prev">
				<?php previous_posts_link( $nh_section->get_listing_label('prev') ); ?>
			</div>
		</div>
		<?php

	endif; // if( $wp_query->max_num_pages > 1 )

endif; // if( !have_posts() )
?>

