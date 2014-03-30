				<div id="footer">
					<div class="footer-pad">
					
						<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Bottom') ) : ?>
					
                		<ul class="menu">
                		
                			<?php if (get_post_meta($post->ID, 'no_nav', true) == '') {wp_list_bookmarks('title_li=&categorize=0&category_name=blogroll&title_before=<span>&title_after=</span>');} ?>
											
                		</ul>
                		
                		<?php endif; ?>
                		
            		</div>
				</div>
				
				<?php if(get_option('after_rocketlogo') == "true") { ?> 
				
					<a href="http://www.rockettheme.com"><span id="logo2"></span></a>
					
				<?php } ?>
					
				<div class="module">
				    <div class="module-body">
				        <div>
				        	<?php _re('Copyright © STDTesting.com. All rights reserved.'); ?>
				        </div>
						
					</div>
				</div>
		
			</div>
		</div>
		
		<?php wp_footer(); ?>
		
	</body>
</html>