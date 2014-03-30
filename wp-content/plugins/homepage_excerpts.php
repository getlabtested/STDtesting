<?php
/*
Plugin Name: Homepage Excerpts
Plugin URI: http://www.dailyblogtips.com/homepage-excerpts-wordpress-plugin/
Description: Homepage Excerpts
Author: http://www.dailyblogtips.com
Version: 1.0
Author URI: http://www.dailyblogtips.com/
*/
    ##### Function ######
    # Function: Install
        add_action('activate_homepage_excerpts.php', 'homepage_excerpts');
		function homepage_excerpts(){
			add_option("homepage_excerpts_number_words", '30');
			add_option("homepage_excerpts_number_posts", '1');
			add_option("homepage_excerpts_display", 'no');
		}
    #END ##### Function ######

    ##### Add menu #####
	add_action('wp_head', 'homepage_excerpts_session');
	function homepage_excerpts_session(){$_SESSION['homepage_excerpts_nri'] = 0;}
	
    add_action('admin_menu', 'homepage_excerpts_menu');
    function homepage_excerpts_menu() {
        if (function_exists('add_options_page')) {
            add_options_page('Homepage Excerpts', 'Homepage Excerpts', 9, 'homepage_excerpts', 'homepage_excerpts_display');
        }
    }
    #END ##### Add menu #####
    
    ##### Menu ######
    function homepage_excerpts_display(){
		$ppp = get_option('posts_per_page');
		
        if($_POST['Submit']){
			$homepage_excerpts_number_words = $_POST['homepage_excerpts_number_words'];
			if ($homepage_excerpts_number_words < 1) $homepage_excerpts_number_words = 50;
			
			update_option("homepage_excerpts_number_words", $homepage_excerpts_number_words);
			update_option("homepage_excerpts_number_posts", $_POST['homepage_excerpts_number_posts']);
			update_option("homepage_excerpts_display", $_POST['homepage_excerpts_display']);
			
			echo '<div id="message" class="updated fade"><p>Update successful!</p></div>';
		}
		$output = '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';

		// GENERAL OPTIONS
		$output .= '<div class="wrap">'."\n";
		$output .= '	<h2>General options</h2>'."\n";
		$output .= '	<table width="100%" border="0" cellspacing="0" cellpadding="6">'."\n";

		$output .= '		<tr>'."\n";
		$output .= '			<td align="right" width="30%">Number of posts to be displayed fully: </td>'."\n";
		$output .= '			<td align="left">';
		$output .= '				<select name="homepage_excerpts_number_posts">'."\n";
		
		if ($ppp > 11) $ppp = 11;
		for ($i=0;$i<=$ppp;$i++){
			if (get_option('homepage_excerpts_number_posts') == $i){
				$output .= '					<option value="'.$i.'" selected="selected">'.$i.'</option>'."\n";
			}
			else {
				$output .= '					<option value="'.$i.'">'.$i.'</option>'."\n";
			}
		}
		$output .= '				</select>'."\n";
		$output .= '			</td>';
		$output .= '		</tr>'."\n";
		
		$homepage_excerpts_display = get_option('homepage_excerpts_display');
		$output .= '		<tr>'."\n";
		$output .= '			<td align="right" width="30%">Use custom post excerpt? </td>'."\n";
		$output .= '			<td align="left">';
		$output .= '				<select name="homepage_excerpts_display">'."\n";
		$output .= '					<option value="no"';if ($homepage_excerpts_display == 'no') $output .= 'selected="selected"';$output .= '>No</option>'."\n";
		$output .= '					<option value="yes"';if ($homepage_excerpts_display == 'yes') $output .= 'selected="selected"';$output .= '>Yes</option>'."\n";
		$output .= '				</select>'."\n";
		$output .= '			</td>';
		$output .= '		</tr>'."\n";
		$output .= '		<tr>'."\n";
		$output .= '			<td align="right" width="30%">If not using post excerpts, grab the first X words from the post: </td>'."\n";
		$output .= '			<td align="left"><input type="text" name="homepage_excerpts_number_words" value="'.get_option('homepage_excerpts_number_words').'" /></td>';
		$output .= '		</tr>'."\n";

		$output .= '		<tr>'."\n";
		$output .= '			<td align="center" colspan="2">'."\n";
		$output .= '				<input type="submit" name="Submit" class="button" value="Update" />&nbsp;&nbsp;'."\n";
		$output .= '			</td>'."\n";
		$output .= '		</tr>'."\n";
		$output .= '	</table>'."\n";
		$output .= '</form>';
		$output .= '</div>'."\n";
        echo $output;
    }
    #END ##### Menu ######   
    function homepage_excerpts_filter($content){
		global $post;
		$homepage_excerpts_more = ' [...] <a href="'.get_permalink().'" class="more-link">Continue Reading...</a>';
		$output = $content;
		
		if (is_home()){
			$_SESSION['homepage_excerpts_nri'] += 1;

			if ($_SESSION['homepage_excerpts_nri'] <= get_option('homepage_excerpts_number_posts') && get_query_var('paged') == ''){
				$output = $post->post_content;
			}
			else {
				
				if (!empty($post->post_password)) { // if there's a password
					if ($_COOKIE['wp-postpass_'.COOKIEHASH] != $post->post_password) { // and it doesn't match cookie
						$output = get_the_password_form();
					}
				}
				else {
					if (get_option("homepage_excerpts_display") == 'yes' && !empty($post->post_excerpt)){
						$output = $post->post_excerpt.$homepage_excerpts_more;
					}
					else {
						$nr_word = get_option("homepage_excerpts_number_words");
						$output = preg_replace('@<script[^>]*?>.*?</script>@si', '', $post->post_content); //remove javascript
						$output = preg_replace('@<![\s\S]*?--[ \t\n\r]*>@', '', $output); // remove CDATA, html comments
						$output = strip_tags($output);
						$words = explode(' ', $output, $nr_word);
						$hhh = count($words);
						if ($hhh >= $nr_word){
							array_pop($words);
							$output = implode(' ', $words);
							$output = $output.$homepage_excerpts_more;
						}
					}
				}
			}
		}
		return $output;
    }
	add_filter('the_content', 'homepage_excerpts_filter',0);
?>
