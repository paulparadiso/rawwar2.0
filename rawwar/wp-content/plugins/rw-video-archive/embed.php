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
<script type="text/javascript">
	if (window == window.top) {
		window.location = "<?php the_permalink() ?>";
	}
</script>
<?php wp_head(); ?>
</head>
<body>
<?php
	global $post;
	if (have_posts() && (the_post() || true) && $post->video):
		$options = array(
			'fullscreen' => false,
			'edit_clip' => false,
			'share_clip' => false,
			'show_title' => true
		);
		if ($_REQUEST['width'] && is_numeric($_REQUEST['width'])) {
			$options['width'] = $_REQUEST['width'];
		}
		rw_the_player($options);
	endif;
?>
</body>
</html>