<?php get_header(); ?>

<div id="page">

	<div class="column span-11 first" id="maincontent">

		<div class="content">

		<h2>Error 500 - Service Temporarily Unavailable</h2>
		
		<p>Yikes. Something went wrong.</p>
		<?php if (isset($errMessage)): ?>
		<p><?php echo $errMessage; ?></p>
		<?php endif; ?>
		<p>You can call our support number to continue your order: <strong>866-749-6269</strong></p>

		</div> <!-- /content -->
	</div> <!-- /maincontent-->

<?php get_sidebar(); ?>

</div> <!-- /page -->

<?php get_footer(); ?>
