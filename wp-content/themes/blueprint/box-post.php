<!-- Post Box -->
<style type="text/css">
#box-post {
  margin-bottom: 15px;
}
.box-post-posts {
  margin-bottom: 10px;
}
</style>
<div id="box-post" class="clear span-10">
    <?php
    add_filter('excerpt_length', 'ppmd_post_box_excerpt_length', 999);
    add_filter('excerpt_more', 'ppmd_post_box_excerpt_more', 100);
    function ppmdGetParent() {
        global $post;
        $parent = -1;
        if(0 === $post->post_parent) {
            $parent = $post->ID;
        } else {
            $parent = $post->post_parent;
        }
        return get_post($parent);
    }
    $parent = ppmdGetParent();
    $args = array( 'orderby' => 'rand', 'category_name' => $parent->post_name, 'numberposts' => '4', 'order'=> 'ASC' );
    $postsList = get_posts( $args );
    //@todo This is a bad work around, the catrgory names need to stay consistent. Please reset the category slug name.
    /*if(null === $postsList) {
        $args = array( 'category_name' => $parent->post_name . '-2', 'numberposts' => '4', 'order'=> 'ASC' );
        $postsList = get_posts( $args );
    }*/
    $i = 0;
    foreach ($postsList as $post) : setup_postdata($post); ?>
      <div class="box-post-posts column span-5 <?php if(0 === $i % 2) { echo 'first'; } else { echo 'last'; } ?>">
        <h3><?php the_title(); ?></h3>
        <p class="last"><?php echo get_the_excerpt(); ?></p>
      </div>
      <?php if(0 !== $i % 2) : ?>
        <div class="clear" ></div>
      <?php endif; ?> 
      <?php  $i++; ?>
    <?php endforeach; ?>
</div>
<!-- /Post Box -->
