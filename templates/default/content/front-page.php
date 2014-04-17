<?php global $nh_config, $nh_mobile_support, $nh_template_vars, $wp_query; ?>
<?php
$options = $nh_config->get_admin_options( 'front-page' );
?>


<?php foreach( $options['sections'] as $column_name => $sections ): ?>

	<div class="column <?php echo 'front-page-'.$column_name; ?>">

	<?php
	
		foreach( $sections as $section_key ):
	
			$section = $nh_config->get_section_by_key( $section_key, true );
			if( $section == null ) continue;
		
			$stories = $section->get_stories( 'front-page' );
			
			?>
			<div class="section-box <?php $section_key; ?>-section <?php echo $section->thumbnail_image; ?>-image">

				<h2>
				<?php echo nh_get_anchor( 
						$section->get_section_link(), 
						$section->name.' Archives', 
						null,
						$section->title ); ?>
				</h2>
				
				<?php
				foreach( $stories as $story ):
		
					$nh_template_vars['story'] = $story;
					$nh_template_vars['section'] = $section;
					nh_get_template_part( 'featured', 'story', $section->key );
		
				endforeach;
				?>
		
				<div class="more">
					<?php echo nh_get_anchor( 
						$section->get_section_link(), 
						$section->name.' Archives', 
						null,
						'More <em>'.$section->name.'</em> &raquo;' ); ?>
				</div><!-- .more -->

			</div><!-- .section-box -->
			<?php

		endforeach; // foreach( $current_column as $section_key )
	?>

	<?php nh_use_widget( 'content', 'bottom' ); ?>
	</div><!-- .column -->

<?php endforeach; // foreach( $options['sections'] as $column_name => $sections ) ?>

