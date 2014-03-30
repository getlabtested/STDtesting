<?php	
/*  Copyright 2006 Vincent Prat  (email : vpratfr@yahoo.fr)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//#################################################################
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('You are not allowed to call this page directly.'); 
}
//#################################################################


//#################################################################
// The Importer class for old 1.x plugin version
if (!class_exists("PostTemplates1xImporter")) {

class PostTemplates1xImporter {

	var $imported_templates = 0;
	var $deleted_templates = 0;
	var $parsed_posts = 0;
	
	/*
	* Constructor
	*/
	function PostTemplates1xImporter() {
	}

	/*
	* Function to actually import the old templates
	*/
	function import($copy_old_templates = false, $delete_old_templates = false) {
		global $wpdb, $post_templates_dao;
		
		$imported_templates = 0;
		$deleted_templates = 0;
		$parsed_posts = 0;

		$posts = $wpdb->get_results("SELECT * FROM " . $wpdb->posts . " WHERE post_type='template' OR post_type='template-page'");		
		foreach ($posts as $post) {
			if ($copy_old_templates) {
				$post_templates_dao->create_template_from_post($post->ID);
				$imported_templates++;
			}
			
			if ($delete_old_templates) {
				wp_delete_post($post->ID);
				$deleted_templates++;
			}
			
			$parsed_posts++;
		}
	}
	
} // class PostTemplates1xImporter {

} // if (!class_exists("PostTemplates1xImporter")) {

?>