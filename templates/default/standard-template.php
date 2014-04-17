
<?php global $nh_config, $nh_mobile_support, $nh_template_vars; ?>


<!DOCTYPE html>

<!--[if lt IE 7 ]> <html class="ie6 old-ie no-js" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7 ]>    <html class="ie7 old-ie no-js" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]>    <html class="ie8 old-ie no-js" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="new-browser no-js" <?php language_attributes(); ?>>   <!--<![endif]-->

<head>

	<meta charset="<?php bloginfo('charset'); ?>" />
	<title><?php echo bloginfo('name').' | '.$nh_template_vars['page-title']; ?></title>
	
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<link rel="shortcut icon" href="<?php echo nh_get_theme_file_url('images/favicon.ico'); ?>" />

	<?php if( $nh_mobile_support->is_mobile ): ?>
		<meta name="viewport" content="user-scalable=no, initial-scale=1, minimum-scale=1, maximum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi">
	<?php endif; ?>
	
	<?php wp_head(); ?>

	<script type="text/javascript">
		jQuery('html').removeClass('no-js');
		var is_mobile = <?php echo ($nh_mobile_support->is_mobile) ? 'true' : 'false'; ?>;
		var use_mobile_site = <?php echo ($nh_mobile_support->use_mobile_site) ? 'true' : 'false'; ?>;
	</script>

</head>

<?php
	$class = array();
	if( $nh_mobile_support->use_mobile_site ) $class[] = 'mobile-site'; else $class[] = 'full-site';
?>
<body <?php body_class($class); ?> >

<div id="site-outside-wrapper">
<div id="site-inside-wrapper">
<?php nh_use_widget( 'site', 'top' ); ?>

	<?php
	nh_get_template_part( 'header' );
	nh_get_template_part( 'banner' );
	nh_get_template_part( 'subheader' );
	nh_get_template_part( 'mobile-menu' );
	nh_get_template_part( 'main' );
	nh_get_template_part( 'footer' );
	?>

<?php nh_use_widget( 'site', 'bottom' ); ?>
</div> <!-- #site-inside-wrapper -->
</div> <!-- #site-outside-wrapper -->

<?php wp_footer(); ?>

</body>

</html>

