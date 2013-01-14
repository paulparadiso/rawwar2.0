<?php
// Template Name: Get Data Template
?>

<?php

$sql = "
SELECT
posts.post_date_gmt,
posts.post_title,
posts.post_content,
posts.post_name,
videos.foreign_key,
videos.duration,
videos.work_date,
videos.video_date,

IFNULL(wpusers.display_name collate utf8_general_ci, users.user_name) as user_name,


IFNULL(wpusers.user_email collate utf8_general_ci, users.email) as user_email

FROM rw_posts AS posts 
INNER JOIN videos ON videos.post_id = posts.ID
INNER JOIN resources ON resources.key_id = posts.ID AND resources.resource_type ='video'
LEFT JOIN rw_users as wpusers ON wpusers.ID = posts.post_author
LEFT JOIN users ON resources.user_id = users.user_id

WHERE posts.post_type = 'video' AND posts.post_status = 'publish'
ORDER BY posts.post_date ASC";

global $wpdb;

$results = $wpdb->get_results($sql);

$out = json_encode($results);

echo $out;

?>