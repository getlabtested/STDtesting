<?php
/*
Template Name: Page - Landing
*/
?>

<?php get_header(); ?>

				<!-- Begin Main Content -->
				
				<?php after_column_ninja(); ?>
				
      			<div id="main-content" class="<?php echo $column_ninja_ext; ?>">
												
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
						
										<?php the_content(); ?>
										
										<?php edit_post_link(_r('Edit this entry.'), '', ''); ?>
										
										<?php if(comments_open()) { ?>
							
										<a name="comments"></a>
																				
										<?php comments_template(); ?>
										
										<?php } ?>
																							
							<?php endwhile; else: ?>
								
								<span class="attention"><?php _re('Sorry, no pages matched your criteria.'); ?></span>
								
							<?php endif; ?>
      		</div>

<?php get_footer(); ?>