

<?php global $nh_config, $nh_mobile_support, $nh_template_vars; ?>


<div id="content-wrapper" class="clearfix">

	<?php
	$section = $nh_template_vars['section'];
	$key = $section->key;
	$thumbnail = $section->thumbnail_image;
	$featured = $section->featured_image;
	
	if( $section->key !== 'none' )
		$num_cols = $nh_config->get_number_of_columns( $nh_template_vars['content-type'], $section );
	else
		$num_cols = $nh_config->get_number_of_columns( $nh_template_vars['content-type'] );
	$nh_template_vars['num-cols'] = $num_cols;
	?>

	<div id="content" class="<?php echo $key ?>-section num-columnh-<?php echo $num_cols; ?> <?php echo $thumbnail; ?>-thumbnail-image <?php echo $featured; ?>-featured-image clearfix">
	<?php nh_use_widget( 'content', 'top' ); ?>

	
	<?php
	nh_get_template_part( $nh_template_vars['content-type'], 'content', $key );
	?>

	
	<?php nh_use_widget( 'content', 'bottom' ); ?>
	</div><!-- #content -->

</div><!-- #content-wrapper -->

