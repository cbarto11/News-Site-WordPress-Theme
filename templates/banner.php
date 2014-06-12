

<?php global $nh_config, $nh_mobile_support, $nh_template_vars; ?>
<?php if( (is_front_page()) && ($nh_config->show_template_part('banner')) ): ?>


<div id="banner-wrapper" class="clearfix">

	<div id="banner" class="clearfix">
	<?php nh_use_widget( 'banner', 'top' ); ?>
	
	<script type="text/javascript">
	jQuery(document).ready(function()
	{
		jQuery('#featured-slider').nivoSlider({
			effect: 'fade',
			animSpeed: 500,
			pauseTime: 8000,
			directionNav: true,
			controlNav: true,
			controlNavThumbs: false
		});
	});
	</script>

	<?php $banner_images = $nh_config->get_banner_images(); ?>
	
	<div id="featured-slider-wrapper">
			
		<div id="featured-slider">
			<?php foreach( $banner_images as $slide ): ?>
				<?php echo nh_get_anchor( 
					$slide['url'], 
					htmlentities($slide['alt']), 
					null, 
					'<img src="'.$slide['src'].'" alt="'.htmlentities($slide['alt']).'" />' ); ?>
			<?php endforeach; ?>
		</div>
	</div>
	
	<?php nh_use_widget( 'banner', 'bottom' ); ?>
	</div><!-- #banner -->

</div><!-- #banner-wrapper -->


<?php endif; ?>

