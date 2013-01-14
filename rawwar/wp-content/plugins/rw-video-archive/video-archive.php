<?php
//todo: get rid of 'approved' everywhere
//todo: only require captcha for account creation, clip creation?
//todo: track IP addresses for all post edits

error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('SCRIPT_DEBUG', true);
define('RW_ANONYMOUS_AUTHOR', 3);
class VideoArchive {
	function init() {
	
		$this->initialize_custom_types();
		
		if (is_admin()) {
			require_once('rw-admin.php');
			$this->admin = new VideoArchiveAdmin($this);
			add_action('admin_init',array($this->admin,'admin_init'));
		}


		//AJAX action for validating YouTube URL in Add Post form. requires log-in
		add_action('wp_ajax_validate_video_url', array($this,'callback_validate_video_url'));

		//add_action('wp_ajax_add_video_action', array($this,'callback_add_video_action'));
		//add_action('wp_ajax_nopriv_add_video_action', array($this,'callback_add_video_action'));
		
		add_action('wp_ajax_save_video_action', array($this,'callback_save_video_action'));
		add_action('wp_ajax_nopriv_save_video_action', array($this,'callback_save_video_action'));
		
		//add_action('wp_insert_post',array($this,'callback_insert_video'));

		add_action('wp_ajax_save_clip_action', array($this,'callback_save_clip_action'));
		add_action('wp_ajax_nopriv_save_clip_action', array($this,'callback_save_clip_action'));

		//todo: only do this on pages that need it
		add_action('wp_print_styles', array($this,'add_video_css_header') );
		add_action('wp_print_scripts', array($this,'add_video_js_header') );
		
//		add_action('wp_insert_post', array($this,'action_wp_insert_post'), 10, 2 ); //debug
		add_filter('wp_insert_post_data', array($this,'filter_wp_insert_post_data'), 10, 2 );
	
		//for selecting posts from database
		add_filter('posts_join', array($this,'filter_posts_join') );
		add_filter('posts_fields', array($this,'filter_posts_fields') );
		//add_filter('posts_groupby', array($this,'filter_posts_groupby') );
		add_action('the_post',array($this,'action_the_post') );
		add_filter('posts_request', array($this,'filter_posts_request') ); //debug
		add_action('pre_get_posts', array($this,'action_pre_get_posts') ); //debug
		add_filter('the_content', array($this,'filter_the_content') );

		//revision stuff
		/*
		Can't figure any way to make revisions save custom fields, so leave these out for now
		add_filter('_wp_post_revision_fields', array($this,'filter_wp_post_revision_fields') );
		add_filter('_wp_post_revision_field_work_date', array($this,'filter__wp_post_revision_field_work_date'), 10, 2);
		*/
		
		//temporary stuff from the old way. todo: delete?
		//add_filter('posts_orderby', array($this,'filter_posts_orderby') );
		 //for columns in edit posts view. temporary
		//add_action('rw_render_add_video_form', array($this,'render_add_video_form') );
		add_filter('the_author_posts_link', array($this,'filter_the_author_posts_link') );

		//todo: change to a different set of keys that is not globally embeddable		
		$this->captcha_publickey = "6Lf5VgwAAAAAACNMsdKeXrg2ArSepTsIz77BJf5I";
		$this->captcha_privatekey = "6Lf5VgwAAAAAAH35xVStZTumNJbMUDD_4_2o97Ag";
		$this->captcha_uses_left = false;

		//set up alternate view for embeddable iframe
		global $wp_rewrite;
		add_feed('embed',array($this,'render_embed_view'));
		
		//todo: spawn this off
		add_action('shutdown',array($this,'update_video_cache') );
		//$this->update_video_cache();
		
		$this->video_data_mem_cache = array();
		
		$this->yt_regex = "/^(http:\/\/)?(www.)?(youtube\.com\/((watch(\?|#!)v=|v\/)|watch_videos\?([a-zA-Z_\-]+=[^&]*&)*video_ids=)|youtu\.be\/)([A-Za-z0-9_\-]+)/"; //id=8

	}
	
	function __destruct() {
//		$this->update_video_cache();
	}
	
	function initialize_custom_types() {
		//register custom post types
		register_post_type('video', array(
			'label' => __('Videos'),
			'singular_label' => __('Video'),
			'labels' => array(
				'name' => 'Videos',
				'singular_name' => 'Video',
				'add_new' => __('Add New'),
				'add_new_item' => __('Add New Video'),
				'edit_item' => __('Edit Video'),
				'new_item' => __('New Video'),
				'view_item' => __('View Video'),
				'search_items' => __('Search Videos'),
				'not_found' =>  __('No videos found'),
				'not_found_in_trash' => __('No videos found in Trash'), 
			),
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
//			'_builtin' => false,
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => array('slug' => 'video'),
			'query_var' => false,
			'supports' => array('title', 'editor', 'revisions'),
			'taxonomies' => array('post_tag','category')
		));

		register_taxonomy('theme',array('video','post'), array(
			'hierarchical' => true,
			'labels' => array(
				'name' => 'Themes',
				'singular_name' => 'Theme',
				'search_items' =>  __( 'Search Themes' ),
				'all_items' => __( 'All Themes' ),
				'edit_item' => __( 'Edit Theme' ), 
				'update_item' => __( 'Update Theme' ),
				'add_new_item' => __( 'Add New Theme' ),
				'new_item_name' => __( 'New Theme Name' ),
				'separate_items_with_commas' => __( 'Separate themes with commas' ),
				'add_or_remove_items' => __( 'Add or remove themes' ),
				'choose_from_most_used' => __( 'Choose from the most used themes' )
			),
			'show_ui' => true,
			'show_tagcloud' => false,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'theme' )
		));

