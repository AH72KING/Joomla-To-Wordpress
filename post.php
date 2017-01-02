<?php

set_time_limit( 1800 );
require_once 'config.php';

$wpdb   = new wpdb( WPDP_USER, WPDP_PASS, WPDP_DB, WPDP_HOST );
$joomdb = new wpdb( JOOM_USER, JOOM_PASS, JOOM_DB, JOOM_HOST );

$sql = 'SELECT c.*, cat.title as cat_name, u.username FROM ' . JOOM_PREFIX . 'content c
        LEFT JOIN ' . JOOM_PREFIX . 'categories cat ON cat.id = c.catid
        LEFT JOIN ' . JOOM_PREFIX . 'users u ON u.id = c.created_by
        WHERE c.state = 1';

$posts = $joomdb->get_results($sql);

foreach ($posts as $post) {

    $author = $wpdb->get_results("SELECT * FROM WHERE user_login = '".$post->username."' ");
	$exists = $wpdb->get_var("SELECT ID FROM ". WPDP_PREFIX . "posts WHERE ID = ".$post->id." ");

    $authorID = $author->ID;

    $postArray = array(
    	'ID'                => $post->id,
    	'post_author'       => $authorID,
    	'post_date'         => $post->created,
    	'post_content'      => $post->introtext . $post->fulltext, //combining intro text and fulltext
        'post_title'        => $post->title,
        'post_status'       => 'publish',
        'comment_status'    => 'open',
        'ping_status'       => 'open',
        'post_name'         =>  $post->id.'-'.$post->alias // slug
    );

    $postCatTermArray = array(
    	'object_id'        => $post->id,
    	'term_taxonomy_id' => $post->catid,
        'term_order'       => 0
    );
    
    $postArray['post_content'] = str_replace( '<hr id="system-readmore" />', "<!--Read More-->", $postArray['post_content'] );

    if ( !$exists) {
     
    	$wpdb->insert(''. WPDP_PREFIX . 'posts', $postArray);
    	$wpdb->insert(''. WPDP_PREFIX . 'term_relationships', $postCatTermArray);
    	

    }else{
    	$where = array(
    		'ID' => $post->id
    	);
    	$postUpdatearr = array(
	    	'post_author'     => $authorID,
	    	'post_date'       => $post->created,
	    	'post_content'    => $post->introtext . $post->fulltext,
	        'post_title'      => $post->title,
	        'post_status'     => 'publish',
	        'comment_status'  => 'open',
	        'ping_status'     => 'open',
	        'post_name'       =>  $post->id.'-'.$post->alias // slug
	    );
	   
    	$wpdb->update(''. WPDP_PREFIX . 'posts', $postUpdatearr,$where);
    
    }

    $postMetaAuthorAliasArray = array(
    	'post_id'    => $post->id,
    	'meta_key'   => 'ca_author_alias',  // there is great plugin that use this search for it CA Author Alias
        'meta_value' => $post->created_by_alias
    );
    // for hits count;
    $postMetaHitsArray = array(
    	'post_id'    => $post->id,
    	'meta_key'   => 'post_hits',  // Change it as you code it in wp
        'meta_value' => $post->hits
    );
    $whereAuthorAliasPostID = array(
    	'post_id'  => $post->id,
    	'meta_key' => 'ca_author_alias' // there is great plugin that use this search for it CA Author Alias
    );
    $whereMetaHitsUpdatePostID = array(
    	'post_id' => $post->id,
    	'meta_key'=> 'post_hits'
    );
    // i am deleting it first because some time it add multiple when you re run script 
    // solution for that was to update .!!
    $wpdb->delete(''. WPDP_PREFIX . 'postmeta', $whereAuthorAliasPostID);
    $wpdb->delete(''. WPDP_PREFIX . 'postmeta', $whereMetaHitsUpdatePostID);
    $wpdb->insert(''. WPDP_PREFIX . 'postmeta', $postMetaAuthorAliasArray);
    $wpdb->insert(''. WPDP_PREFIX . 'postmeta', $postMetaHitsArray);
    
}
echo '<br>';
echo 'Posts  are Transferred';
