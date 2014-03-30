<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<?php if(!$_SESSION['cssIncText']) { ?>
	<title><?php wp_title(''); ?> <?php if ( !(is_404()) && (is_single()) or (is_page()) or (is_archive()) ) { ?> :: <?php } ?> <?php bloginfo('name'); ?></title>
<?php unset($_SESSION['cssIncText']); } ?>
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" /> 
	<link rel="icon" href="/favicon.ico" type="image/gif" />

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/screen.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/blueprint-wp.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/print.css" type="text/css" media="print" />

	<!-- To use a custom stylesheet, uncomment the next line: -->
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/style.css" type="text/css" media="screen, projection" />

	<!-- Javascripts  -->
	<!-- <script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery-1.1.3.1.pack.js"></script> -->
	<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/functions.js"></script>
	<!--[if lt IE 7]>
	<script defer type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/pngfix.js"></script>
	<![endif]-->

	<!-- Show the grid and baseline  -->
	<style type="text/css">
/*		.container { background: url(<?php bloginfo('stylesheet_directory'); ?>/css/lib/img/grid.png); }*/
	</style>

	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php wp_head(); ?>
	<style type="text/css">.find-center-tab {color:red}</style>
</head>
<body>
<!-- ClickTale Top part -->
<script type="text/javascript">
var WRInitTime=(new Date()).getTime();
</script>
<!-- ClickTale end of Top part -->

<script type="text/javascript" src="//www.hellobar.com/hellobar.js"></script>
<script type="text/javascript">
    new HelloBar(10349,24411);
</script>
<noscript>
    The Hello Bar is a simple <a href="https://www.hellobar.com">web toolbar</a> that engages users and communicates a call to action.
</noscript>



<div class="container">
  <!-- Header -->
  <div class="column span-14 first" id="header">
	<!-- Site Name -->
	<a class="logo" href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a>
	<div class="description"><?php bloginfo('description'); ?></div>
	<!-- Search -->
	<div style="vertical-align:text-top;"><?php include (TEMPLATEPATH . '/searchform.php'); ?></div>
<br /><br /><br />
<div id="headerOffer"></div>

  </div> <!-- #header -->

  

  <!-- Navigation -->
  <div class="column span-14 first large" id="nav">
	<div class="content">
<?php
    if (((!empty($post->ID) && get_post_meta($post->ID, 'no_nav', true) == '')) ||
        ((empty($post->ID) && ("" == $_SESSION['no_nav'])))): ?>
        <?php wp_nav_menu(array('menu' => 'Main Menu' )); ?>
		<div id="navOrderBtn"><div style="padding-top:4px;"><a href="/order" style="color:#0a524f">ORDER NOW</a></div></div>
        <?php unset($_SESSION['no_nav']); ?>
    <?php else: echo "&nbsp;"; endif; ?>
	</div>
  </div>
<!-- End Navigation -->
