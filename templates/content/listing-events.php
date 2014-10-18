
<?php global $nh_config, $nh_mobile_support, $nh_template_vars, $wp_query; ?>
<?php $nh_section = $nh_template_vars['section']; ?>

<h1><?php echo $nh_template_vars['page-title']; ?></h1>

<?php if( isset($nh_template_vars['description']) ): ?>
	<div class="description"><?php echo $nh_template_vars['description']; ?></div>
<?php endif; ?>


<?php

list( $month, $year, $start_datetime, $end_datetime ) = array_values( NH_CustomEventPostType::get_events_datetime() );
$end_datetime->sub( new DateInterval('PT1S') );

?>
<div class="date-range">
	<?php echo $start_datetime->format('F d, Y') . ' to ' . $end_datetime->format('F d, Y'); ?>
</div>
<?php

$events_url = get_site_url().'/event';
$prev_year = new DateTime( $start_datetime->format('Y-m-d') ); 
$prev_year->sub( new DateInterval('P1Y') );
$next_year = new DateTime( $start_datetime->format('Y-m-d') ); 
$next_year->add( new DateInterval('P1Y') );

?>
<div class="date-controls">
	<div class="year">
		<a href="<?php echo $events_url; ?>?event-date=12-<?php echo $prev_year->format("Y"); ?>">
			<
			<?php /* TOOD: replace with image. */ ?>
		</a>
		<?php echo $start_datetime->format('Y'); ?>
		<a href="<?php echo $events_url; ?>?event-date=12-<?php echo $next_year->format("Y"); ?>">
			>
			<?php /* TOOD: replace with image. */ ?>
		</a>
	</div>
	<div class="months">
		<?php
		$months = array(
			1 => 'JAN',
			2 => 'FEB',
			3 => 'MAR',
			4 => 'APR',
			5 => 'MAY',
			6 => 'JUN',
			7 => 'JUL',
			8 => 'AUG',
			9 => 'SEP',
			10 => 'OCT',
			11 => 'NOV',
			12 => 'DEC',
		);
		?>
		<?php foreach( $months as $m => $name ): ?>
			<?php if( $month == $m ): ?>
			<span>
				<?php echo $name; ?>
			</span>
			<?php else: ?>
			<a href="<?php echo $events_url; ?>?event-date=<?php echo sprintf("%02s", $m); ?>-<?php echo $start_datetime->format('Y'); ?>">
				<?php echo $name; ?>
			</a>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>


<?php
//------------------------------------------------------------------------------------
// Print of the stories for this archive listing.
//------------------------------------------------------------------------------------
if( !have_posts() ):

	?>
	<p>No events found.</p>
	<?php

else:

	$current_date = new DateTime( $start_datetime->format('Y-m-d') );
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

