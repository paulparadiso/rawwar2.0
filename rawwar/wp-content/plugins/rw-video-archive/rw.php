<?php
/*
Plugin Name: VideoArchive
Plugin URI: http://chirls.com
Description: Video Archive for RAW/WAR
Author: Brian Chirls
Version: 0.1
Author URI: http://chirls.com
*/

global $rw_plugin_path;
global $rw_plugin_url;
//global $relative_plugin_path;

$relative_plugin_path = str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
$rw_plugin_path = ABSPATH . 'wp-content/plugins/' . $relative_plugin_path;
$rw_plugin_url = trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. $relative_plugin_path;
 
require_once($rw_plugin_path . 'video-archive.php');
require_once($rw_plugin_path . 'recaptchalib.php');
require_once($rw_plugin_path . 'template-functions.php');

global $va;

if (!function_exists('rw_init')) {

	//todo: put all these utility functions somewhere safe
	function clean_db_string($string) {
		return clean_db($string,true);
	}
	
	function clean_db($string, $force_string = false) {
		$string = trim($string);
		if ($string == '' || $string == null) {
			$string = 'null';
		} else if (!is_numeric($string) || $force_string) {
			$string = "'" . mysql_real_escape_string($string) . "'";
		}
		return $string;
	}
	
	function make_number($str) {
		if (is_numeric($str)) {
			return (double)$str;
		} else {
			return $str;
		}
	}

	function rw_init() {
		global $va;
		$va = new VideoArchive; //TODO: maybe we just create this outside of any functions to make sure it runs as early as possible?
		$va->init();

/*
		add_filter('redirect_canonical',array($oblog,'filter_redirect_canonical'),10,2);
		add_filter('page_rewrite_rules', array($oblog,'filter_page_rewrite_rules'),1);
		add_filter('post_rewrite_rules', array($oblog,'filter_post_rewrite_rules'),1);
		add_filter('parse_request', array($oblog,'filter_parse_request'));
		add_filter('query_vars', array($oblog,'filter_query_vars'));
  		add_filter('post_link', array($oblog,'filter_post_link'),10,2);
		add_filter('rewrite_rules_array', array($oblog,'filter_rewrite_rules_array'));
*/


		//this runs every two minutes, which sounds like a lot, but every feed and trigger runs on its own schedule, so this won't do anything more than two queries most of the time
	/*
		if ( !wp_next_scheduled('oblog_cron_update_feeds') ) {
			wp_schedule_event(time(), 'two minutes', 'oblog_cron_update_feeds');
		}
	*/
	}
	
	add_action('init', 'rw_init');
	//register_activation_hook( __FILE__, 'rw_install' );
}

if (!function_exists('secondsToTimeString')) {
	function secondsToTimeString($seconds) {
		$minutes = floor($seconds / 60);
		$s = round($seconds) % 60;
		if ($s < 10) {
			$s = '0' . $s;
		}
		return $minutes . ':' . $s;
	}
}

/*
if (!function_exists('oblog_cron_update_feeds')) {
	function oblog_cron_update_feeds() {
		global $oblog;
		global $ofeed;
		
		if (!$oblog || !$ofeed) {
			return;
		}
		
		$ofeed->update_all_by_time();
	}
}
*/
?>