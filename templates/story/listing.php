

<?php global $nh_config, $nh_mobile_support, $nh_template_vars; ?>
<?php
$section = $nh_template_vars['section'];
$story = $nh_template_vars['story'];
?>

<div class="story <?php echo $section->key; ?>-section <?php echo $section->thumbnail_image; ?>-image clearfix">
<?php echo nh_get_anchor( $story['link'], $story['title'] ); ?>

	<?php if( $section->thumbnail_image !== 'none' ): ?>
	
		<div class="image">
		
			<?php if( $story['image'] ): ?>
				<img src="<?php echo $story['image']; ?>" alt="Featured Image" />
			<?php endif; ?>
			
			<?php if( $story['embed'] ): ?>
				<?php echo $story['embed']; ?>
			<?php endif; ?>

		</div><!-- .image -->
	
	<?php endif; ?>

	<div class="details">
	
		<h3><?php echo $story['title']; ?></h3>
		
		<?php if( count($story['description']) > 0 ): ?>

			<div class="description">

			<?php 
			foreach( $story['description'] as $key => $value ):
				if( is_array($value) ):
					
					?><div class="<?php echo $key; ?>"><?php
					
					foreach( $value as $k => $v ):
						?><div class="<?php echo $k; ?>"><?php echo $v; ?></div><?php
					endforeach;
				
					?></div><?php
					
				else:

					?><div class="<?php echo $key; ?>"><?php echo $value; ?></div><?php
					
				endif;
			endforeach;
			?>
	
			</div><!-- .contents -->

		<?php endif; ?>
	
	</div><!-- .description -->
	
</a>
</div><!-- .story -->

