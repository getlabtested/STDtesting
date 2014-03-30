<?php
$navHTML= "";
//Grab the current post or parent post if available
if($post->post_parent == 0) {
	$parent = $post->ID;
	$disease = strtolower(get_the_title($post->ID));
} else {
	$parent = $post->post_parent;
	$disease = strtolower(get_the_title($post->post_parent));
}
//Parameters for the wordpress function
$args = array(
'sort_column' => 'menu_order', //The posts defined menu order
   'child_of' => $parent, //The parent ID from above
      'depth' => 2, //Only need to pull one sub-category down
       'echo' => false,
   'title_li' => null
);
//$overviewNav = '<li><a href="' . get_permalink($parent) .'" title="' . ucwords($disease) . '">' . Overview .'</a></li>' . PHP_EOL;
$overviewNav = '<li><a href="' . get_permalink($parent) .'" title="' . ucwords($disease) . '">' .ucwords($disease) . ' ' . Overview .'</a></li>' . PHP_EOL;
$navHTML = wp_list_pages($args);
//$navHTML = str_ireplace('>' . $disease . ' ','>',$navHTML);
$navHTML = $overviewNav . $navHTML;
?>
<style type="text/css" media="screen">
  div#sidenavSHG {border-bottom: 0px;}
  div#sidenavSHG li:last-child {border-bottom: 0px none;}
</style>
<div id="sidenavSHG" class="column span-3 first">
  <ul>
    <li>
      <ul>
      <?php echo $navHTML; ?>
      </ul>
    </li>
  </ul>
</div>
