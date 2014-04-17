

<?php global $nh_config, $nh_mobile_support, $nh_template_vars; ?>
<?php if( $nh_config->show_template_part('mobile-menu') && $nh_mobile_support->use_mobile_site ): ?>

<?php $widget_areas = $nh_config->get_mobile_widget_areas(); ?>

<div id="mobile-menu-wrapper" class="clearfix">

	<div id="mobile-menu" class="clearfix">
	<?php nh_use_widget( 'mobile-menu', 'top' ); ?>
	
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
				
		<div id="<?php echo $widget['id']; ?>" class="expanded-menu">
			<?php nh_use_widget( 'mobile-menu', $widget['index'] ); ?>
		</div>
		
		<?php endforeach; ?>
		
	</div><!-- .menu -->
	
	<?php nh_use_widget( 'mobile-menu', 'bottom' ); ?>
	</div><!-- #mobile-menu -->

</div><!-- #mobile-menu-wrapper -->


<?php endif; ?>

