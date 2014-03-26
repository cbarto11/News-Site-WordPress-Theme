

<?php global $ns_config, $ns_mobile_support, $ns_template_vars, $wp_query; ?>
<?php $ns_section = $ns_template_vars['section']; ?>

<h1><?php echo $ns_template_vars['page-title']; ?></h1>

<?php if( isset($ns_template_vars['description']) ): ?>
	<div class="description"><?php echo $ns_template_vars['description']; ?></siv>
<?php endif; ?>

<?php
//------------------------------------------------------------------------------------
// Print of the stories for this archive listing.
//------------------------------------------------------------------------------------
if( !have_posts() ):

	?>
	<p>No events found.</p>
	<?php

else:

	$current_date = new DateTime;
	$current_date->sub(new DateInterval('P1D')); 
	$close_previous_day = false;
	
	while( have_posts() ):
	
		the_post();
		$story = $ns_section->get_listing_story( get_post() );
		
		$same_day = true;
		if( $story['datetime']->format('y-d-M') != $current_date->format('y-d-M') )
		{
			$same_day = false;
			$current_date = $story['datetime'];
			$month = $current_date->format('F');
			$day = $current_date->format('j');
			$weekday = $current_date->format('l');

			if( $close_previous_day )
			{
				?>
				</div>
				<?php
			}

			?>
			<div class="agenda-day">
				<div class="date-label">
					<div class="weekday"><?php echo $weekday; ?></div>
					<div class="month"><?php echo $month; ?></div>
					<div class="day"><?php echo $day; ?></div>
				</div>
			<?php
			
			$close_previous_day = true;
		}

		$ns_template_vars['section'] = $ns_section;
		$ns_template_vars['story'] = $story;
		ns_get_template_part( 'listing', 'story', $ns_section->key );

	endwhile;

	//--------------------------------------------------------------------------------
	// Page Navigation.
	//--------------------------------------------------------------------------------
	if( $wp_query->max_num_pages > 1 ):

		?>
		<div id="page-navigation" class="clearfix" role="navigation">
			<div class="nav-older">
				<?php next_posts_link( '&laquo; Older Events' ); ?>
			</div>
			<div class="nav-newer">
				<?php previous_posts_link( 'Newer Events &raquo;' ); ?>
			</div>
		</div>
		<?php

	endif; // if( $wp_query->max_num_pages > 1 )

endif; // if( !have_posts() )
?>

