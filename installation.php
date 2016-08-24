<?php
/**
 * PlugMe Demo Table example
 */

$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix.'plugme_example';


// table membres
$sql = "CREATE TABLE IF NOT EXISTS `".$table_name."` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255),
    `email` varchar(255),
    `password` varchar(255),
    `gender` enum('male','female', 'both', 'unknown') default 'unknown',
    `occupation` varchar(255),
    `bio` TEXT,
    `image` varchar(255),
    `dateofbirth` date,
    `enabled` tinyint(1) default '0',
    `featured` tinyint(1) default '0',
    `website` varchar(255),
    `phone` varchar(14),
    `country` varchar(255),
    `note` smallint(6),
    `role` varchar(255),
    `joindate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,    
    UNIQUE KEY id (id)
) $charset_collate;";

require_once ABSPATH . 'wp-admin/includes/upgrade.php';
dbDelta($sql);


// insert fake data data
$result = $wpdb->get_row('SELECT COUNT(id) as c FROM '.$table_name, ARRAY_A);
if(!empty($result) && array_key_exists('c', $result) && $result['c'] == 0) {

    $fake_data = include 'fakedata.php';

    foreach($fake_data as $record) {
        $wpdb->insert($table_name, $record);
    }
}