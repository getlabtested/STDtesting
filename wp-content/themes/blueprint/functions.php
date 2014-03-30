<?php
include_once TEMPLATEPATH . '/functions-ppmd.php';
/*-----------------------------------------------------------------------------
	Remove AutoP and Texturize from Pages
-----------------------------------------------------------------------------*/
//if (is_page()) {
//disable auto p
//remove_filter ('the_content', 'wpautop');
// Remove auto formatting
remove_filter('the_content', 'wptexturize');
//}

/*-----------------------------------------------------------------------------
	Except - Read More
-----------------------------------------------------------------------------*/
function new_excerpt_more($more) {
       global $post;
	return '...<br /><strong><a href="'. get_permalink($post->ID) . '">Learn More</a></strong>';
}
add_filter('excerpt_more', 'new_excerpt_more');

/*-----------------------------------------------------------------------------
	Limit the number of words in an excerpt, post, or string
-----------------------------------------------------------------------------*/
function limit_words($string, $word_limit) {
 
	// creates an array of words from $string (this will be our excerpt)
	// explode divides the excerpt up by using a space character
 
	$words = explode(' ', $string);
 
	// this next bit chops the $words array and sticks it back together
	// starting at the first word '0' and ending at the $word_limit
	// the $word_limit which is passed in the function will be the number
	// of words we want to use
	// implode glues the chopped up array back together using a space character
	return implode(' ', array_slice($words, 0, $word_limit));
 
}
/*-----------------------------------------------------------------------------
	Widget Support
-----------------------------------------------------------------------------*/
if ( function_exists('register_sidebar') )
    register_sidebar();

/*-----------------------------------------------------------------------------
	Display Post Template
-----------------------------------------------------------------------------*/
function getPost($post = NULL) {
	include('post.php');
}

/*-----------------------------------------------------------------------------
	Display Post Template
-----------------------------------------------------------------------------*/
function getStaticPage($post = NULL) {
	include('static-post.php');
}

/*-----------------------------------------------------------------------------
	Install Options - Not implemented yet
-----------------------------------------------------------------------------
if (!get_option('blueprint_installed')) {

	add_option('blueprint_installed',
						 $current, 
						 'This options simply tells me if K2 has been installed before', 
						 $autoload);

	add_option('blueprint_aboutblurp', 
						 'Enter your about text', 
						 'Allows you to write a small blurp about you and your blog, which will be put on the frontpage', 
						 $autoload);

	add_option('blueprint_columns', 
						 '12', 
						 'The number of columns across the page (default is 12)', 
						 $autoload);

	add_option('blueprint_columns_content',
						 '9', 
						 'Number of columns the content (left side) takes up (default is 9, this and sidebar must add up to total column number)',
							$autoload);

	add_option('blueprint_column_sidebar',
						 '3', 
						'Number of columns the sidebar (right side) takes up (default is 3, this and left column must add up to total column number)',
						$autoload);

	add_option('blueprint_show_author',
						 '0', 
						'Show the author on posts? (Default: no)',
						$autoload);

	add_option('blueprint_show_tagline',
						 '0', 
						'Show the tagline under the site title? (Default: no)',
						$autoload);

}
*/
/**
 * Removes default title in external pages
 * @param string $buffer The current PHP buffer
 * @return string The buffer with <title/> removed
 */
function ppmdTitleCallback($buffer) {
        return str_replace('<title>  STDTesting.com</title>', '' , $buffer);
}
//@todo Not used, under test
function my_excerpt($text, $excerpt)
{
    if ($excerpt) return $excerpt;
    $text = strip_shortcodes( $text );
    $text = apply_filters('the_content', $text);
    $text = str_replace(']]>', ']]&gt;', $text);
    $text = strip_tags($text);
    $excerpt_length = apply_filters('excerpt_length', 55);
    $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
    $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
    if ( count($words) > $excerpt_length ) {
            array_pop($words);
            $text = implode(' ', $words);
            $text = $text . $excerpt_more;
    } else {
            $text = implode(' ', $words);
    }
    return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}

//box-post configs 
function ppmd_post_box_excerpt_more($more) {
    global $post;
    return '<br /><a href="'. get_permalink($post->ID) . '"><b>LEARN MORE</b></a>';
}
function ppmd_post_box_excerpt_length($length) {
    return 40;
}
/**
 * Add custom css files
 * As of now not needed MCH
 */
#add_action('wp_print_styles', 'add_stdtesting_style');
function add_stdtesting_style() {
	$css = '/css/stdtesting.css';
	$stdtestingStyleUrl = get_bloginfo('template_url') . $css;
        $stdtestingStyleFile = TEMPLATEPATH . $css;
	if ( file_exists($stdtestingStyleFile) ) {
		wp_register_style('stdtesting', $stdtestingStyleUrl, false, false, 'all');
		wp_enqueue_style('stdtesting');
	}
}

/**
 * Site phone number to display to customer
 */
function affPhoneNumber() {
    return '866-749-6269';
}