		register_taxonomy('people',array('video','post','clip'), array(
			'hierarchical' => false,
			'labels' => array(
				'name' => 'People',
				'singular_name' => 'Person',
				'search_items' =>  __( 'Search People' ),
				'all_items' => __( 'All People' ),
				'edit_item' => __( 'Edit Person' ), 
				'update_item' => __( 'Update Person' ),
				'add_new_item' => __( 'Add New Person' ),
				'new_item_name' => __( 'New Person Name' ),
				'separate_items_with_commas' => __( 'Separate people with commas' ),
				'add_or_remove_items' => __( 'Add or remove people' ),
				'choose_from_most_used' => __( 'Choose from the most used people' )
			),
			'show_ui' => true,
			'show_tagcloud' => false,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'person' )
		));

		register_post_type('clip', array(
			'label' => __('Clips'),
			'singular_label' => __('Clip'),
			'labels' => array(
				'name' => 'Clips',
				'singular_name' => 'Clip',
				'add_new' => __('Add New'),
				'add_new_item' => __('Add New Clip'),
				'edit_item' => __('Edit Clip'),
				'new_item' => __('New Clip'),
				'view_item' => __('View Clip'),
				'search_items' => __('Search Clips'),
				'not_found' =>  __('No clips found'),
				'not_found_in_trash' => __('No clips found in Trash'), 
			),
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
//			'_builtin' => false,
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => array('slug' => 'clip'),
			'query_var' => false,
			'supports' => array('title', 'editor'),
			'taxonomies' => array('post_tag')
		));


	}
	
	function filter_the_content($content) {
		global $post;

		$content = rw_linkify($content);
		if ($post->video && is_single()) {
			//todo: make it work for other video services
			$content .= '<p><a href="http://youtube.com/watch?v=' . $post->video->foreign_key . '">View full video on YouTube</a></p>';
		}
		return $content;
	}

	function filter_posts_groupby($groupby) {
//		echo "groupby: $groupby\n";
		return $groupby;
	}

	function allow_edit($post_id) {
		if (($current_user = wp_get_current_user()) && $current_user->ID) {
			return current_user_can('edit_post', $post_id);
		} else {
			global $wpdb;
			$user_id = $this->get_user_account();
			$sql = "SELECT resources.*
			FROM resources
			INNER JOIN {$wpdb->posts} AS posts ON resources.key_id = posts.ID AND resources.resource_type IN ('post','video','clip')
			WHERE resources.key_id = " . clean_db($post_id);
			$posts = $wpdb->get_results($sql);
			if (count($posts) && $posts[0]->user_id == $user_id) {
				return true;
			} else {
				return false;
			}
		}
	}

	function get_the_player($opts = array()) {
		//todo: move this all to a new template function
		//so it can be thumbnail for search/browse listing and player for single
		global $wpdb;
		//global $post;

		$opts = array_merge(array(
			'fullscreen' => true,
			'edit_clip' => true,
			'share_clip' => true,
			'show_title' => false,
			'post' => $GLOBALS['post']
		), $opts);

		if (is_numeric($opts['post'])) {
			$post = get_post($opts['post']);
		} else {
			$post = $opts['post'];
		}

		$permalink = get_permalink($post->ID);

		if ($post && $post->video) {

			if ($opts['show_title']) {
				$video_title = '<div class="video_title"><a href="' . $permalink . '" rel="bookmark" title="Permanent Link to ' . the_title_attribute(array('echo' => false)) . '" target="_top">' . get_the_title($post->ID) . '</a></div>';
			}

			if ($opts['width'] && is_numeric($opts['width'])) {
				$options_json = ', ' . json_encode(array(
					width => intval($opts['width'])
				));
			}

			$video_json = json_encode($post->video);
			if ($opts['edit_clip']) {
				$clip_times = <<<EOT
				<div class="clip_times">
					<h4>Clip Times</h4>
					<label>From</label>
					<input type="text" class="clip_start_input"/>
					<label>To</label>
					<input type="text" class="clip_end_input"/>
				</div>
EOT;

				if ('mini' === $opts['edit_clip']) {
					$edit_clip_form = '<div class="edit_clip_form edit_clip_form_mini" style="display: none">' . $clip_times . '</div>';
				} else {
					$editclip_button = '<div class="editclip_button func_button">Edit Clip</div>';

					//require_once(ABSPATH . 'wp-admin/includes/template.php');
					//$categories = get_terms_checklist();

					$save_buttons = '<input type="button" value="Save as New Clip" class="clip_save_new_button"/>';
					if ($post->video && $post->video->clip_id &&
						$post->video->video_id != $post->post_id &&
						$this->allow_edit($post->ID)) {
						$button_text = "Save Clip";
						$save_buttons = "<input type=\"button\" value=\"$button_text\" class=\"clip_save_button\"/> $save_buttons";

					} else {
						//$button_text = "Save Video";
					}
			
					$email_form = '';
					if (!is_user_logged_in()) {
						$email_form = '<label>Your Name</label>
				<input type="text" class="clip_name"/>
				<div class="clip_name_error">&nbsp;</div>				
				
				<label>Email Address</label>
				<input type="text" class="clip_email"/>
				<div class="clip_email_error">&nbsp;</div>
						';
					}
					$captcha_form = '';
					if ($this->check_captcha()) {
						$captcha_form = recaptcha_get_html($this->captcha_publickey, '') . '<br/><div id="video_captcha_error">&nbsp;</div>';
					}

					$edit_clip_form = <<<EOT
			<div class="edit_clip_form clip_panel" style="display: none">
				<h3>Create Clip</h3>
				$clip_times
		
				<!-- <fieldset> -->
					<label>Title</label>
					<input type="text" class="clip_title"/>
					<div class="clip_title_error">&nbsp;</div>
				<!-- </fieldset> -->

				<!-- <fieldset> -->
					<label>Description</label>
					<textarea class="clip_description"></textarea>
					<div class="clip_description_error">&nbsp;</div>
				<!-- </fieldset> -->

				<label>Video Participants</label>
				<input type="text" class="clip_people"/>
				<div class="clip_people_error">&nbsp;</div>
				
				<!-- <fieldset> -->
					<label>Keywords (comma-separated)</label>
					<input type="text" class="clip_keywords"/>
				<!-- </fieldset> -->
<!--
				<label>Categories</label>
				<ul class="clip_categories">
$categories
				</ul>
-->
				$email_form
				<div style="text-align: right">
				<div class="clip_error">&nbsp;</div>
				$save_buttons
				</div>
			</div>
EOT;
				}
			}

			//todo: change content to excerpt
			$clean_content = rawurlencode($post->post_content);
			$clean_link = rawurlencode($permalink);
			$clean_title = rawurlencode($post->post_title);

			$email_link = "mailto:?subject=" . $clean_title . '&body=' . $clean_link . urlencode("\n\n") . $clean_content;

			$twitter_link = "http://twitter.com/home?status=" . $clean_title . ' http://rawwar.org/?p=' . $post->ID;
			
			$facebook_link = 'http://www.facebook.com/share.php?u=' . $clean_link;

			$tumblr_link = 'http://www.tumblr.com/share?v=3&u=' . $clean_link .
				'&t=' . $clean_title .
				'&s=' . $clean_content;
				
			$embed_code = '<iframe src="' . htmlentities($permalink) . '?feed=embed" width="500" height="500" style="border: none"></iframe>';

			if ($opts['share_clip']) {
				$share_button = '<div class="share_button func_button">Share</div>';
				$share_clip_form = <<<EOT
			<div class="share_clip_form clip_panel">
				<h3>Share Clip</h3>
				<div class="share_element">
					<a href="$email_link"><div class="share_icon" style="background-position: 0 0"></div></a>
					<label><a href="$email_link">Email</a></label>
				</div>
				<div class="share_element">
					<a href="$facebook_link" target="_new"><div class="share_icon" style="background-position: -35px 0"></div></a>
					<label><a href="$facebook_link" target="_new">Facebook</a></label>
				</div>
				<div class="share_element">
					<a href="$twitter_link" target="_new"><div class="share_icon" style="background-position: -70px 0"></div></a>
					<label><a href="$twitter_link" target="_new">Twitter</a></label>
				</div>
				<div class="share_element">
					<a href="$tumblr_link" target="_new"><div class="share_icon" style="background-position: -105px 0"></div></a>
					<label><a href="$tumblr_link" target="_new">Tumblr</a></label>
				</div>
				<div class="share_element">
					<textarea>$embed_code</textarea>
					<label>Embed</label>
				</div>
			</div>
EOT;
			}

			if ($opts['fullscreen']) {
				$fullscreen_button = '<div class="fullscreen_button func_button">Full Screen</div>';
			}
			$new_content = <<<EOT
		<div class="player_container" id="player_container_{$post->ID}">
			<div class="video_container paused">
				$video_title
				<div class="video" style="overflow-y: hidden" id="ytapiplayer-{$post->ID}"></div>
				<div class="play_bar">
					<div class="play-outside"></div> 
					<div class="play-inside"></div> 
					<div class="play-outside"></div> 
					<div class="play_marker_range"> 
						<div class="play_marker"><div></div></div> 
						<div class="in_marker"><div></div></div> 
						<div class="out_marker"><div></div></div> 
					</div> 
				</div> 
				<div class="control_bar">
					<div class="play_button"></div>
					<!-- <div class="start_button"></div> -->
					<!-- <div class="end_button"></div> -->
					<div class="video_time">0:00 / 0:00</div> 
					<div class="mute_button"></div>
					<div class="volume_slider">
						<span class="volume_slider_handle"></span>
					</div>
					$share_button
					$editclip_button
					$fullscreen_button
				</div>
				$edit_clip_form
				$share_clip_form
			</div>
		</div>
		 <script type="text/javascript"> jQuery(document).ready(function($) {window.rwPlayers[{$post->ID}] = new RwPlayer({$post->ID},$video_json $options_json);});
		 </script>
EOT;
			//$content = $new_content.$content;
			return $new_content;
			//$content .= "<!--<pre>" . print_r($post->video,true) . "</pre>-->";
		}
	}
	
	function action_the_post($post) {
		global $wpdb, $authordata;
		//echo "action_the_post\n";
		$do_user = false;

		switch ($post->post_type) {
			case 'video':
			case 'clip':
				$do_user = true;

				$video = $this->get_video_data($post->ID);
				if ($video) {					
					//clean up video object
					$fields = array_keys(get_object_vars($video));
					foreach ($fields as $field) {
						$str = $video->$field;
						if (is_numeric($str)) {
							$video->$field = (double)$str;
						}
					}
					
					$video->allow_edit = $this->allow_edit($post->ID);
					
					/*
					edit_time is UNIX_TIMESTAMP('{$post->post_modified}')
					if ($video->allow_edit && time() - $video->edit_time < 60 * 60)  {
						unset($video->original_start);
						unset($video->original_end);
					}
					*/

					if ($this->allow_edit($post->ID)) {
						$video->clip_description = $post->post_content;
						$video->clip_title = $post->post_title;
					}

					$post->video = $video;
				}
			case 'post':
				$do_user = true;
		}

		if ($do_user && $post->rw_user_id) {
			$sql = "SELECT * FROM users WHERE user_id = {$post->rw_user_id}";
			$user = $wpdb->get_results($sql);
			if ((is_null($user->wp_user_id) || !$user->wp_user_id) && $user[0]->user_name && is_object($authordata)) {
				$authordata->display_name = $user[0]->user_name;
			}
			if (is_admin()) { //todo: further restrict this based on user level
				$post->rwUser = $user[0];
			}
		}
	}
	
	function filter_the_author_posts_link($link) {
		global $post;
		if ($post->post_author == RW_ANONYMOUS_AUTHOR) {
			return get_the_author();
		} else {
			return $link;
		}
	}
	
	function filter_posts_request($request) {
		//todo: for debugging purposes only, so remove this
		//echo "request:$request\n";
		return $request;
	}
	
	function action_pre_get_posts($query) {
		//todo: for debugging purposes only, so remove this
		//echo "query:\n"; print_r($query); die;

		if (!$query->query_vars['post_type'] && !is_page()) {
			$query->query_vars['post_type'] = array(
				'video','clip','post'
			);
		}

		//return $query;
	}
	
	
	function filter_posts_join($join) {
		global $wpdb;
		$join .= "LEFT JOIN resources ON resources.key_id = {$wpdb->posts}.ID AND resources.resource_type IN ('video','clip','post')\n";

		//temporary?
		$join .= "LEFT JOIN users ON resources.user_id = users.user_id\n";

		return $join;
	}
	
	function filter_posts_fields($fields) {
		global $wpdb;
		$fields .= ",resources.resource_type, resources.user_id as rw_user_id, resources.approved as rw_approved";
		return $fields;
	}
	
	function update_video_cache() {
		global $wpdb;
		$sql = "SELECT video_xml_cache.foreign_key, TIMESTAMPDIFF(HOUR,video_xml_cache.update_time,NOW()) as cache_age
			FROM videos
			INNER JOIN video_xml_cache ON videos.source = video_xml_cache.source AND videos.foreign_key = video_xml_cache.foreign_key
			INNER JOIN {$wpdb->posts} AS posts ON posts.ID=videos.post_id AND posts.post_status = 'publish'
			WHERE TIMESTAMPDIFF(HOUR,video_xml_cache.update_time,NOW()) > 24 OR TIMESTAMPDIFF(HOUR,video_xml_cache.update_time,NOW()) > 0 AND video_xml_cache.view_count = 0
			ORDER BY cache_age DESC
			LIMIT 0,1";
//echo "$sql\n";
		$videos = $wpdb->get_results($sql);
//print_r($videos);
		if (count($videos)) {
			$this->update_video_data($videos[0]->foreign_key);
		}
	}
	
	function update_video_data($video_id, &$error = null) {
		//todo: save if existing video is no longer accessible and adjust score
	
		global $wpdb;
		//extract video data from xml and save it
		$video = array();
		$source = 'youtube.com';
		$video_xml_url = "http://gdata.youtube.com/feeds/api/videos/$video_id?v=2";
		$num_thumbs = 0;
		$cache_age = 99;

		if (!$video_id) {
			error_log("null video id!\n" . print_r(debug_backtrace(true),true));
			return false;
		}

		$sql = "SELECT video_xml_cache.cache_id, video_xml_cache.update_time, video_xml_cache.xml,
				TIMESTAMPDIFF(HOUR,video_xml_cache.update_time,NOW()) as cache_age,
				count(thumbnails.thumbnail_id) as num_thumbs
			FROM video_xml_cache
				LEFT JOIN videos ON videos.source = video_xml_cache.source AND videos.foreign_key = video_xml_cache.foreign_key
				LEFT JOIN thumbnails ON thumbnails.cache_id = video_xml_cache.cache_id
			WHERE video_xml_cache.source='$source' AND video_xml_cache.foreign_key = " . clean_db($video_id) . "
			GROUP BY video_xml_cache.update_time, video_xml_cache.xml, cache_age";
		$video_cache = $wpdb->get_results($sql);
		//echo "/* $sql */\n";
		//echo "/* " . print_r($video_cache,true) . " */\n";

		if ($video_cache && $video_cache[0]->cache_age <= 24) {
			$doc = new DOMDocument;
			@$xml_success = $doc->loadXML($video_cache[0]->xml);
			if (!$xml_success) {
				$video_cache = false; //this xml is bad, so retrieve it again
			} else if (!$doc || !$doc->documentElement || $doc->documentElement->nodeName != 'entry') {
				//make sure there's only one "<entry>"
				$video_cache = false; //this xml is bad, so retrieve it again
			}
		}

		if (!$video_cache || $video_cache[0]->cache_age > 24) {
			$request = new WP_Http;
			$result = $request->request( $video_xml_url );
			if (is_array($result) && is_array($result['response']) && $result['response']['code'] == 200) {
				$video['rw_update_time'] = date('c');
				$xml = $result['body'];
				$clean_xml = clean_db($xml);
				
				$sql = "INSERT INTO video_xml_cache (original_url,source,foreign_key,xml)
				VALUES (" . clean_db($video_xml_url) . ',' . clean_db($source) . ',' . clean_db($video_id) . ",$clean_xml) 
				ON DUPLICATE KEY UPDATE cache_id=LAST_INSERT_ID(cache_id), update_time=NOW(), xml=$clean_xml";
//echo "/* $sql */\n";
				$wpdb->query($sql);
				$cache_id = mysql_insert_id();
			} else {
				$error = 'YouTube video not found';
			}
		} else {
			$cache_id = $video_cache[0]->cache_id;
			$cache_age = $video_cache[0]->cache_age;
			$num_thumbs = $video_cache[0]->num_thumbs;
			$xml = $video_cache[0]->xml;
			$video['rw_update_time'] = $video_cache[0]->update_time;
		}
		
		if ($xml && !$error) {
			if (!$xml_success) {
				$doc = new DOMDocument;
				@$xml_success = $doc->loadXML($xml);
				if (!$xml_success) {
					$doc = false;
				}
			}
			if ($doc && $doc->documentElement && $doc->documentElement->nodeName == 'entry') {
				$xpath = new DOMXpath($doc);
				$xpath->registerNamespace('atom','http://www.w3.org/2005/Atom');
				$xpath->registerNamespace('media','http://search.yahoo.com/mrss/');
				$xpath->registerNamespace('gd','http://schemas.google.com/g/2005');
				$xpath->registerNamespace('yt','http://gdata.youtube.com/schemas/2007');
				$entry = $doc->documentElement;

				//first check access control
				$embed_permission = $xpath->evaluate("string(yt:accessControl[@action='embed']/@permission)",$entry);
				$syndicate_permission = $xpath->evaluate("string(yt:accessControl[@action='syndicate']/@permission)",$entry);
				$countries_permission = $xpath->evaluate("string(media:group/media:restriction[@type='country' and @relationship='deny'])",$entry);
				$countries_permission = explode(' ',$countries_permission);
				if ($embed_permission && $embed_permission != 'allowed'
					|| $syndicate_permission && $syndicate_permission != 'allowed'
					|| is_array($countries_permission) && in_array('US',$countries_permission)) {
					$error = 'Embedding is not allowed for this video.';
				} else {
					$video['rw_author_name'] = $xpath->evaluate("string(atom:author/atom:name)",$entry);
					$author_uri = $xpath->evaluate("string(atom:author/atom:uri)",$entry);
					$author_uri = explode('/',$author_uri);
					$video['rw_author_account'] = $author_uri[count($author_uri) - 1];
					$video['rw_location'] = $xpath->evaluate("string(yt:location)",$entry);
					$aspect = $xpath->evaluate("string(media:group/yt:aspectRatio)",$entry);
					$video['rw_aspect_ratio'] = ($aspect == 'widescreen' ? 16/9 : 4/3);
					$video['rw_duration'] = $xpath->evaluate("number(media:group/yt:duration/@seconds)",$entry);
					$video['rw_view_count'] = $xpath->evaluate("number(yt:statistics/@viewCount)",$entry);
					
					/*
					gd:rating
					*/
					$thumbnail_elements = $xpath->query("media:group/media:thumbnail",$entry);
						$thumbnails = array();
					foreach ($thumbnail_elements as $th) {
						$thumb = new StdClass;
						$thumb->thumbnail_url = $th->getAttribute('url');
						$thumb->width = $th->getAttribute('width');
						$thumb->height = $th->getAttribute('height');
						if ($th->hasAttribute('time') && preg_match('/^(\d\d):(\d\d):(\d\d)(.(\d\d\d))?/',$th->getAttribute('time'),$t)) {
							$thumb->time_offset = $t[1] * 3600 + $t[2] * 60 + $t[3] + $t[5] / 1000;
						} else {
							$thumb->time_offset = $video['rw_duration'] / 2.0;
						}
						$thumbnails[] = $thumb;
					}
					$video['rw_thumbnails'] = $thumbnails;

					$video['rw_source'] = $source;
					$video['rw_foreign_key'] = $video_id;
					$video['rw_original_url'] = "http://youtube.com/watch?v=$video_id"; //todo: get this from form

					$video['title'] = $xpath->evaluate("string(atom:title)",$entry);
					$video['description'] = $xpath->evaluate("string(media:group/media:description[@type='plain'])",$entry);
					if (!$video['description']) {
						$video['description'] = $xpath->evaluate("string(media:group/media:description)",$entry);
					}

					//keywords
					//todo: filter out annoyingly useless keywords: the, of, a, an, in, etc.
					$keyword_elements = $xpath->query("atom:category[@scheme='http://gdata.youtube.com/schemas/2007/keywords.cat']/@term",$entry);
					$keywords = array();
					foreach ($keyword_elements as $kw) {
						$keywords[] = $kw->nodeValue;
					}
					$keywords = array_unique($keywords);
					$video['keywords'] = implode(', ',$keywords);

					//update stats
					$video['rw_recorded'] = $xpath->evaluate("string(yt:recorded)",$entry);
					if (!$video['rw_recorded'] || !preg_match('/\d{4}-\d\d?-\d\d?/',$video['rw_recorded'])) {
						$video['rw_recorded'] = null;
					} else {
						//$video['rw_recorded'] = clean_db($video['rw_recorded']);
					}
					
					//todo: normalize rating for other services. this assumes out of 5
					$video['rw_rating'] = $xpath->evaluate("number(gd:rating/@average)",$entry);
					if ($video['rw_rating'] && is_numeric($video['rw_rating'])) {
						$video['rw_rating'] = ($video['rw_rating'] - 1.0) / 4.0;
					} else {
						$video['rw_rating'] = null;
					}
					$video['rw_num_raters'] = $xpath->evaluate("number(gd:rating/@numRaters)",$entry);
					if ($video['rw_num_raters'] && is_numeric($video['rw_num_raters'])) {
					} else {
						$video['rw_num_raters'] = null;
					}
					$video['rw_favorite_count'] = $xpath->evaluate("number(yt:statistics/@favoriteCount)",$entry);
					$video['rw_view_count'] = $xpath->evaluate("number(yt:statistics/@viewCount)",$entry);

					if ($cache_age >= 24) {
						$sql = "UPDATE video_xml_cache SET 
							rating = {$video['rw_rating']},
							num_raters = {$video['rw_num_raters']},
							favorite_count = {$video['rw_favorite_count']},
							view_count = {$video['rw_view_count']},
							recorded_date = " . clean_db($video['rw_recorded']) . ",
							aspect_ratio = {$video['rw_aspect_ratio']},
							duration = " . clean_db($video['rw_duration']) . ",
							video_title = " . clean_db($video['title']) . ",
							video_description = " . clean_db($video['description']) . "
							WHERE cache_id=$cache_id";
//echo "/* $sql */\n";
						$wpdb->query($sql);
					}

					//save thumbnails
					if ($num_thumbs != count($video['rw_thumbnails']) && count($video['rw_thumbnails'])) {
						$wpdb->query("DELETE FROM thumbnails WHERE cache_id = $cache_id");
						foreach ($video['rw_thumbnails'] as $thumb) {
							$thumb = array_map('clean_db',get_object_vars($thumb));
							$sql = "INSERT INTO thumbnails (cache_id, thumbnail_url, width, height, time_offset)
								VALUES ($cache_id, {$thumb['thumbnail_url']}, {$thumb['width']}, {$thumb['height']}, {$thumb['time_offset']})";
							//echo "/* $sql */\n";
							$wpdb->query($sql);
						}
					}
				}
			} else {
				$error = 'YouTube video not found';
			}
		}

		//todo: clean out very old cache entries that are not also posts

		if ($error) {
			return false;
		} else {
			return $video;
		}
	}
	
	function callback_save_clip_action() {
		//print_r($_REQUEST); //die;
//echo "/* callback_save_clip_action...\n"; print_r($_REQUEST); echo "*/\n";

		if (($error = $this->check_captcha($_REQUEST, 1))) {
			$response->error = array(
				'clip_error' => $error
			);
			echo json_encode($response);
			exit;
		}

		global $wpdb;
		
		$error = array();
		
		//todo: validate name and email address

		
		//validate title.  todo: Make sure it's not the same as the original clip
		$title = trim($_REQUEST['title']);
		if (!$title) {
			$error['title'] = 'Please enter a title';
		}

		//validate description.  todo: Make sure it's not the same as the original clip
		$description = trim($_REQUEST['description']);
		if (!$description) {
			$error['description'] = 'Please enter a description';
		}
		
		$video_id = trim($_REQUEST['video_id']);

		$post = array(
			'post_status' => 'publish'
		);

		//get foreign key from database
		$sql = "SELECT videos.source, videos.foreign_key
			FROM videos
			WHERE videos.post_id=" . clean_db($video_id);
		$rows = $wpdb->get_results($sql);
//echo "$sql\n";
//print_r($rows); //die;

		if (count($rows) && $rows[0]->foreign_key) {
			$foreign_key = $rows[0]->foreign_key;
			$old_post = get_post($_REQUEST['post_id'],ARRAY_A);
			if ($old_post) {
				$post = array_merge($old_post, $post);
			}
		} else {
			//return error
			$error['clip_error'] = 'Post not found';
		}

		if (!count($error) && ($video = $this->update_video_data($foreign_key,$video_error))) {
			$post = array_merge($video,$post);
//print_r($post); die;
			$post['post_title'] = $title;
			$post['post_content'] = trim($_REQUEST['description']);
			$keywords = trim($_REQUEST['post_keywords']);
			//$keywords = explode(',',$keywords);
			//$keywords = array_map('trim',$keywords);
			//$post['tags_input'] = array_unique($keywords);
			$post['tags_input'] = $keywords;
			//get a user account
			$post['rw_user_id'] = $this->get_user_account($email,$name); //todo: user_name
	
			if (!is_user_logged_in()) {
				$post['post_author'] = RW_ANONYMOUS_AUTHOR; //if not logged in, set post_author to Anonymous

				if (!$post['post_name']) {
					//if this is not a logged-in user, just create a short slug
					$index = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_";
					$base = strlen($index);
					$log_divisor = log10($base);
					$out = "";
					$num = $redirect_id;
					for ( $t = floor( log10( $num ) / $log_divisor ); $t >= 0; $t-- ) {
						$a = floor( $num / pow( $base, $t ) );
						$out = $out . $index[$a];
						$num = $num - ( $a * pow( $base, $t ) );
					}
	
					$post['post_name'] = $out;
				}
			}
	
			$post['ID'] = $_REQUEST['post_id'];
			$post['rw_video_id'] = $video_id;

			//save clip data
			$post['start_offset'] = $_REQUEST['clip_start'];
			$post['end_offset'] = $_REQUEST['clip_end'];

//echo "/* inserting post...\n"; print_r($post); echo "*/\n";
			$post_id = wp_insert_post($post);
			if ($post_id) {
//echo "/* inserted post ID=$post_id */\n";
				$this->rw_insert_video_post($post_id,$post);
				
				$response->post_id = $post_id;
				$response->post_url = get_permalink( $post_id );
			} else {
				//todo: error
				$error['clip_error'] = 'Unable to create post';
				//echo "no post!\n";
			}
		}
		if ($video_error) {
			$error['clip_error'] = $video_error;
		}

		$response->error = $error;
		echo json_encode($response);
		exit;
	}

	function callback_save_video_action($post_ID, $post) {
		//todo: allow edit if post_id is provided. check security
		
		//if (($error = $this->check_captcha($_REQUEST, 1))) {
		//	$response->error = $error;
		//	echo json_encode($response);
		//	exit;
		//}

		global $wpdb; //get the database handle
		print_r($post_ID); //print the id of the post which triggered this call
		$new_post = get_post($post_ID);//retrieve the post				
		print_r($_REQUEST);//print the form data
		print_t($new_post);//print the post data

		//todo: clean up input in case of escaped characters or whatever
		$video_id = trim($_REQUEST['video_id']);//foriegn key used for video in db
		$email = trim($_REQUEST['video_email']);
		$name = trim($_REQUEST['video_name']);
		
		$is_clip = $_REQUEST['clip'] == 'true' || $_REQUEST['clip'] == '1';

		if (!$error && ($video = $this->update_video_data($video_id,$error))) {
			//todo: allow edit if post_id is provided. check security
			$sql = "SELECT posts.ID
				FROM videos
				INNER JOIN {$wpdb->posts} AS posts ON posts.ID = videos.post_id AND posts.post_status IN ('published','draft','private')
				WHERE videos.foreign_key = " . clean_db($video_id);
			//todo: allow edit if post_id is provided. check security
			$rows = $wpdb->get_results($sql);
			if ($is_clip) {
				if (!count($rows)) {
					//todo: Generate main clip as a post? or return an error?
					$error = 'Not ready yet';
				}
				//todo: additional validation of title, description
			} else if (count($rows)) { //todo: allow for editing
				$error = 'This video has <a href="' . get_permalink($rows[0]->ID) . '">already been posted</a>';
			}
		}

		//validate email
		$email_regex = '/^[\w-][\+\w-]*(\.[\+\w-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)*?\.[a-z]{2,6}|(\d{1,3}\.){3}\d{1,3})(:\d{4})?$/';
		if ($email && !preg_match($email_regex,$email,$matches)) {
			$error = 'Not a valid email address';
		} else if (!$name && !is_user_logged_in()) {
			$error = 'Please enter your name';
		}

		$title = trim($_REQUEST['title']);
		
		//validate title
		if (!$title) {
			$error = 'Please enter a title';
		}
		
		$post = array(
			'post_status' => 'publish'
		);

		if (!$error) {
			$post = array_merge($video,$post);

			$post['post_title'] = $title;
			$post['post_content'] = trim($_REQUEST['description']);
			$keywords = trim($_REQUEST['post_keywords']);
			$cat = trim($_REQUEST['video_cat']);
			if (is_numeric($cat)) {
				$post['post_category'] = array($cat);
			}
			//$keywords = explode(',',$keywords);
			//$keywords = array_map('trim',$keywords);
			//$post['tags_input'] = array_unique($keywords);
			$post['tags_input'] = $keywords;
			//get a user account
			$post['rw_user_id'] = $this->get_user_account($email,$name); //todo: user_name
	
			if (!is_user_logged_in()) {
				$post['post_author'] = RW_ANONYMOUS_AUTHOR; //if not logged in, set post_author to Anonymous
			}
	
			//$this->rw_insert_video_post(23, $post);
			$post_id = wp_insert_post($post);
			$post['rw_video_id'] = $post_id;
			$this->rw_insert_video_post($post_id,$post);
			
			$response->post_id = $post_id;
			$response->post_url = get_permalink( $post_id );
		}


		$response->error = $error;
		echo json_encode($response);
		exit;
	}

	function callback_insert_video($post_ID){	
		
		global $wpdb;		
		
		$fields = array(
			'post_id' => $post_ID,
			'source' => null,
			'original_url' => null,
			'update_time' => null,
			'aspect_ratio' => null,
			'work_date' => null,
			'duration' => null,
			'foriegn_key' => null
		);	
		
		$fields_meta = array(
			'post_id' => (int) $post_ID,			
			'firstname' => $_REQUEST['firstname'],
			'email' => $_REQUEST['email'],
			'geo_city' => $_REQUEST['geotag_city'],
			'geo_region' => $_REQUEST['geotag_region'],
			'geo_country' => $_REQUEST['geotag_country'],
			'geo_country_code' => $_REQUEST['geotag_country_code'],
			'geo_latitude' => (float) $_REQUEST['geotag_latitude'],
			'geo_longitude' => (float) $_REQUEST['geotag_longitude']
		);
		
		//foreach ($fields_meta as $key => $val) {
		//	$rw_key = $key;
		//	if (array_key_exists($rw_key,$_REQUEST)) {
		//		$clean_val = clean_db($_REQUEST[$rw_key]);
		//		$fields_meta[$key] = $clean_val;
		//		$update[] = "$key = $clean_val";
		//	}
		//}	
		
				
		
		//$sql = "INSERT INTO videos_meta (post_id," . implode(',',array_keys($fields_meta)) . ")
		//	VALUES (" . clean_db($post_ID) . "," . implode(',',array_values($fields_meta)) . ")";	
		
		//$result = $wpdb->query($sql);	
		$wpdb->show_errors();		
		$wpdb->insert('videos_meta',$fields_meta,	array('%d','%s','%s','%s','%s','%s','%s','%f','%f'));	
		print_r($fields_meta);
		echo "<br>";		
		print_r($sql);		
		echo "<br>" ;
		print_r($_REQUEST);
		exit;
	}
	
	function rw_insert_video_post($post_id, $post) {
		global $wpdb;
//echo "clip fields\n";
		
		if (!is_array($post) && is_object($post)) {
			$post = get_object_vars($post);
		}

		//check $post for user_id
		if (!isset($post['rw_user_id']) || !$post['rw_user_id']) {
			return;
		}
	
		//save video
		$fields = array(
			'source' => null,
			'foreign_key' => null,
			'update_time' => null,
			'original_url' => null,
//			'author_name' => null,
//			'author_account' => null,
//			'location' => null,
			'aspect_ratio' => null,
			'duration' => null,
//			'view_count' => null,
//			'user_id' => null,
		);

		//save clip. todo: move this below
		$clip_fields = array(
			'start_offset' => $post['start_offset'] || 0,
			'end_offset' => $fields['duration']
		);
		
		//if this is a clip edit
		if ($post['rw_video_id'] != $post_id) {
			$resource_type = 'clip';
			if (isset($post['end_offset']) && isset($post['start_offset'])) {
				$clip_fields['start_offset'] = $post['start_offset'];
				$clip_fields['end_offset'] = $post['end_offset'];
			}
		} else {
			$resource_type = 'video';
		}
			
//echo "clip fields\n";
//print_r($clip_fields);

		$update = array();
		foreach ($fields as $key => $val) {
			$rw_key = 'rw_' . $key;
			if (array_key_exists($rw_key,$post)) {
				$clean_val = clean_db($post[$rw_key]);
				$fields[$key] = $clean_val;
				$update[] = "$key = $clean_val";
			}
		}

		//todo: double-check that fields and update are not empty?
		$sql = "INSERT INTO videos (post_id," . implode(',',array_keys($fields)) . ")
			VALUES (" . clean_db($post['rw_video_id']) . "," . implode(',',array_values($fields)) . ")
			ON DUPLICATE KEY UPDATE post_id=LAST_INSERT_ID(post_id)," . implode(', ',$update);
		//echo "/* $sql */\n"; //die;
		$wpdb->query($sql); //todo: error check
		//echo "//" . mysql_info() . "\n";

		//save clip data
		$clip_update = array();
		foreach ($clip_fields as $key => $val) {
			$clean_val = clean_db($val);
			$clip_fields[$key] = $clean_val;
			$clip_update[] = "$key = $clean_val";
		}
		$clip_fields['original_start'] = $clip_fields['start_offset'];
		$clip_fields['original_end'] = $clip_fields['end_offset'];
		$clip_fields['original_post_id'] = clean_db($post['rw_video_id']);

		$sql = "INSERT INTO clips (post_id," . implode(',',array_keys($clip_fields)) . ")
			VALUES (" . clean_db($post_id) . "," . implode(',',array_values($clip_fields)) . ")
			ON DUPLICATE KEY UPDATE post_id=LAST_INSERT_ID(post_id)," . implode(', ',$clip_update);
		//echo "/* $sql */\n"; //die;
		$wpdb->query($sql); //todo: error check
		//echo "//" . mysql_info() . "\n";

		//save resource
		$user_id=clean_db($post['rw_user_id']);
		$sql = "INSERT INTO resources (user_id,resource_type,key_id)
			VALUES ($user_id,'$resource_type',$post_id)
			ON DUPLICATE KEY UPDATE resource_id=LAST_INSERT_ID(resource_id)";
		//echo "/* $sql */\n"; //die;
		$wpdb->query($sql); //todo: error check
		//echo "//" . mysql_info() . "\n";
		
	}
	
	function get_user_account($email = '',$user_name = '',$block_create = false) {
		if ($this->user_id && !$email) return $this->user_id;
		global $wpdb;

		if (($current_user = wp_get_current_user()) && $current_user->ID) {
//echo "/* " . print_r($current_user,true) . " */\n";
			$wp_user_id = clean_db($current_user->ID);
			$email = clean_db($current_user->user_email);
			$user_name = clean_db($current_user->user_login);
			$ip = "''"; //todo ip address
			//todo: create new admin key
			$sql = "INSERT INTO users (wp_user_id,user_name,email,ip)
				VALUES($wp_user_id,$user_name,$email,$ip)
				ON DUPLICATE KEY UPDATE user_id=user_id";
			$wpdb->query($sql);
			$this->user_id = $wpdb->insert_id;
			return $this->user_id;
		} else {
//echo "/* cookie:\n" . print_r($_COOKIE,true) . " */\n";
			if ($_COOKIE && $_COOKIE['rw_cookie']) {
				$cookie = trim($_COOKIE['rw_cookie']);
			} else if ($_GET['rw_cookie']) {
				//try to get session stuff from URL
				$cookie = trim($_GET['rw_cookie']);
			}
	
			if ($cookie) {
				$sql = "SELECT * FROM users WHERE cookie = " . clean_db($cookie) . "
					AND (email=" . clean_db($email) . " OR ISNULL(email) OR ISNULL(" . clean_db($email) . "))";
//echo "/*\n$sql\n*/\n";
				$user = $wpdb->get_results($sql);
				if ($user && ($user = $user[0]) && $user->user_id) {
					//todo: put these both in one statement
					if ($email && (is_null($user->email) || !$user->email)) {
						$wpdb->query("UPDATE users SET email=" . clean_db($email) . " WHERE user_id={$user->user_id}");
//echo "/*\nsaved email address $email\n*/\n";
					}
					if ($user_name && (is_null($user->user_name) || !$user->user_name)) {
						$wpdb->query("UPDATE users SET user_name=" . clean_db($user_name) . " WHERE user_id={$user->user_id}");
//echo "/*\nsaved user_name $user_name\n*/\n";
					}
					$this->user_id = $user->user_id;
					return $user->user_id;
				}
			}

			if ((!$cookie || !$user) && !$block_create) {
				$cookie = sha1(SECURE_AUTH_KEY . time());
				if (!headers_sent()) {
					setcookie("rw_cookie", $cookie, $time, SITECOOKIEPATH, COOKIE_DOMAIN);
				}
				$email = clean_db($email);
				$user_name = clean_db($user_name);
				$ip = "''"; //todo ip address
				if ($email) {
					$admin_key = clean_db(sha1(SECURE_AUTH_KEY . $email . time()));
				} else {
					$admin_key = 'null';
				}
				$cookie = clean_db($cookie);
				$sql = "INSERT INTO users (wp_user_id,user_name,email,ip,cookie,admin_key)
					VALUES(NULL,$user_name,$email,$ip,$cookie,$admin_key)
					ON DUPLICATE KEY UPDATE user_id=user_id";
//echo "/*\n$sql\n*/\n";
				$wpdb->query($sql);
				$this->user_id = $wpdb->insert_id;
				return $this->user_id;
			} else {
				return false;
			}
		}

	}
	
	function callback_validate_video_url() {
		global $wpdb;
		
		//print_r($_REQUEST);
		//todo: clean up input in case of escaped characters or whatever
		
		//validate youtube url
		if (!preg_match($this->yt_regex,$_REQUEST['video_url'],$matches)) {
			$error_msg = 'Not a valid YouTube URL';
			$error = 'invalid-url';
		} else {
			$video_id = $matches[8];//todo: escape this for URL?
			$source = 'youtube.com';

			if (!$error && $video_id && ($video = $this->update_video_data($video_id,$error_msg))) {
				$sql = "SELECT posts.ID
					FROM videos
					INNER JOIN {$wpdb->posts} AS posts ON posts.ID = videos.post_id AND posts.post_status IN ('publish','draft','private')
					WHERE videos.foreign_key = " . clean_db($video_id) . "
					AND videos.post_id<>" . clean_db($_REQUEST['post_id']);

				//todo: allow edit if post_id is provided. check security
				$rows = $wpdb->get_results($sql);

				if (count($rows)) { //todo: allow for editing
					$error_msg = 'This video has <a href="' . get_permalink($rows[0]->ID) . '">already been posted</a>';
					$error = 'video-duplicate';
				} else {

					$response = new StdClass;
					$response->video_id = $video_id;
					$response->title = $video['title'];
					$response->description = $video['description'];
					$response->keywords = explode(',',$video['keywords']);
					$response->recorded_date = $video['rw_recorded'];
				}
			}
			if ($error_msg == 'YouTube video not found') {
				$error = 'video-not-found';
			} else if ($error_msg && !$error) {
				$error = 'embed-disallowed';
			}
		}
		
		//$response->hide_captcha = !$this->check_captcha(null);
		$response->error = $error;
		$response->error_msg = $error_msg;
		echo json_encode($response);
		exit;
	}

	function callback_add_video_action() {
		//todo: get rid of this
		global $wpdb;
		
		//print_r($_REQUEST);
		//todo: clean up input in case of escaped characters or whatever
		
		//validate youtube url
		if (!preg_match($this->yt_regex,$_REQUEST['video_url'],$matches)) {
			$error = 'Not a valid YouTube URL';
		} else {
			$video_id = $matches[8];//todo: escape this for URL?
			$source = 'youtube.com';

			if (!$error && ($video = $this->update_video_data($video_id,$error))) {
				$sql = "SELECT posts.ID
					FROM videos
					INNER JOIN {$wpdb->posts} AS posts ON posts.ID = videos.post_id AND posts.post_status IN ('publish','draft','private')
					WHERE videos.foreign_key = " . clean_db($video_id);
//echo "/* $sql \n*/\n";

				//todo: allow edit if post_id is provided. check security
				$rows = $wpdb->get_results($sql);
//echo "/* " . print_r($rows,true) . " \n*/\n";
				if (count($rows)) { //todo: allow for editing
					$error = 'This video has <a href="' . get_permalink($rows[0]->ID) . '">already been posted</a>';
				} else {

					$response = new StdClass;
					$response->video_id = $video_id;
					$response->title = $video['title'];
					$response->description = $video['description'];
					$response->keywords = $video['keywords'];
				}
			}
		}
		
		$response->hide_captcha = !$this->check_captcha(null);
		$response->error = $error;
		echo json_encode($response);
		exit;
	}
	
	function add_video_js_header() {
		global $rw_plugin_url;
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-form');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('rwplayer', $rw_plugin_url . 'js/rwplayer.js', array('jquery', 'jquery-form','jquery-ui-draggable'));
		wp_enqueue_script('rwclient', $rw_plugin_url . 'js/rwclient.js', array('jquery', 'jquery-form','jquery-ui-draggable'));

		//wp_deregister_script('swfobject');
		//wp_register_script('swfobject', $rw_plugin_url . 'swfobject/swfobject.js'); //todo: clean out the directory
		wp_enqueue_script('swfobject');

		if (!is_admin()) {
			wp_enqueue_script('recaptcha_ajax','http://api.recaptcha.net/js/recaptcha_ajax.js');
		?>
		<script type="text/javascript" >
			var ajaxurl = '<?= admin_url('admin-ajax.php') ?>';
		</script><?php
		}
	}
	
	function add_video_css_header() {
		global $rw_plugin_url;
		wp_enqueue_style( 'rwclient', $rw_plugin_url . 'css/rwplayer.css');
	}
	
	function render_embed_view($blah) {
		global $rw_plugin_path;

		load_template( $rw_plugin_path . '/embed.php' );
	}
	
	function check_captcha($arr = array(), $dec = 0) {
		if (is_user_logged_in() && current_user_can( 'edit_others_posts' )) { //basically, editors
//if (headers_sent()) { echo "check_captcha: logged in \n"; }
			return false;
		} else if ($this->captcha_uses_left && !$dec) {
//if (headers_sent()) { echo "check_captcha: already verified\n"; }
			return false;
		}
		global $wpdb;
		$user_id = $this->get_user_account();
//if (headers_sent()) { print_r($arr); }
		if ($arr["captcha_response"] && $arr["captcha_challenge"]) {
			//we do have a captcha response already. see if we've cached it
			$sql = "SELECT * FROM captcha_auth WHERE user_id=" . clean_db($user_id) . " AND TIMESTAMPDIFF(MINUTE,captcha_auth.timestamp,NOW()) < 30 AND captcha_response=" . clean_db($arr["captcha_response"]) . " AND captcha_challenge=" . clean_db($arr["captcha_challenge"]);
//if (headers_sent()) { echo "/* sql: $sql\n*/"; }
			$auth = $wpdb->get_results($sql,ARRAY_A);
			if (count($auth)) {
				$auth = $auth[0];
				if (!$auth['uses_left']) {
					return 'Please enter captcha solution';
				}
			} else {
				//captcha response hasn't been cached, so see if it's valid
				$resp = recaptcha_check_answer($this->captcha_privatekey,
												$_SERVER["REMOTE_ADDR"],
												$arr["captcha_challenge"],
												$arr["captcha_response"]);
				if (!$resp->is_valid) {
					//todo: error message
					return $resp->error;
				}
				//todo: just delete old ones. or delete 'em somewhere else
				$sql = "UPDATE captcha_auth SET uses_left = 0 WHERE user_id=" . clean_db($user_id) . " AND TIMESTAMPDIFF(MINUTE,captcha_auth.timestamp,NOW()) < 30 AND captcha_response=" . clean_db($arr["captcha_response"]) . " AND captcha_challenge=" . clean_db($arr["captcha_challenge"]);
//if (headers_sent()) { echo "/* sql: $sql\n*/"; }
				$wpdb->query($sql);
				$auth = array(
					'captcha_challenge' => $arr["captcha_challenge"],
					'captcha_response' => $arr["captcha_response"],
				);
			}
		}
		
		if ($auth) {
		} else if ($_COOKIE && ($auth_id = trim($_COOKIE['rw_auth_cookie']))) {
			$sql = "SELECT * FROM captcha_auth WHERE user_id=" . clean_db($user_id) . " AND auth_id=" . clean_db($auth_id);
			$auth = $wpdb->get_results($sql,ARRAY_A);
			if (count($auth)) {
				$auth = $auth[0];
				if (!$auth['uses_left']) {
					return 'Please enter captcha solution';
				}
			} else {
				//no captcha and cookie is invalid
				return 'Please enter captcha solution';
			}
		} else {
			//no captcha and no found cookie
			return 'Please enter captcha solution';
		}

		$auth = array_merge(array(
			'user_id' => $user_id,
			'remote_addr' => $_SERVER["REMOTE_ADDR"],
			'timestamp' => date('c')
		),$auth);
		if (!$auth['auth_id']) {
			$auth['auth_id'] = sha1(date('c') . $user_id . AUTH_KEY);
		}
		if ($auth['uses_left']) {
			$auth['uses_left'] -= $dec;
		} else if (is_user_logged_in()) {
			$auth['uses_left'] = 8;
		} else {
			$auth['uses_left'] = 4;
		}
		$this->captcha_uses_left = $auth['uses_left'];
		
		$auth_clean = array_map('clean_db',$auth);
		
		$sql = "INSERT INTO captcha_auth (" . implode(',',array_keys($auth_clean)) . ") VALUES (" . implode(',',array_values($auth_clean)) . ")
			ON DUPLICATE KEY UPDATE auth_id={$auth_clean['auth_id']}, uses_left={$auth_clean['uses_left']}";
//if (headers_sent()) { echo "/* sql: $sql\n*/"; }
		$wpdb->query($sql);

		if (!headers_sent()) {
			setcookie("rw_auth_cookie", $auth['auth_id'], 0, SITECOOKIEPATH, COOKIE_DOMAIN);
		}
		return false;
	}
	
	function get_video_data($post_id) {
		global $wpdb;
		if ($this->video_data_mem_cache[$post_id]) {
			return $this->video_data_mem_cache[$post_id];
		} else {
			$sql = "SELECT IFNULL(clips.post_id,videos.post_id) as post_id, videos.post_id as video_id, videos.source, videos.foreign_key, videos.duration, videos.aspect_ratio, clips.clip_id, clips.original_start, clips.original_end, clips.start_offset, clips.end_offset,
				#posts.post_author,
				thumbnails.thumbnail_url, thumbnails.width, thumbnails.height, videos.locked,
				videos.video_date, videos.video_date_accuracy, 
				videos.work_date, videos.work_date_accuracy,
				video_xml_cache.video_title as cache_title,
				video_xml_cache.video_description as cache_description
				FROM videos
				#INNER JOIN {$wpdb->posts} as posts ON posts.ID = videos.post_id
				INNER JOIN video_xml_cache ON video_xml_cache.foreign_key = videos.foreign_key
				LEFT JOIN clips ON clips.original_post_id = videos.post_id AND clips.post_id = $post_id
				LEFT JOIN thumbnails ON thumbnails.cache_id = video_xml_cache.cache_id
				WHERE (clips.post_id = $post_id OR ISNULL(clips.clip_id) AND videos.post_id = $post_id)
				ORDER BY ABS(thumbnails.time_offset - 
				(IFNULL(clips.start_offset,0) + IFNULL(clips.end_offset,videos.duration))/2) ASC,
				ABS(width - 90) ASC
				LIMIT 0,1";
			$video_data = $wpdb->get_results($sql);
			if ($video_data && count($video_data)) {
				$video_data = $video_data[0];

				$fields = array_keys(get_object_vars($video_data));
				foreach ($fields as $field) {
					$str = $video_data->$field;
					if (is_numeric($str)) {
						$video_data->$field = (double)$str;
					}
				}

				$this->video_data_mem_cache[$post_id] = $video_data;

				if ($video->post_id != $video->video_id) {
					$video->locked = true;
				}

				return $video_data;
			} else {
				return false;
			}
		}
	}

	function action_wp_insert_post($post_ID, $post) {
echo "<pre>\naction_wp_insert_post\n";
		global $wpdb;

		print_r($_POST);
		print_r($post); die;
	}
	
	function filter_wp_insert_post_data($data, $postarr) {
		global $wpdb;

		//todo: only update the below if post is publish, draft or private
		if ($postarr['ID'] && $postarr['post_type'] == 'video') {
//			echo "<pre>\nfilter_wp_insert_post_data\n";
//			print_r($postarr);

			if (!preg_match($this->yt_regex,$postarr['video_url'],$matches)) {
				//todo: error
				return false;
			}
		
			$foreign_key = $matches[8];
			$locked = ($postarr['post_status'] == 'publish');
			
			//parse, validate dates
			$date_regex = '/(\d{4})-(\d\d?)-(\d\d?)/';
			$accuracy_values = array('decade', 'part_decade', 'year', 'quarter', 'date');
			$work_date_accuracy = strtolower($postarr['work_date_accuracy']);
			$video_date_accuracy = strtolower($postarr['video_date_accuracy']);
			$work_date = $postarr['work_date'];
			$video_date = $postarr['video_date'];

			if (!preg_match($date_regex,$work_date) || !in_array($work_date_accuracy,$accuracy_values)) {
				//todo: validate date range?
				$work_date = null;
				$work_date_accuracy = null;
			}
			if (!preg_match($date_regex,$video_date) || !in_array($video_date_accuracy,$accuracy_values)) {
				//todo: validate date range?
				$video_date = null;
				$video_date_accuracy = null;
			}
		
			//todo:only update foreign_key if it has not been locked?
			$sql = "SELECT videos.post_id, videos.foreign_key, 
				FROM videos
				INNER JOIN {$wpdb->posts} AS posts ON posts.ID = videos.post_id AND posts.post_status IN ('published','draft','private')
				WHERE videos.foreign_key = " . clean_db($foreign_key) . " AND locked";
			$rows = $wpdb->get_results($sql);
			if (count($rows)) {
				if ($postarr['ID'] == $rows[0]->post_id) {
					$locked = 1;
				} else {
					//todo: error
					echo "fail!"; die;
					return false;
				}
			} else {
				$video = $this->get_video_data($postarr['ID']);
				$locked = $locked || ($video && $video->locked);
				if ($video->locked) {
					$foreign_key = $video->foreign_key;
				}
			}
		
			$post_id = clean_db($postarr['ID']);
			$foreign_key = clean_db($foreign_key);
			$work_date = clean_db($work_date);
			$video_date = clean_db($video_date);
			$work_date_accuracy = clean_db($work_date_accuracy);
			$video_date_accuracy = clean_db($video_date_accuracy);
			if (!$locked) {
				$locked = 0;
			}

			$sql = "INSERT INTO videos
				(post_id,source,foreign_key,duration,aspect_ratio,original_url,update_time,work_date,work_date_accuracy, video_date, video_date_accuracy, locked)
			SELECT $post_id, 'youtube.com',$foreign_key, video_xml_cache.duration, video_xml_cache.aspect_ratio, video_xml_cache.original_url, video_xml_cache.update_time, $work_date, $work_date_accuracy, $video_date, $video_date_accuracy, $locked
			FROM video_xml_cache WHERE video_xml_cache.source = 'youtube.com' AND video_xml_cache.foreign_key = $foreign_key
			ON DUPLICATE KEY UPDATE videos.foreign_key = $foreign_key, videos.work_date=$work_date,
				videos.work_date_accuracy=$work_date_accuracy, videos.video_date=$video_date,
				videos.video_date_accuracy=$video_date_accuracy, locked=$locked";
			$wpdb->query($sql);

			//save resource
			$user_id = $postarr['user_ID'];
			if (!$user_id && ($current_user = wp_get_current_user())) {
				$user_id = $current_user->ID;
			}
			$sql = "INSERT INTO resources (user_id,resource_type,key_id)
				VALUES ($user_id,'video',$post_id)
				ON DUPLICATE KEY UPDATE resource_id=LAST_INSERT_ID(resource_id)";
			//echo "/* $sql */\n"; //die;
			$wpdb->query($sql); //todo: error check
		}
		//todo: make this add a resource for all posts that don't have them
		
		if (false && isset($postarr['rw_approved']) &&
			($postarr['rw_approved'] == 0 || $postarr['rw_approved'] == 1) &&
			count($postarr['post'])
			&& ($user = wp_get_current_user()) && $user->has_cap('edit_others_posts')) {
			$post_ids = array_filter($postarr['post'], is_numeric);
			if (count($post_ids)) {
				$sql = "UPDATE resources SET approved = {$postarr['rw_approved']}
				WHERE resource_type IN ('video','clip','post') AND key_id IN (" . implode(',',$post_ids) . ")";
				//echo $sql; die;
				$wpdb->query($sql);
			}
		}
		return $data;
	}
	
	function filter_posts_orderby($orderby) {
		if (false && !is_admin()) {
			$o = explode(',',$orderby);
			array_unshift($o,"(post_author = users.wp_user_id OR users.email IS NOT NULL) DESC");
			array_unshift($o,"rw_approved DESC");
			$orderby = implode(', ',$o);
		}
		return $orderby;
	}
	
	function filter_wp_post_revision_fields($fields) {
		$fields = array_merge($fields, array(
			'work_date' => __( 'Work Creation Date' ),
			'video_date' => __( 'Video Creation Date' )
		));
		return $fields;
	}
}

