

<?php global $ns_config, $ns_mobile_support, $ns_template_vars; ?>


<div id="content-wrapper" class="clearfix">

	<?php
	$section = $ns_template_vars['section'];
	$key = $section->key;
	$thumbnail = $section->thumbnail_image;
	$featured = $section->featured_image;
	?>

	<div id="content" class="<?php echo $key ?>-section <?php echo $thumbnail; ?>-thumbnail-image <?php echo $featured; ?>-featured-image clearfix">
	<?php ns_use_widget( 'content', 'top' ); ?>

	
	<?php
	ns_get_template_part( $ns_template_vars['content-type'], 'content', $key );
	?>

	
	<?php ns_use_widget( 'content', 'bottom' ); ?>
	</div><!-- #content -->

</div><!-- #content-wrapper -->

