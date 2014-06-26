

<?php global $nh_config, $nh_mobile_support, $nh_template_vars; ?>
<?php if( $nh_config->show_template_part('footer') ): ?>

<div id="footer-wrapper" class="clearfix">

	<div id="footer" class="clearfix">
	<?php nh_use_widget( 'footer', 'top' ); ?>
	
		<?php $footer_widgets = $nh_config->get_footer_widget_areas(); ?>
	
		<div class="clearfix widget-area num-cols-<?php echo count($footer_widgets); ?>">
			<?php foreach($footer_widgets as $widget ): ?>
				<?php nh_use_widget( 'footer', $widget ); ?>
			<?php endforeach; ?>
		</div>

	<div class="copyright">
	<?php echo $nh_config->get_value( 'footer', 'copyright' ); ?>
	</div>
	
	<?php nh_use_widget( 'footer', 'bottom' ); ?>
	</div><!-- #footer -->

</div><!-- #footer-wrapper -->


<?php endif; ?>

