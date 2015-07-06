<?php
/**
 * Plugin Name: Smallgroup Map
 * Description: A plugin to show smallgroups on a map
 * Version: 2.3.1
 * Author: Hornig Software
 * Author URI: http://hornig-software.com
 * License: The MIT License (MIT)
 * Text Domain: HS_sg_map
 * Domain Path: /lang
 */
 
 /* Copyright (c)2015 Hornig Software <info@h-software.de>

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

/*textfiles to internationalize the plugin*/
function HS_sg_map_textdomain(){
	load_plugin_textdomain( 'HS_sg_map', false, basename( dirname( __FILE__ ) ) . '/lang' );
}
add_action( 'plugins_loaded', 'HS_sg_map_textdomain' );

/*file for creating the post*/
require_once( 'map.php' );

/*all functions for the admin panel*/
require_once( 'backend.php' );

/*file for adding the from to create a post*/
require_once( 'function.php' );

/*this function doesn't work in function.php so it is here*/
function pa_user_list_wqpay_link( $actions, $user_object ) {
    $new['settings'] = '<a href="' . admin_url( 'options-general.php?page=HS_sg_map_menu' ) . '">' . __( 'Settings', 'HS_sg_map' )  . '</a>';
    return array_merge( $new, $actions );
}
add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), 'pa_user_list_wqpay_link', 10, 2 );
 
/*setup functions to install and uninstall the plugin*/
function HS_sg_map_install() {
	global $wpdb;
	$table_name = $wpdb->prefix."HS_sg_map_sgs";
	$charset_collate = $wpdb->get_charset_collate();
	
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id TINYINT(9) UNSIGNED NOT NULL AUTO_INCREMENT,
		new_members TINYINT(1) UNSIGNED NOT NULL,
		about TEXT NOT NULL,
		description TEXT NOT NULL,
		url TINYTEXT NOT NULL,
		lat DECIMAL(10,8) NOT NULL,
		lng DECIMAL(11,8) NOT NULL,
		UNIQUE KEY id (id)
		) $charset_collate;";
		
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
	add_option( 'HS_sg_map_lat', 48 );
	add_option( 'HS_sg_map_lng', 8 );
	add_option( 'HS_sg_map_zoom', 9 );
}
register_activation_hook( __FILE__, 'HS_sg_map_install' );

function HS_sg_map_uninstall() {
}
register_deactivation_hook( __FILE__, 'HS_sg_map_uninstall' );