<?php global $ns_config, $ns_mobile_support, $ns_template_vars, $wp_query; ?>
<?php
$options = $ns_config->get_admin_options( 'front-page' );
?>


<?php foreach( $options['sections'] as $column_name => $sections ): ?>

	<div class="column <?php echo 'front-page-'.$column_name; ?>">

	<?php
	
		foreach( $sections as $section_key ):
	
			$section = $ns_config->get_section_by_key( $section_key, true );
			if( $section == null ) continue;
		
			$stories = $section->get_stories( 'front-page' );
			
			?>
			<div class="section-box <?php $section_key; ?>-section <?php echo $section->thumbnail_image; ?>-image">

				<h2><?php echo $section->title; ?></h2>
		
				<?php
				foreach( $stories as $story ):
		
					$ns_template_vars['story'] = $story;
					$ns_template_vars['section'] = $section;
					ns_get_template_part( 'featured', 'story', $section->key );
		
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

		endforeach; // foreach( $current_column as $section_key )
	?>

	<?php ns_use_widget( 'content', 'bottom' ); ?>
	</div><!-- .column -->

<?php endforeach; // foreach( $options['sections'] as $column_name => $sections ) ?>

