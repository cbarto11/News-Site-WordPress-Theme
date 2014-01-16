

<?php global $ns_config, $ns_mobile_support, $ns_template_vars; ?>


<?php 

if( have_posts() ):

	ns_get_template_part( 'listing', 'content', 'none' );
	
else:

	?>
	<h1><?php echo $ns_template_vars['page-title']; ?></h1>

	<p>Sorry, but nothing matched your search criteria. Please try again with some different keywords.</p>
	<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>" >
		<label class="screen-reader-text" for="s">Search for:</label>
		<div class="textbox_wrapper"><input type="text" value="<?php echo $_GET['s']; ?>" name="s" id="s" /></div>
		<input type="submit" id="searchsubmit" value="Search" />
	</form>
	<?php
	
endif;

?>

