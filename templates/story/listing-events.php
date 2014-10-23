

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

	<div class="description clearfix">
	
		<h3><?php echo $story['title']; ?></h3>
		
		<?php if( count($story['description']) > 0 ): ?>

			<div class="contents">
			
			<?php
			$excerpt = '<div class="excerpt">'.$story['description']['excerpt'].'</div>';
			
			$event_info = '<div class="event-info">';
			foreach( $story['description']['event-info'] as $key => $value ):
				$event_info .= '<div class="'.$key.'">'.$value.'</div>';
			endforeach;
			$event_info .= '</div>';

			if( $nh_mobile_support->use_mobile_site ):
				echo $event_info;
				echo $excerpt;
			else:
				echo $excerpt;
				echo $event_info;
			endif;
			?>

			</div><!-- .contents -->
			
		<?php endif; ?>
	
	</div><!-- .description -->
	
</a>
</div><!-- .story -->

