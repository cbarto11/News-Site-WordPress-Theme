

<?php global $ns_config, $ns_mobile_support, $ns_template_vars, $post; ?>
<?php 
$section = $ns_template_vars['section'];
$story = $section->get_single_story( $post ); 
?>


<?php if( $section->key !== 'none' && $section->key !== 'multi' ): ?>
<div class="section-archive-link">
	<?php echo ns_get_anchor( 
		$section->get_section_link(), 
		$section->name.' Archives',
		null,
		$section->title
	); ?>
</div>
<?php endif; ?>

<h1><?php echo $story['title']; ?></h1>

<div class="story <?php echo $section->key; ?>-section <?php echo $section->featured_image; ?>-image clearfix">

	<div class="details">

	<?php if( $story['image'] ): ?>

		<div class="image">
			<img src="<?php echo $story['image']; ?>" alt="Featured Image" />
		</div><!-- .image -->

	<?php endif; ?>

	<?php if( count($story['description']) > 0 ): ?>

		<div class="description">

		<?php 
		foreach( $story['description'] as $key => $value ):
			if( is_array($value) ):
				
				?><div class="<?php echo $key; ?>"><?php
				
				foreach( $value as $k => $v ):
					?><div class="<?php echo $k; ?>"><?php echo $v; ?></div><?php
				endforeach;
				
				?></div><?php
			
			else:

				?><div class="<?php echo $key; ?>"><?php echo $value; ?></div><?php
				
			endif;
		endforeach;
		?>

		</div><!-- .contents -->

	<?php endif; ?>

	</div><!-- .description -->

</div><!-- .story -->

