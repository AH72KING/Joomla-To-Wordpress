<?php

require_once 'config.php';

$wpdb   = new wpdb( WPDP_USER, WPDP_PASS, WPDP_DB, WPDP_HOST );
$joomdb = new wpdb( JOOM_USER, JOOM_PASS, JOOM_DB, JOOM_HOST );
$categories = $joomdb->get_results( 'select * from ' . JOOM_PREFIX . 'categories' );

foreach ($categories as $category) {
    $catTermArray = array(
    	'term_id'    => $category->id,
        'name'       => $category->title, // check your joomla version & DB it is changed to name in new joomla version
        'slug'       => $category->alias,
        'term_group' => 0
    );
    $catTermtaxonomyArray = array(
    	'term_taxonomy_id' => $category->id,
    	'term_id'          => $category->id,
        'taxonomy'         => 'category',
        'description'      => $category->description,
        'parent'           => 0
    );

    $wpdb->insert(''. WPDP_PREFIX . 'terms', $catTermArray);
    $wpdb->insert(''. WPDP_PREFIX . 'term_taxonomy', $catTermtaxonomyArray);
}
echo '<br>';
echo 'Cats are Transfered';