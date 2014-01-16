

<?php global $ns_config, $ns_mobile_support, $ns_template_vars; ?>


<?php
$num_columns = $ns_config->get_num_columns('front-page');

//ns_print('front-page');
//ns_print($num_columns);
?>


<?php for( $i = 0; $i < $num_columns; $i++ ): ?>

	<div class="column <?php echo 'front-page-'.($i+1); ?>">

	<?php $current_column = $ns_config->get_column('content', 'front-page-column-'.($i+1)); ?>

	<?php if( $current_column !== null ): ?>
	
		<?php
			
			foreach( $current_column as $section_key ):
			
				$section = $ns_config->get_section_by_key( $section_key );
				$stories = $section->get_stories('front-page');

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
	
	<?php endif; // if( count($columns) > $i ) ?>
	
	</div><!-- .column -->

<?php endfor; ?>

