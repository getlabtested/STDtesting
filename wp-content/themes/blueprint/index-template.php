<?php
/*
Template Name: Index
*/
?>

<?php get_header(); ?>
<div id="page">

	<?php get_sidebar(); ?>

	<div class="column span-11 first" id="maincontent">
		<div class="content">
			<div class="column span-11 first" id="HomeContentRow1">
			<div id="HomeContentRow1-bucketLt">
		    	<div>
		<?php
		$sticky = get_option('sticky_posts');
		rsort( $sticky );
		$sticky = array_slice( $sticky, 0, 1);
		query_posts( array( 'post__in' => $sticky, 'caller_get_posts' => 1 ) );

		while (have_posts()) : the_post();
			$img = get_post_meta($post->ID, 'Featured Thumbnail', true);
		?>
			<img height="235" width="430" style="margin-bottom:10px;" src="<?php echo $img; ?>"/><br />
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><? the_title('<h3>', '</h3>'); ?></a>
				<?php
				//the_excerpt();
				echo limit_words(get_the_excerpt(), '40');
				echo '...<br /><strong><a href="'. get_permalink($post->ID) . '">LEARN MORE</a></strong>';
		endwhile;
		wp_reset_query();
		while (have_posts()) : the_post();
			the_content('Read the rest of this entry &raquo;');
		endwhile; 
            ?>
			<?php
		$sticky = get_option('sticky_posts');
		rsort( $sticky );
		$sticky = array_slice( $sticky, 1, 3);
		query_posts( array( 'post__in' => $sticky, 'caller_get_posts' => 1 ) );

		while (have_posts()) : the_post();
          		$img = get_post_meta($post->ID, 'Featured Thumbnail', true);
			?>
			<div style="width:237px; float:left; margin-right:20px;" <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<div class="entry">
					<img style="margin-bottom:10px;" src="<?php echo $img; ?>"/><br />
					<div style="margin-bottom:5px;"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title('<h3>', '</h3>'); ?></a></div>
						<?php
						//the_excerpt();
						echo limit_words(get_the_excerpt(), '40');
						echo '...<br /><strong><a href="'. get_permalink($post->ID) . '">LEARN MORE</a></strong>';
						?>
				</div>
			</div>
			<?php
				endwhile;
			?>
	</div>

	</div> <!-- /content -->

	</div> <!-- /maincontent-->



</div> <!-- /page -->



<?php get_footer(); ?>

