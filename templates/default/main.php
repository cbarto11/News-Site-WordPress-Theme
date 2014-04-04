

<?php global $ns_config, $ns_mobile_support, $ns_template_vars; ?>


<div id="main-wrapper" class="clearfix">

	<div id="main" class="clearfix">
	<?php ns_use_widget( 'main', 'top' ); ?>


	<?php
	ns_get_template_part( 'content' );
	ns_get_template_part( 'sidebar' );
	?>

	
	<?php ns_use_widget( 'main', 'bottom' ); ?>
	</div><!-- #main -->

</div><!-- #main-wrapper -->

