<?php
   /*
   Plugin Name: bets today post
   Plugin URI: https://betstoday.com/
   description: This plugin to Used for post
   Version: 1.2
   Author: betstoday.us
   Author URI: https://betstoday.us/
   License: GPL2
   */
function betstodaypost_install() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();

  // Create first table
  $betstodaypost1 = $wpdb->prefix . 'betstodaypost';
  $sql1 = "CREATE TABLE $betstodaypost1 (
    match_id int(11) NOT NULL AUTO_INCREMENT,
    matchup varchar(250) NOT NULL,
    oddsoption varchar(6000) NOT NULL,
    odds varchar(6000) NOT NULL,
    links varchar(1000) NOT NULL,
    result varchar(600) NOT NULL,
    matchdate varchar(600) NOT NULL,
    last_update datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    PRIMARY KEY  (match_id)
  ) $charset_collate;";
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql1 );

  // Create second table
  $titletable = $wpdb->prefix . 'betstitle';
  $sql2 = "CREATE TABLE $titletable (
    titletable_id int(11) NOT NULL AUTO_INCREMENT,
    bets_title varchar(255) NOT NULL,
    bets_update datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
    PRIMARY KEY  (titletable_id)
  ) $charset_collate;";
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql2 );
}
register_activation_hook( __FILE__, 'betstodaypost_install' );
function my_plugin_deactivation() {
  global $wpdb;
  $table_name1 = $wpdb->prefix . 'betstodaypost';
  $table_name2 = $wpdb->prefix . 'betstitle';
  $wpdb->query("DROP TABLE IF EXISTS $table_name1, $table_name2");
}

register_deactivation_hook( __FILE__, 'my_plugin_deactivation' );

include(plugin_dir_path(__FILE__) . 'admin/betstodaypost.php');
?>