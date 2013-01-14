<?php
if ( function_exists('register_sidebar') )
    register_sidebar(array(
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget' => '</li>',
        'before_title' => '<h2 class="widgettitle">',
        'after_title' => '</h2>',
    ));

//custom login header
function rw_login_head() {
	echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('template_directory') . '/rw-admin.css" />';
}
add_action('login_head', 'rw_login_head');

//custom admin header
function rw_admin_head() {
	echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('template_directory') . '/rw-admin.css" />';
}
add_action('admin_head', 'rw_admin_head');

?>