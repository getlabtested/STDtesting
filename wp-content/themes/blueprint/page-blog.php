<?php
/*
Template Name: Page - Blog
*/
?>

<?php get_header(); ?>

				<!-- Begin Main Content -->
		
<div id="page">

	<div class="column span-10 first" id="maincontent">

		<div class="content2">               				
               					<!-- Begin col1 -->
               				
                        		                  	

            <div class="page-container page-blog">								
                                
                <?php $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                query_posts('paged='.$paged.'&cat=-'.get_option('after_showcase_cat')); ?>
            
                <?php while (have_posts()) : the_post(); ?>
                
                <?php $more = 0; ?>
            
                <div class="article_row article-<?php the_ID(); ?>">
                    <div class="article_column column1 cols1">
                        <div class="colpad">
                            <h2 class="contentheading"><?php the_title(); ?></h2>
                            
                            
                            <?php the_content(false); ?>
                            
                            <?php if(preg_match("/\<\!\-\-more\-\-\>/", $post->post_content)) { ?>
                            
                            <a href="<?php the_permalink(); ?>" class="readon"><?php echo 'Learn More'; ?></a>
                            
                            <?php } ?>
                            
                        </div>
                    </div>
                </div>
                
                <?php endwhile;?>
                
                <?php global $wp_query; $total_pages = $wp_query->max_num_pages; if ( $total_pages > 1 ) { ?>
                                
                <div class="blog_nav">
                    <div class="alignleft"><?php next_posts_link('&laquo; Older Entries'); ?></div>
                    <div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
                    <div class="clr"></div>
                </div>
                                    
                <?php } ?>
                
            </div>

		</div>
        
	</div>
		                        
    <!-- End col1 -->
    
    <?php include ('sidebar-posts.php'); ?>
                                                                    
    <!-- End col3 -->
                        		
						    
</div>

<?php get_footer(); ?>