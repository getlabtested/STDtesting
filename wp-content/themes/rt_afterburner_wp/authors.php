<?php get_header(); ?>

				<!-- Begin Main Content -->
				<div id="content" class="narrowcolumn">
<!-- This sets the $curauth variable -->
<?php
if(isset($_GET['author_name'])) :
$curauth = get_userdatabylogin($author_name);
else :
$curauth = get_userdata(intval($author));
endif;
?>
<h3>About: <?php echo $curauth->display_name; ?></h3>
<p><strong>Website:</strong> <a href="<?php echo $curauth->user_url; ?>"><?php echo $curauth->user_url; ?></a></p>
<p><strong>Profile:</strong> <?php echo $curauth->user_description; ?></p>
<h3>Posts by <?php echo $curauth->display_name; ?>:</h3>
<ul>
<!-- The Loop -->
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<li>
<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>">
<?php the_title(); ?></a>
</li>
<?php endwhile; else: ?>
<p><?php _e('No posts by this author.'); ?></p>
<?php endif; ?>
<!-- End Loop -->
</ul>
</div>

<?php get_footer(); ?>