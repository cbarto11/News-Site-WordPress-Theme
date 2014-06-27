

<?php global $nh_config, $nh_mobile_support, $nh_template_vars; ?>
<?php if( $nh_config->show_template_part( 'subheader' ) ): ?>


<div id="subheader-wrapper" class="clearfix">

	<div id="subheader" class="clearfix">
	<?php nh_use_widget( 'subheader', 'top' ); ?>
	
	
	<?php
	$image = $nh_config->get_image_data( 'subheader', 'image' );
	$image['link'] = get_home_url();
	nh_image( $image );
	?>
	

	<?php nh_use_widget( 'subheader', 'bottom' ); ?>
	</div><!-- #subheader -->

</div><!-- #subheader-wrapper -->


<?php endif; ?>

