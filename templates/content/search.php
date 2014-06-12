

<?php global $nh_config, $nh_mobile_support, $nh_template_vars, $wp_query; ?>


<?php 

if( have_posts() ):

	nh_get_template_part( 'listing', 'content', 'none' );
	
else:

	?>
	<h1><?php echo $nh_template_vars['page-title']; ?></h1>

	<p>Sorry, but nothing matched your search criteria. Please try again with some different keywords.</p>
	<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>" >
		<label class="screen-reader-text" for="s">Search for:</label>
		<div class="textbox_wrapper"><input type="text" value="<?php echo get_search_query(); ?>" name="s" id="s" /></div>
		<input type="submit" id="searchsubmit" value="Search" />
	</form>
	<?php
	
endif;

?>

