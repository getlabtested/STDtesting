<?php
/*
Template Name: Page with Sidebar 2
*/
?>

<?php get_header(); ?>

<div id="page">

	<div class="column span-10 first" id="maincontent">

		<div class="content2">

			<?php 
			
				//$my_query = new WP_Query('showposts=8');
				//while ($my_query->have_posts()) : $my_query->the_post();
				
				// Don't show featured item
				//if ($post->ID == $featuredID) continue;
				
				while (have_posts()) : the_post();
				
				?>
				
				<?php getStaticPage($post); ?>
					
				<?php endwhile; ?>
	
					<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
					<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>

		</div> <!-- /content -->
	</div> <!-- /maincontent-->
	
<?php include ('sidebar3.php'); ?>

</div> <!-- /page -->

<?php get_footer(); ?>
