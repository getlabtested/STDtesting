<?php
/*
Template Name: Page - Inner
*/
?>

<?php get_header(); ?>

				<?php if (get_posts('cat='.get_option('after_showcase_cat')) && get_option('after_showcase_cat') != '-1') { ?>

				<!-- Begin Showcase -->
	
				
				
				<!-- End Showcase -->
				
				<?php } ?>
				
				<!-- Begin Main Content -->
				
				<?php after_column_ninja(); ?>
				
      			<div id="main-content" class="<?php echo $column_ninja_ext; ?>">
            		<div id="colmask" class="ckl-<?php echo get_option('after_leftcol_color'); ?>">
         		    	<div id="colmid" class="cdr-color1">
               				<div id="colright" class="ctr-<?php echo get_option('after_rightcol_color'); ?>">
               				
               					<!-- Begin col1 -->
               				
                        		<div id="col1wrap">
									<div id="col1pad">
		                            	<div id="col1">
			                                <div class="component-pad">             
												<div class="blog page-cms">
												
													<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
												
													<div class="article_row page-<?php the_ID(); ?>">
														<div class="article_column column1 cols1">
															<div class="colpad">
													
								
																
																<?php the_content(); ?>
																
																<?php edit_post_link(_r('Edit this entry.'), '', ''); ?>
																
																<?php if(comments_open()) { ?>
													
																<a name="comments"></a>
																										
																<?php comments_template(); ?>
																
																<?php } ?>
																
															</div>
														</div>
													</div>
													
													<?php endwhile; else: ?>
														
														<span class="attention"><?php _re('Sorry, no pages matched your criteria.'); ?></span>
														
													<?php endif; ?>
													
												</div>
		                                    </div>
			                            </div>
									</div>
		                        </div>
		                        
		                        <!-- End col1 -->
		                        
		                        <!-- Begin col2 -->
		                        
		                        <?php if(get_option('after_leftcol_enabled') == "true") { ?>
		                        
		                        	<?php get_sidebar('left'); ?>
		                        	
		                       	<?php } ?>
 		                        
		                        <!-- End col2 -->
		                        
		                        <!-- Begin col3 -->
		                        
		                        <?php if(get_option('after_rightcol_enabled') == "true") { ?>
		                        
									<?php get_sidebar('right'); ?>
                        		
                        		<?php } ?>
                        		
                        		<!-- End col3 -->
                        		
						    </div>
                		</div>
            		</div>
        		</div>

<?php get_footer(); ?>