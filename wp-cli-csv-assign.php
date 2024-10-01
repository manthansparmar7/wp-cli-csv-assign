<?php
/*
Plugin Name: WP-CLI CSV Assign
Description: An import WP-CLI command to assign categories and tags to posts from a CSV file, also export CSV file based on saved posts data using export CSV command.
Version: 1.0.0
Author: Manthan Parmar
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

// Plugin version
define( 'WP_CLI_CSV_ASSIGN_VERSION', '1.0.0' );

// Plugin directory path
define( 'WP_CLI_CSV_ASSIGN_DIR_PATH', plugin_dir_path( __FILE__ ) );

/**
 * File that conatins code to import csv file and assign category and tags from it.
 */
require_once WP_CLI_CSV_ASSIGN_DIR_PATH . '/includes/csv-import.php';

/**
 * File that conatins code to export csv file based on stored posts in backend.
 */
require_once WP_CLI_CSV_ASSIGN_DIR_PATH . '/includes/csv-export.php';