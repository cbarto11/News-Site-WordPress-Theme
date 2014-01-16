

<?php global $ns_config, $ns_mobile_support, $ns_template_vars; ?>
<?php if( $ns_config->show_template_part('footer') ): ?>

<div id="footer-wrapper" class="clearfix">

	<div id="footer" class="clearfix">
	<?php ns_use_widget( 'footer', 'top' ); ?>
	
		<?php $footer_widgets = $ns_config->get_footer_widget_areas(); ?>
	
		<div class="clearfix widget-area num-cols-<?php echo count($footer_widgets); ?>">
			<?php foreach($footer_widgets as $widget ): ?>
				<?php ns_use_widget( 'footer', $widget ); ?>
			<?php endforeach; ?>
		</div>

	<div class="copyright">
	<?php echo $ns_config->get_value('footer', 'copyright'); ?>
	</div>
	
	<?php ns_use_widget( 'footer', 'bottom' ); ?>
	</div><!-- #footer -->

</div><!-- #footer-wrapper -->


<?php endif; ?>

