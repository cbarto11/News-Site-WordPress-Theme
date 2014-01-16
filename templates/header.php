

<?php global $ns_config, $ns_mobile_support, $ns_template_vars; ?>
<?php if( $ns_config->show_template_part('header') ): ?>


<div id="header-wrapper" class="clearfix">

	<div id="header" class="clearfix">
	<?php ns_use_widget( 'header', 'top' ); ?>


	<?php
	$image_info = ns_get_image_info($ns_config->get_value('header', 'image'));
	
	?>
	
	<div class="masthead" style="background-image:url('<?php echo $image_info['url']; ?>'); height:<?php echo $image_info['height']; ?>px;">
	
		<?php 
		$title_box_info = $ns_config->get_value('header', 'title-box'); 
		if( $title_box_info['show-title'] || $title_box_info['show-description'] ):
		?>
			<div class="title-box-wrapper" style="height:<?php echo $image_info['height']; ?>px;">
			<div class="title-box <?php echo $title_box_info['position'] ?>">
				<?php if( $title_box_info['show-title'] ): ?>
					<div class="name"><?php echo get_bloginfo('name'); ?></div>
				<?php endif; ?>
				<?php if( $title_box_info['show-description'] ): ?>
					<div class="description"><?php echo get_bloginfo('description'); ?></div>
				<?php endif; ?>
			</div>
			</div>
		<?php endif; ?>
		
		<?php if( !empty($image_info['link']) ): ?>
			<a href="<?php echo $image_info['link']; ?>" title="<?php echo $image_info['title']; ?>" class="click-box"></a>
		<?php endif; ?>
	</div>
	
	<?php ns_use_widget( 'header', 'bottom' ); ?>
	</div><!-- #header -->

</div><!-- #header-wrapper -->


<?php endif; ?>

