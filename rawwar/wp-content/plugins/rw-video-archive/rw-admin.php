<?php


class VideoArchiveAdmin {
	function __construct($parent) {
		$this->videoArchive = $parent;

		add_action('admin_menu', array($this,'featured_article_menu'));
		add_filter('add_menu_classes',array($this,'filter_add_menu_classes'));

		add_filter('menu_order',array($this,'filter_menu_order'));
		add_filter('custom_menu_order',array($this,'filter_custom_menu_order'));

	}

	function admin_init() {
		add_meta_box("video_url", "Play Clip", array($this,"clip_edit_box"), "clip", "normal", "high");
		add_meta_box("video_url", "Video Source", array($this,"video_url_edit_box"), "video", "normal", "high");
		add_meta_box("work_date", "Work Creation Date", array($this,"work_date_edit_box"), "video", "normal", "high");
		add_meta_box("video_date", "Video Creation Date", array($this,"video_date_edit_box"), "video", "normal", "high");

		add_filter("manage_edit-video_columns", array($this,'filter_video_columns'));
		add_filter("manage_edit-clip_columns", array($this,'filter_clip_columns'));
		add_action('manage_posts_custom_column', array($this,'action_manage_posts_custom_column') , 10, 2);
		add_action('admin_print_styles', array($this,'action_admin_print_styles') );
		add_action('admin_print_scripts', array($this,'action_admin_print_scripts') );
		add_action('restrict_manage_posts', array($this,'action_restrict_manage_posts') );
		
		add_action('bulk_edit_custom_box', array($this,'action_bulk_edit_custom_box'), 10, 2 );

		add_action('wp_dashboard_setup', array($this,'action_wp_dashboard_setup') );

		//the old way. todo: delete
		add_filter('manage_posts_columns', array($this,'filter_manage_posts_columns') );

		$this->setup_capabilities();
	}
	
	function setup_capabilities() {
		global $wp_roles;

		$wp_roles->add_cap('author','edit_others_posts');
		$wp_roles->remove_cap('author','delete_published_posts');
		$wp_roles->remove_cap('author','moderate_comments');
		
		add_filter('user_has_cap',array($this,'filter_user_has_cap'), 10, 3);
	}

	function filter_user_has_cap($allcaps, $caps, $args) {
		if ($args[0] == 'edit_post') {
			$current_user = wp_get_current_user();
			$post = get_post($args[2]);

			if (!$post || $post->post_author != $current_user->ID && !$allcaps['edit_users'] &&
				$post->post_type == 'clip') {
				foreach($caps as $cap)  {
					$allcaps[$cap] = 0;
				}
			}
		}
		return $allcaps;
	}

	function filter_add_menu_classes($menus) {
//print_r($menus);
		$current_user = wp_get_current_user();
		foreach ($menus as $i => $menu) {
			if ($menu[5] == 'menu-comments' && !(
				in_array('administrator', $current_user->roles) ||
				in_array('editor', $current_user->roles)
			)) {
				unset($menus[$i]);
			}
		}
		return $menus;
	}
	
	function filter_menu_order($menu_order) {
		//print_r($menu_order);
		$menus = array(
			'edit.php?post_type=video',
			'edit.php',
			'edit.php?post_type=clip',
			'upload.php',
			'edit-comments.php',
			'edit.php?post_type=page',
			'link-manager.php'
		);
		$indexes = array();
		
		foreach ($menu_order as $i => $menu) {
			if (in_array($menu,$menus)) {
				$indexes[] = $i;
			}
		}

		foreach ($menus as $menu) {
			$i = array_shift($indexes);
			$menu_order[$i] = $menu;
			if (!count($indexes)) {
				break;
			}
		}
		
		//print_r($menu_order); die;
		return $menu_order;
	}

	function filter_custom_menu_order($order) {
		return true;
	}

	function action_admin_print_scripts() {
		global $rw_plugin_url;

		global $post;

		if ($post->post_type == 'video') {
			wp_enqueue_script('jquery-ui-slider', $rw_plugin_url . 'js/ui.slider.js', array('jquery', 'jquery-ui-core'));
			wp_enqueue_script('rw-admin', $rw_plugin_url . 'js/rw-admin.js', array('jquery-ui-slider','swfobject'));
//		} else if ($post->post_type == 'clip') {
//			wp_enqueue_script('rw-admin', $rw_plugin_url . 'js/rwplayer.js');
		}

	}
	
