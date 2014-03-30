<?php get_header(); ?>



<div id="page">



<?php get_sidebar(); ?>



	<div class="column span-11 first" id="maincontent">



		<div class="content">



			<?php 

			

				//$my_query = new WP_Query('showposts=8');

				//while ($my_query->have_posts()) : $my_query->the_post();

				

				// Don't show featured item

				//if ($post->ID == $featuredID) continue;

				

				while (have_posts()) : the_post();

				

				?>

				

				<?php getPost($post); ?>

					

				<?php endwhile; ?>
HERE
	    <?php
$my_query = new WP_Query('cat=6&posts_per_page=3');
while ($my_query->have_posts()) : $my_query->the_post();
        ?>
THERE
        <div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
          <h2><?php permalink(); ?></h2>
          <div class="entry">
          <?php
          $img = get_post_meta($post->ID, 'Featured Thumbnail', true);
          ?><img src="<?php echo $img; ?>"/><?php
          the_content('Read More');
          ?>
          </div>
        </div>
        <?php
      endwhile;
    endif;
    wp_reset_query();
    ?>
</div>

					<div class="alignleft"><?php next_posts_link('&laquo; Previous STD Articles') ?></div>

					<div class="alignright"><?php previous_posts_link('Next STD Articles &raquo;') ?></div>



		</div> <!-- /content -->

	</div> <!-- /maincontent-->

	





</div> <!-- /page -->



<?php get_footer(); ?>

