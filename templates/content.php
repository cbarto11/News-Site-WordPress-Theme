

<?php global $nh_config, $nh_mobile_support, $nh_template_vars; ?>


<div id="content-wrapper" class="clearfix">

	<?php
	$section = $nh_template_vars['section'];
	$key = $section->key;
	$thumbnail = $section->thumbnail_image;
	$featured = $section->featured_image;

	$num_columns = 1;
	if( !$nh_mobile_support->use_mobile_site )
		$num_columns = $section->get_number_of_columns( $nh_template_vars['content-type'] );
	?>

	<div id="content" class="<?php echo $key ?>-section num-columns-<?php echo $num_columns; ?> <?php echo $thumbnail; ?>-thumbnail-image <?php echo $featured; ?>-featured-image clearfix">
	<?php nh_use_widget( 'content', 'top' ); ?>

	
	<?php
// 	nh_print( $nh_template_vars['content-type'].' : content : '.$key );
	nh_get_template_part( $nh_template_vars['content-type'], 'content', $key );
	?>

	
	<?php nh_use_widget( 'content', 'bottom' ); ?>
	</div><!-- #content -->

</div><!-- #content-wrapper -->

