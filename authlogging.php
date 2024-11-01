<?php
/**
 * @package SOUNDBOARD WordPress Authentication Logging Plugin
 * @version 0.1
 */

/*
Plugin Name: wp-auth-logging
Plugin URI: http://memo.jj-net.jp/
Description: wordpress authentication activity is logged via syslog(3).
Author: Yusuke YOSHIDA
Version: 0.1
Author URI: http://www.soundboard.co.jp/
*/


function wp_auth_logging_auth_log($pri, $msg) {
  $h = home_url("/");
  $f = defined("LOG_AUTHPRIV") ? LOG_AUTHPRIV : LOG_AUTH;

  openlog("wordpress($h)", LOG_NDELAY|LOG_PID, $f);
  syslog($pri, $msg);
  closelog();
}

function wp_auth_logging_client_ip_address() {
  if (isset($_SERVER['HTTP_CLIENT_IP'])) {
    $addr =  $_SERVER['HTTP_CLIENT_IP'];
  } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $addr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR'], 2);
  } else {
    $addr = $_SERVER['REMOTE_ADDR'];
  }
  return $addr;
}

function wp_auth_logging_fail_log($u) {
  $addr = wp_auth_logging_client_ip_address();
  wp_auth_logging_auth_log(LOG_NOTICE, "Failure login for $u from $addr");
}

function wp_auth_logging_success_log($u) {
  $addr = wp_auth_logging_client_ip_address();
  wp_auth_logging_auth_log(LOG_INFO, "Success login for $u from $addr");
}

add_action("wp_login_failed", wp_auth_logging_fail_log);
add_action("wp_login", wp_auth_logging_success_log);

?>
