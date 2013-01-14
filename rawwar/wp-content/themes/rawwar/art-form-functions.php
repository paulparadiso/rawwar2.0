<?php

function make_post(){
	
	$themes_present = array();
	if($_POST['theme-con'] == 1){
		array_push($themes_present, 3);	
	};
	if($_POST['theme-body'] == 1){
		array_push($themes_present, 4);	
	};
	if($_POST['theme-media'] == 1){
		array_push($themes_present, 5);	
	};
	if($_POST['theme-act'] == 1){
		array_push($themes_present, 6);	
	};
	if($_POST['theme-id'] == 1){
		array_push($themes_present, 7);	
	};
	
	$artist_name = fix_capitals($_POST['artistname']);
	$artist_id = submit_artist($artist_name);
	
	$title = fix_capitals($_POST['post-title']);	
	
	$already_have = check_for_artwork($artist_id, $title);
	if($already_have != 0){
		return 0;	
	} else {	
		$new_post = array(
			'post_title' => $artist_name . " - " . $title . " - " . $_POST['year'],
			'post_status' => 'pending',
			'post_content' => $_POST['description'],
			'post_author' => 0,
			'post_category' => $themes_present,
			'tags_input' => $_POST['tags']
		);
		$uploader_id = submit_uploader();
		$artwork_id = submit_artwork($uploader_id, $artist_id);
		$post_id = wp_insert_post($new_post);
		add_post_meta($post_id, 'artwork_id', $artwork_id);	
		//insert_video($post_id);
		return 1;	
	}
}
function fix_capitals($input){
	$pieces = explode(" ",$input);
	if(count($pieces) > 1){
		foreach($pieces as &$value){
			$value = strtolower($value);
			$value = ucfirst($value);	
		}	
		unset($value);
		return implode(" ",$pieces);
	} else {
		$title = $input;
		$title = strtolower($title);
		$title = ucfirst($title);	
		return $title;
	}
}

function check_for_artwork($artist_id, $title){
	global $wpdb;
	$wpdb->show_errors();
	$title = fix_capitals($_POST['post-title']);
	
	$query = 'SELECT * FROM artworks WHERE title = "' . $title . '" AND artist = "' . $artist_id . '" LIMIT 0,1';
	$results = $wpdb->get_results($query);
	if($results){
		return $results[0]->id;	
	} else { 
		return 0;
	}
}

function submit_artist($name){
	global $wpdb;
	$query = 'SELECT * FROM artists WHERE name = "' . $name . '" LIMIT 0,1';
	$results = $wpdb->get_results($query);
	if($results){
		return $results[0]->id;	
	} else { 
		$artist = array(
			'name' => $name
		);
		$result = $wpdb->insert("artists",$artist);
		if($result){
			return $wpdb->insert_id;	
		}
	}
	return 0;
}

function submit_uploader(){
	global $wpdb;
	$wpdb->show_errors();
	$query = 'SELECT * FROM uploaders WHERE email = "' . $_POST['email'] . '" LIMIT 0,1';
	$results = $wpdb->get_results($query);
	if($results){
		return $results[0]->id;	
	} else { 
		$uploader = array(
			'firstname' => $_POST['firstname'],
			'email' => $_POST['email'],
			'geo_city' => $_POST['geotag_city'],
			'geo_region' => $_POST['geotag_region'],
			'geo_country' => $_POST['geotag_country'],
			'geo_country_code' => $_POST['geotag_country_code'],
			'geo_latitude' => (float) $_POST['geotag_latitude'],
			'geo_longitude' => (float) $_POST['geotag_longitude']			
		);
		$result = $wpdb->insert("uploaders",$uploader,array('%s','%s','%s','%s','%s','%s','%f','%f'));
		if($result){
			return $wpdb->insert_id;	
		}
	}
	return 0;	 
}

function submit_artwork($uploader_id, $artist_id){
	global $wpdb;
	$wpdb->show_errors();
	$title = fix_capitals($_POST['post-title']);
	
	$query = 'SELECT * FROM artworks WHERE title = "' . $title . '" AND artist = "' . $artist_id . '" LIMIT 0,1';
	$results = $wpdb->get_results($query);
	if($results){
		return $results[0]->id;	
	} else { 
		$artwork = array(
			'type' => $_POST['artwork-type'],
			'url' => $_POST['url'],
			'title' => $title,
			'work_date' => $_POST['year'],
			'artist' => (int) $artist_id,
			'uploader' => (int) $uploader_id		
		);
		$result = $wpdb->insert("artworks",$artwork,array('%s','%s','%s','%s','%d','%d'));
		if($result){
			return $wpdb->insert_id;	
		}
	}
	return 0;	 
}

function insert_artwork($post_id){
	$yt_regex = "/^(http:\/\/)?(www.)?(youtube\.com\/((watch(\?|#!)v=|v\/)|watch_videos\?([a-zA-Z_\-]+=[^&]*&)*video_ids=)|youtu\.be\/)([A-Za-z0-9_\-]+)/"; //id=8
	if(!preg_match($this->yt_regex,$_POST['url'],$matches)){
		$error_msg = 'invalid youtube';
	} else {
		$video_id = $matches[8];
		$source = 'youtube.com';	
	}
}

function check_form(){
	if (array_key_exists('referredby', $_POST)) {			
		return true;
	} else {
		return false;	
	}
}

function get_video_data($video_id){
}
?>

