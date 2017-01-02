<?php

set_time_limit( 1800 );
require_once 'config.php';

$wpdb   = new wpdb( WPDP_USER, WPDP_PASS, WPDP_DB, WPDP_HOST );
$joomdb = new wpdb( JOOM_USER, JOOM_PASS, JOOM_DB, JOOM_HOST );

$sql = 'SELECT * FROM ' . JOOM_PREFIX . 'jcomments'; // i am using jcomment here you can tranfer joscomment same way
$comments = $joomdb->get_results( $sql );

foreach ($comments as $comment) {
    
    $exists = $wpdb->get_var("SELECT comment_ID FROM ". WPDP_PREFIX . "comments WHERE comment_ID = ".$comment->id."");
    if ( ! $exists ) {
        $commentdata = array(
            'comment_ID'            => $comment->id,
            'comment_post_ID'       => $comment->object_id,
            'comment_author'        => $comment->name,
            'comment_author_IP'     => $comment->ip,
            'comment_author_email'  => $comment->email,
            'comment_author_url'    => $comment->homepage,
            'comment_date'          => $comment->date,
            'comment_content'       => $comment->title.' <br>'.$comment->comment, // concating title and body as wordpress dont have title field
            'comment_approved'      => $comment->published,
            'comment_parent'        => $comment->parent,
            'user_id'               => $comment->userid
        );
        $wpdb->insert(''. WPDP_PREFIX . 'comments', $commentdata);
    }else{
        $where = array(
            'comment_ID'            => $comment->id,
            'comment_post_ID'       => $comment->object_id
       );
        $commentUpdatedata = array(
            'comment_author'        => $comment->name,
            'comment_author_IP'     => $comment->ip,
            'comment_author_email'  => $comment->email,
            'comment_author_url'    => $comment->homepage,
            'comment_date'          => $comment->date,
            'comment_content'       => $comment->title.' <br>'.$comment->comment, // concating title and body as wordpress dont have title field
            'comment_approved'      => $comment->published,
            'comment_parent'        => $comment->parent,
            'user_id'               => $comment->userid
        );
        $wpdb->update(''. WPDP_PREFIX . 'comments', $commentUpdatedata,$where);
    }
}

echo '<br>';
echo 'Comments  are Transferred';