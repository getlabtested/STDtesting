<?php
/*
Template Name: Sexual Health Guide
*/
?>
<?php get_header(); ?>
<div id="page">
	<div class="column span-10 first" id="maincontent">
		<div class="content2">
            <h1><?php wp_title("",true); ?></h1>
            <?php include_once TEMPLATEPATH . '/side-nav.php'; ?>
			<?php if(have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
			<?php getStaticPage($post); ?>
			<?php endwhile; ?>
			<?php endif; ?>
			<div class="alignleft"><?php next_posts_link('&laquo; Previous Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Next Entries &raquo;') ?></div>
		</div> <!-- /content -->
        <?php include_once TEMPLATEPATH . '/box-post.php'; ?>
        <?php include_once TEMPLATEPATH . '/info-page-buy.php'; ?>
	</div> <!-- /maincontent-->
<?php include ('sidebar2.php'); ?>
</div> <!-- /page -->
<?php get_footer(); ?>

