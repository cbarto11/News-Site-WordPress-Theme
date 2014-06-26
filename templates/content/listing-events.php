

<?php global $nh_config, $nh_mobile_support, $nh_template_vars, $wp_query; ?>
<?php $nh_section = $nh_template_vars['section']; ?>

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
	<p>No events found.</p>
	<?php

else:

	$current_date = new DateTime;
	$current_date->sub( new DateInterval('P1D') ); 
	$close_previous_day = false;
	
	while( have_posts() ):
	
		the_post();
		$story = $nh_section->get_listing_story( get_post() );
		
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

		$nh_template_vars['section'] = $nh_section;
		$nh_template_vars['story'] = $story;
		nh_get_template_part( 'listing', 'story', $nh_section->key );

	endwhile;

endif; // if( !have_posts() )
?>

