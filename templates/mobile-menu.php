

<?php global $ns_config, $ns_mobile_support, $ns_template_vars; ?>
<?php if( $ns_config->show_template_part('mobile-menu') && $ns_mobile_support->use_mobile_site ): ?>

<?php $widget_areas = $ns_config->get_mobile_widget_areas(); ?>

<div id="mobile-menu-wrapper" class="clearfix">

	<div id="mobile-menu" class="clearfix">
	<?php ns_use_widget( 'mobile-menu', 'top' ); ?>
	
	<script type="text/javascript">
	jQuery(document).ready(function()
	{
		jQuery('#mobile-menu .menu').MobileMenu();
	});
	</script>

	<div class="menu">

		<ul>
			<?php
			foreach($widget_areas as $widget)
				echo '<li><a href="#'.$widget['id'].'">'.$widget['name'].'</a></li>';
			?>
		</ul>

		<?php foreach($widget_areas as $widget): ?>
				
		<div id="<?php echo $widget['id']; ?>">
			<?php ns_use_widget( 'mobile-menu', $widget['index'] ); ?>
		</div>
		
		<?php endforeach; ?>
		
	</div><!-- .menu -->
	
	<?php ns_use_widget( 'mobile-menu', 'bottom' ); ?>
	</div><!-- #mobile-menu -->

</div><!-- #mobile-menu-wrapper -->


<?php endif; ?>

