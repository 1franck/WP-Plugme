<?php
/**
 * Plugin Name: PlugMe Demo
 * Plugin URI: https://github.com/1Franck/plugme
 * Description: Wordpress database driven plugin helper
 * Version: 1.0.0
 * Author: François Lajoie
 * Author URI: http://francoislajoie.com
 * License: MIT License
 */

include_once 'funcs.php';

/**
 * PLUGIN ACTIVATION HOOKS SECTION
 * -------------------------------
 */


/**
 * Create database tables
 */
register_activation_hook( __FILE__, 'pm_example_installation' );

function pm_example_installation()
{
    global $wpdb;
    include 'installation.php';
}

/**
 * ADMIN PANEL
 * -----------
 */
add_action('admin_menu', 'pm_example_admin_menu');
 
function pm_example_admin_menu()
{
    add_menu_page( 
        'Plugme', //page title
        'Plugme Demo', //menu title
        'manage_options', //capability
        'plugme_menu', //menu slug
        'pm_example_admin_content', //tpl func 
        'dashicons-admin-plugins', //menu icon
        1 //menu order
    );
}
 
function pm_example_admin_content()
{
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    include dirname(__FILE__).'/adminpage.tpl.php';
}