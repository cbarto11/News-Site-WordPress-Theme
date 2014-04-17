

<?php global $nh_config, $nh_mobile_support, $nh_template_vars; ?>


<div id="main-wrapper" class="clearfix">

	<div id="main" class="clearfix">
	<?php nh_use_widget( 'main', 'top' ); ?>


	<?php
	nh_get_template_part( 'content' );
	nh_get_template_part( 'sidebar' );
	?>

	
	<?php nh_use_widget( 'main', 'bottom' ); ?>
	</div><!-- #main -->

</div><!-- #main-wrapper -->