/*

todo: update server database as follows

ALTER TABLE `videos` ADD `work_date` DATE NULL ,
ADD `work_date_accuracy` ENUM( 'decade', 'part_decade', 'year', 'quarter', 'date' ) NULL ,
ADD `video_date` DATE NULL ,
ADD `video_date_accuracy` ENUM( 'decade', 'part_decade', 'year', 'quarter', 'date' ) NULL ;

UPDATE rw_posts INNER JOIN videos ON videos.post_id=rw_posts.ID
SET rw_posts.post_type = 'video';

UPDATE rw_posts INNER JOIN clips ON clips.post_id=rw_posts.ID
SET rw_posts.post_type = 'clip'
WHERE clips.original_post_id <> clips.post_id;

ALTER TABLE `videos` ADD `locked` TINYINT( 1 ) NOT NULL DEFAULT '0';

UPDATE videos INNER JOIN rw_posts ON videos.post_id=rw_posts.ID
SET locked = 1 WHERE rw_posts.post_status = 'publish';

SELECT
	videos.foreign_key,
	COUNT(videos.post_id) as num_videos,
	COUNT(rw_posts.ID) as num_posts
FROM videos
LEFT JOIN rw_posts ON videos.post_id=rw_posts.ID
GROUP BY videos.foreign_key
ORDER BY num_posts DESC;

#DELETE videos.* FROM videos LEFT JOIN rw_posts ON videos.post_id=rw_posts.ID WHERE rw_posts.ID IS NULL;

ALTER TABLE `video_xml_cache` ADD `duration` FLOAT NULL AFTER `foreign_key` ,
ADD `aspect_ratio` FLOAT NOT NULL DEFAULT '1.333333333333333' AFTER `duration` ;

ALTER TABLE `video_xml_cache` ADD `video_title` TEXT NULL ,
ADD `video_description` TEXT NULL ;

*/


?>