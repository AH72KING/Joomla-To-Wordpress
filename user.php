<?php

set_time_limit( 1800 );
require_once 'config.php';

$wpdb   = new wpdb( WPDP_USER, WPDP_PASS, WPDP_DB, WPDP_HOST );
$joomdb = new wpdb( JOOM_USER, JOOM_PASS, JOOM_DB, JOOM_HOST );
$users = $joomdb->get_results( 'select * from ' . JOOM_PREFIX . 'users' );

if ( $users ) {
    foreach ($users as $user) {
        $user_data = array(
            'ID'              => $user->id,
            'user_pass'       => $user->password,
            'user_login'      => $user->username,
            'user_email'      => $user->email,
            'display_name'    => $user->name,
            'user_registered' => $user->registerDate,
        );
        $user_metadata_capabilities = array(
            'user_id'    => $user->id,
            'meta_key'   => 'wp_capabilities',
            'meta_value' => 'a:1:{s:13:"administrator";s:1:"1";}'  // change administrator if you want to
        );
        $user_metadata_user_level = array(
            'user_id'    => $user->id,
            'meta_key'   => 'wp_user_level',
            'meta_value' => '10' // change wp_user_level if you want to
        );

        $wpdb->insert(''. WPDP_PREFIX . 'users', $user_data);
        $wpdb->insert(''. WPDP_PREFIX . 'usermeta', $user_metadata_capabilities);
        $wpdb->insert(''. WPDP_PREFIX . 'usermeta', $user_metadata_user_level);

        //In Simple SQL
        /*
        INSERT INTO `wp_users` (`user_login`, `user_pass`, `user_nicename`, `user_email`, `user_status`)
        VALUES ('newadmin', MD5('pass123'), 'firstname lastname', 'email@example.com', '0');

        INSERT INTO `wp_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) 
        VALUES (NULL, (Select max(id) FROM wp_users), 'wp_capabilities', 'a:1:{s:13:"administrator";s:1:"1";}');

        INSERT INTO `wp_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) 
        VALUES (NULL, (Select max(id) FROM wp_users), 'wp_user_level', '10');
        */
     
    }
}
echo '<br>';
echo 'Users are Transferred';