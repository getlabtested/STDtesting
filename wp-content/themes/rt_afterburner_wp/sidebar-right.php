								<div id="col3" class="<?php echo get_option('after_rightcol_color'); ?>">
								
									<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Over Right Menu') ) : ?>
                					<?php endif; ?>
								
									<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Right Menu') ) : ?>
								


<!-- Begin Widget -->
		                        
                        			<div class="module m_menu">
										<h3 class="module-title"><?php _re('Main Menu'); ?></h3>
			    						<div class="module-body">
			    						
			    						<!-- Begin Widget Content -->
			    						
	        								<ul class="menu">
	        								
												<li class="home<?php if ( is_front_page() ) echo ' active';?>"><a href="<?php bloginfo('home'); ?>/"><span><?php _re('Home'); ?></span></a></li>
									
												<?php
												$my_pages = wp_list_pages('echo=0&title_li=&link_before=<span>&link_after=</span>');
												$lines = explode("\n", $my_pages);

												$output = "";
												foreach($lines as $line) {
													$line = trim($line);
													if (substr($line, 0, 4) == "<li ") {
	
														if (substr($line, -5, 5) != "</li>") {
															preg_match("#class=(?<!\\\)\"(.*)(?<!\\\)\"#U", $line, $klass);
															if (count($klass)) {
																$klass = $klass[0];
																$new_klass = substr($klass, 0, -1);
																$line = str_replace($klass, $new_klass.' parent"', $line);
															}
														}
													}

													$output .= $line."\n";
												}
												
												$output = str_replace('current_page_item', 'active', $output);
												$output = str_replace('current_page_ancestor', 'active', $output);

												echo $output;
												
												?>

	        								</ul>
	        							
	        							<!-- End Widget Content -->	
	        							
	        							</div>
									</div>
									
									<!-- End Widget -->



<!-- BEGIN COMMENT OUT WIDGETS


									<!-- Begin Widget -->
								
                        			<div class="module">
										<h3 class="module-title"><?php _re('Categories'); ?></h3>
									    <div class="module-body">
									    
									    	<!-- Begin Widget Content -->
									    
	        								<ul>
												<?php wp_list_categories('title_li='); ?>
											</ul>
											
											<!-- End Widget Content -->
											
										</div>
									</div>
									
									<!-- End Widget -->
									
									<!-- Begin Widget -->

									<div class="module">
										<h3 class="module-title"><?php _re('Archive'); ?></h3>
			    						<div class="module-body">
			    						
			    							<!-- Begin Widget Content -->
			    						
	        								<ul>
												<?php wp_get_archives('type=monthly&limit=12'); ?>
											</ul>
											
											<!-- End Widget Content -->
										
										</div>
									</div>
									
									<!-- End Widget -->
									
									<!-- Begin Widget -->

									<div class="module">
										<h3 class="module-title"><?php _re('Meta'); ?></h3>
			    						<div class="module-body">
			    						
			    							<!-- Begin Widget Content -->
			    						
			    							<ul>
	        									<li><?php wp_loginout(); ?></li>
												<li><a href="http://validator.w3.org/check/referer" title="This page validates as XHTML 1.0 Transitional"><?php _re('Valid'); ?> <abbr title="eXtensible HyperText Markup Language">XHTML</abbr></a></li>
												<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
												<li><a href="http://wordpress.org/" title="Powered by WordPress, state-of-the-art semantic personal publishing platform.">WordPress</a></li>
												<?php wp_meta(); ?>		
											</ul>
											
											<!-- End Widget Content -->
										
										</div>
									</div>
									
									<!-- End Widget -->



END COMMENT OUT WIDGETS -->


									
									<?php endif; ?>
									
									<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Under Right Menu') ) : ?>
                					<?php endif; ?>
										
                        		</div>