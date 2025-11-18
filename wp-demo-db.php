<?php

/**
 * Plugin Name: WP Demo DB
 * Version: 1.0
 * Author: You!
 * Description: CRUD with WordPress Database
 */

if (!defined('ABSPATH'))
     exit;

if (!defined('WPDBDEMO_PATH')) {
     define('WPDBDEMO_PATH', plugin_dir_path(__FILE__));
}
if (!defined('WPDBDEMO_URL')) {
     define('WPDBDEMO_URL', plugin_dir_url(__FILE__));
}

require_once WPDBDEMO_PATH . 'includes/class-wp-demo-db.php';
require_once WPDBDEMO_PATH . 'includes/class-wp-demo-db-admin.php';
require_once WPDBDEMO_PATH . 'includes/class-wp-demo-db-enqueue.php';
require_once WPDBDEMO_PATH . 'includes/class-wp-demo-db-api.php';

register_activation_hook(__FILE__, ['WPDemoDb_Database', 'create_table']);
