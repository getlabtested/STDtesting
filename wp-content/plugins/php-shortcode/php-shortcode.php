<?php
/*
Plugin Name: PHP Shortcode
Plugin URI: 
Description: Based on kukukuan's Inline PHP plugin, this plugin allows you to embed and run PHP code in posts, pages or widgets with a WordPress shortcode.
Version: 1.3
Author: Godfrey Chan
Author URI: http://www.chancancode.com/
*/

function php_shortcode_handler($atts = array(), $content = null) {
    extract(shortcode_atts(array('echo' => false), $atts));
    
    // Prepare the PHP code
    if((boolean) $echo) {
        if($content != null && $content != '')
            $content = 'echo '.$content;
    }
    
    $content = $content . ';';
    
    // Init
    $old_error_level = error_reporting(0);
    ob_start();
    
    // Eval
    if(version_compare(PHP_VERSION, '5.0.0', '>')) {
        try { eval($content); } catch(Exception $e) {}
    } else {
        eval($content);
    }
    
    // Cleanup
    $output = ob_get_contents();
    ob_end_clean();
    error_reporting($old_error_level);
    
    return $output;
}

function php_echo_shortcode_handler($atts = array(), $content = null) {
    return php_shortcode_handler(array('echo' => true), $content);
}

function php_shortcode_init() {    
    add_shortcode('php', 'php_shortcode_handler');
    add_shortcode('echo', 'php_echo_shortcode_handler');
    
    // Move do_shortcode to priority 9 (before any WP formatting)
    if(remove_filter('the_content','do_shortcode', 11))
        add_filter('the_content','do_shortcode',9);
}

add_action('init','php_shortcode_init');

?>