	function action_admin_print_styles() {
		global $rw_plugin_url;
		echo "<style type=\"text/css\"> .fixed .column-approved { width: 8%; text-align: center } </style>\n";
		
		wp_enqueue_style('jquery-ui-base',$rw_plugin_url . 'css/jquery/themes/base/ui.all.css');
		wp_enqueue_style('rw-admin',$rw_plugin_url . 'css/rw-admin.css');
		
		global $post;

		if ($post->post_type == 'clip') {
			wp_enqueue_style('rwplayer',$rw_plugin_url . 'css/rwplayer.css');
		}
	}

	function action_bulk_edit_custom_box($column_name, $type) {
		//echo "column_name=$column_name\n";
		//echo "type=$type\n";
		if ($type == 'post' && $column_name == 'approved' &&
			($user = wp_get_current_user()) && $user->has_cap('edit_others_posts')) {
		?><div><fieldset class="inline-edit-col-approved">
			<div class="inline-edit-col">
				<label><span class="title">Approved</span><select name="rw_approved">
					<option value="-1">- No Change -</option>
					<option value="0">Not Approved</option>
					<option value="1">Approved</option>
				</select></label>
			</div>
		</fieldset></div>
		<?php
		}
	}
	
	function action_restrict_manage_posts() {
		$post_type_object = get_post_type_object($_GET['post_type']);
		if ($post_type_object) {
			$object = $post_type_object->labels->name;
			$current_user = wp_get_current_user();
			$author = $current_user->user_login;
			$selected = $_GET['author_name'] == $author ? 'selected="selected"' : '';
			echo <<<EOT
<select name="author_name">
	<option>$object by all Authors</option>
	<option value="$author" $selected>$object by Me</option>
</select>
EOT;
		}
	}

	function action_wp_dashboard_setup() {
		global $wp_meta_boxes;
//		print_r($wp_meta_boxes);
		
		//get rid of extra dashboard widgets
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
	}

	function clip_edit_box($post) {
		$video = $this->videoArchive->get_video_data($post->ID);
		if ($video && $video->foreign_key) {
			$post->video = $video;
			echo $this->videoArchive->get_the_player(array(
				'fullscreen' => false,
				'edit_clip' => 'mini',
				'share_clip' => false,
				'show_title' => false,
				'width' => 400,
				'post' => $post
			));
		}
	}

	function video_url_edit_box($post) {
		$video = $this->videoArchive->get_video_data($post->ID);
		if ($video && $video->foreign_key) {
			if ($video->locked) {
				$input_type = 'hidden';
				$title = 'YouTube Video';
			} else {
				$input_type = 'text';
				$title = 'YouTube Video URL';
			}

			$video_url = 'http://www.youtube.com/watch?v=' . $video->foreign_key;
			$style=' style="display: block"';

			$video_preview = '<object id="video_preview" width="233" height="200"><param name="movie" value="http://www.youtube.com/v/' . $video->foreign_key . '&amp;hl=en_US&amp;fs=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' . $video->foreign_key . '&amp;hl=en_US&amp;fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="233" height="200"></embed></object>';
			
			$cache_title = htmlentities($video->cache_title);
			$cache_description = htmlentities($video->cache_description);
		} else {
			$input_type = 'text';
			$video_url = '';
			$video_preview = '<div id="video_preview"></div>';
		}
		?>
		<label for="video_url_input"><?= $title ?></label>
		<input type="<?= $input_type ?>" id="video_url_input" value="<?= $video_url ?>" name="video_url"/>
		<div id="video_url_error">&nbsp;</div>
		<?= $video_preview ?>
		<div id="video_preview_stats"<?= $style ?>>
			<h4 id="video_preview_title"><?= $cache_title ?></h4>
			<div id="video_preview_description"><?= $cache_description ?></div>
			<ul id="video_preview_keywords"></ul>
		</div>
		<?php
	}
	
	function work_date_edit_box($post) {		
		$video = $this->videoArchive->get_video_data($post->ID);

		?>
		<div id="work_date_container">
			<input type="hidden" name="work_date" value="<?= $video->work_date ?>"/>
			<input type="hidden" name="work_date_accuracy" value="<?= $video->work_date_accuracy ?>"/>
		</div>
		<?php
	}

	function video_date_edit_box($post) {		
		$video = $this->videoArchive->get_video_data($post->ID);

		?><div id="video_date_container">
			<input type="hidden" name="video_date" value="<?= $video->video_date ?>"/>
			<input type="hidden" name="video_date_accuracy" value="<?= $video->video_date_accuracy ?>"/>
		</div>
		<?php
	}

