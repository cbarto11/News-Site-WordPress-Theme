

<?php global $ns_config, $ns_mobile_support, $ns_template_vars; ?>
<?php if( $ns_config->show_template_part('subheader') ): ?>


<div id="subheader-wrapper" class="clearfix">

	<div id="subheader" class="clearfix">
	<?php ns_use_widget( 'subheader', 'top' ); ?>
	
	
	<?php ns_image( $ns_config->get_value('subheader', 'image') ); ?>
	

	<?php ns_use_widget( 'subheader', 'bottom' ); ?>
	</div><!-- #subheader -->

</div><!-- #subheader-wrapper -->


<?php endif; ?>

