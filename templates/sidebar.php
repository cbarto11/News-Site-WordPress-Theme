

<?php global $ns_config, $ns_mobile_support, $ns_template_vars; ?>


<?php
$column = $ns_config->get_value('sidebar', 'sidebar-column-1');
?>


<div id="sidebar-wrapper" class="clearfix">

	<div id="sidebar" class="clearfix">
	<?php ns_use_widget( 'sidebar', 'top' ); ?>

		<div class="column column-sidebar">

		<?php
		foreach( $column as $section_key ):
	
			$section = $ns_config->get_section_by_key( $section_key );
			$stories = $section->get_stories('front-page');

			?>

			<div class="section-box <?php $section_key; ?>-section <?php echo $section->thumbnail_image; ?>-image">

				<h2><?php echo $section->title; ?></h2>
			
				<?php
				global $ns_story;
				foreach( $stories as $story ):
			
					$ns_story = $story;
					ns_get_template_part( 'featured', 'story', $ns_section->key );
			
				endforeach;
				?>
			
				<div class="more">
					<?php echo ns_get_anchor( 
						$section->get_section_link(), 
						$section->name.' Archives', 
						null,
						'More <em>'.$section->name.'</em> &raquo;' ); ?>
				</div><!-- .more -->

			</div><!-- .section-box -->

			<?php

		endforeach; // foreach( $column as $section_key )
		?>
		
		</div><!-- .column -->

	<?php ns_use_widget( 'sidebar', 'bottom' ); ?>
	</div><!-- #sidebar -->
	
</div><!-- #sidebar-wrapper -->