	function filter_manage_posts_columns($defaults) {
		if (($user = wp_get_current_user()) && $user->has_cap('edit_others_posts')) {
			$defaults['approved'] = 'Approved';
		}
		return $defaults;
	}
	
	function filter_video_columns($columns) {
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => "Video Title",
			"author" => "Author",
			"date" => "Date",
			"duration" => "Duration",
			"tags" => "Tags",
			"comments" => '<div class="vers"><img alt="Comments" src="http://localhost:8888/rawwar/wp-admin/images/comment-grey-bubble.png" /></div>'
		);
		//todo: add people who appear

		return $columns;
	}
	
	function filter_clip_columns($columns) {
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => "Clip Title",
			"source_video" => "Source Video",
			"author" => "Author",
			"date" => "Date",
			"duration" => "Duration",
			"tags" => "Tags",
			"comments" => '<div class="vers"><img alt="Comments" src="http://localhost:8888/rawwar/wp-admin/images/comment-grey-bubble.png" /></div>'
		);
		//todo: add people who appear

		return $columns;
	}
	
	function action_manage_posts_custom_column($column_name, $id) {
		global $post;
//todo: remove 'approved'
		if ($column_name == 'duration') {
			$video = $this->videoArchive->get_video_data($id);
			if ($video) {
				if (isset($video->clip_id) && $video->post_id != $video->video_id) {
					$seconds = round($video->end_offset - $video->start_offset);
				} else {
					$seconds = round($video->duration);
				}
				echo sprintf('%d:%02d',floor($seconds / 60),$seconds % 60);
			}
		} else if ($column_name == 'source_video') {
			$video = $this->videoArchive->get_video_data($id);
			//echo $video->
			echo '<a href="' . get_permalink($video->video_id) . '">' . get_the_title($video->video_id) . '</a>';
		} else if ($column_name == 'approved' &&
			($user = wp_get_current_user()) && $user->has_cap('edit_others_posts')) {
			if ($post->rw_approved) {
				echo "Yes";
			} else {
				echo "No";
			}
		}
	}

	function featured_article_menu() {
				if ( function_exists('add_submenu_page') ) {
					add_submenu_page('index.php', __('Featured Article'), __('Featured Article'), 'manage_options', 'rw-featured-config', array($this,'featured_article_page'));
				}
			return;
			
			$current_user = wp_get_current_user();
			if (true || in_array('administrator', $current_user->roles) ||
				in_array('editor', $current_user->roles)
			) {
			}
	}

	function featured_article_page() {
	
		if ( isset($_POST['submit']) ) {
			if ( function_exists('current_user_can') && !current_user_can('manage_options') )
				die(__('Cheatin&#8217; uh?'));
	
			if ( isset( $_POST['rw_featured_article'] ) && $_POST['rw_featured_article'] )
				update_option( 'rw_featured_article', $_POST['rw_featured_article'] );
			else
				delete_option( 'rw_featured_article' );
		}
	
		$rw_featured_article = (int)get_option('rw_featured_article');

?>
<?php if ( !empty($_POST['submit'] ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
<h2><?php _e('Featured Article'); ?></h2>
<div class="narrow">
<form action="" method="post" id="rw-featured-article">
<?php

	global $wpdb;
	$sql = "SELECT SQL_CALC_FOUND_ROWS
		{$wpdb->posts}.*,resources.resource_type, resources.user_id as rw_user_id
		FROM {$wpdb->posts} LEFT JOIN resources ON resources.key_id = rw_posts.ID AND resources.resource_type IN ('video','clip','post')
		LEFT JOIN users ON resources.user_id = users.user_id
		WHERE 1=1  AND {$wpdb->posts}.post_type IN ('video', 'post') AND ({$wpdb->posts}.post_status = 'publish')
		ORDER BY {$wpdb->posts}.post_title ASC";

		$posts = $wpdb->get_results($sql);
//print_r($posts);
?>
<p><label for="rw_featured_article_select">Select Article to feature on Front Page</label></p>
<p><select id="rw_featured_article_select" name="rw_featured_article"><option>  None</option>
<?php

	foreach ($posts as $post) {
		echo '<option value="' . $post->ID;
		if ($rw_featured_article == $post->ID) {
			echo '" selected="selected';
		}
		echo '">' . htmlentities($post->post_title) . '  (' . $post->ID . ')</option>';
	}

?></select></p>
	<p class="submit"><input type="submit" name="submit" value="<?php _e('Update options &raquo;'); ?>" /></p>
</form>

</div>
</div>
<?php
}

}


?>