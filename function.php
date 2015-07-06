<?php
defined( 'ABSPATH' ) or die( 'Please return to the main page' );

function HS_sg_map_admin_scripts() {
	wp_enqueue_media();
	wp_register_script('HS_sg_map_js', plugin_dir_url( __FILE__ ).'/map.js', array(), '1', true );
	wp_enqueue_script('HS_sg_map_js');
	wp_enqueue_style( 'HS_sg_map_style', plugins_url( '/style.css', __FILE__ ) );
	wp_register_script('HS_sg_map_uploader', plugin_dir_url( __FILE__ ).'/uploader.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('HS_sg_map_uploader');
}

if (isset($_GET['page']) && $_GET['page'] == 'HS_sg_map_menu') {
	add_action('admin_print_scripts', 'HS_sg_map_admin_scripts');
}

function HS_sg_map_scripts(){
	wp_register_script('HS_sg_map_js', plugin_dir_url( __FILE__ ).'/map.js', array(), '1', true );
	wp_enqueue_script('HS_sg_map_js');
	wp_enqueue_style( 'HS_sg_map_style', plugins_url( '/style.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'HS_sg_map_scripts' );

function HS_sg_map_menu_add() {
	add_options_page( __( 'Smallgroup Map', 'HS_sg_map' ), __( 'Smallgroup Map', 'HS_sg_map' ), 'edit_pages', 'HS_sg_map_menu', 'HS_sg_map_menu' );
}
add_action( 'admin_menu', 'HS_sg_map_menu_add' );

function HS_is_lat( $lat ){
	if( is_numeric( $lat ) && $lat >= -90 && $lat <= 90 ){ return true; }
	return false;
}

function HS_is_lng( $lng ){
	if( is_numeric( $lng ) && $lng >= -180 && $lng <= 180 ){ return true; }
	return false;
}