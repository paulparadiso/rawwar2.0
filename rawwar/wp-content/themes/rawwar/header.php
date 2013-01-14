<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/rw.css" type="text/css" media="screen" />
<?php $agent = $_SERVER['HTTP_USER_AGENT'];
if (eregi("BlackBerry", $agent)) {?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/mobile.css" type="text/css" media="screen" />
<?php } ?>

<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_head(); ?>
</head>
<body>
<div id="header">
	<a href="<?php echo get_option('home'); ?>/"><img class="spacer" src="<?php bloginfo('template_directory'); ?>/images/spacer.png" alt="<?php bloginfo('name'); ?>" border="0"/></a>
	<?php include (TEMPLATEPATH . '/searchform.php'); ?>
	<ul>
		<?php wp_list_pages('title_li=&exclude=339,217' ); ?>
	<!--
		<li>About</li>
		<li>Browse Clips</li>
	-->
		<?php //wp_register('<li class="page_item">'); ?>
		<li class="page_item"><a href="/wp-admin/">Edit</a></li>
		<li class="page_item"><a href="http://www.womenartrevolution.com">!Women Art Revolution</a></li>
	</ul>
	<!--
	<div id="headerimg">
		<h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
		<div class="description"><?php bloginfo('description'); ?></div>
	</div>
	-->
	<?php //do_action('rw_render_add_video_form'); //todo: get rid of this ?>
</div>