<?php

set_time_limit( 1800 );
require_once 'config.php';

$wpdb   = new wpdb( WPDP_USER, WPDP_PASS, WPDP_DB, WPDP_HOST );

$posts  = $wpdb->get_results("SELECT ID FROM ". WPDP_PREFIX . "posts");

foreach ($posts as  $post) {

		$NumComments = $wpdb->get_var("SELECT COUNT(comment_ID) FROM ". WPDP_PREFIX . "comments WHERE comment_post_ID = ".$post->ID."");

		$where =  array(
			'ID' => $post->ID
		);

		$commentUpdatedata = array(
			'comment_count' => $NumComments
		);
		$wpdb->update(''. WPDP_PREFIX . 'posts', $commentUpdatedata,$where);

}
echo '<br>';
echo 'Comments Counted and Transferred';
