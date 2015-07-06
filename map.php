<?php
defined( 'ABSPATH' ) or die( 'Please return to the main page' );

function HS_sg_map_frontend( $args ){
	if( isset( $args['width'] ) && is_numeric( $args['width'] ) ){
		$width = esc_attr( $args['width'] ) . 'px';
	}
	else{
		$width = '750px';
	}
	
	if( isset( $args['height'] ) && is_numeric( $args['height'] ) ){
		$height = esc_attr( $args['height'] ) . 'px';
	}
	else{
		$height = '400px';
	}
	
	global $wpdb;
	$table_name = $wpdb->prefix."HS_sg_map_sgs";
	$array = $wpdb->get_results( "SELECT * FROM $table_name", 'ARRAY_A' );
	$sg_locations = 'var sg_locations = [';
	
	foreach( $array as $row ){
		$content = '<img style="max-height:200px;" src="' . esc_url( $row['url'] ) . '"><div><h3>' . __( 'About Us', 'HS_sg_map' ) . '</h3>' . stripslashes( preg_replace('/\s+/', ' ', $row['about'] ) ) . '</div><div><h3>' . __( 'Description', 'HS_sg_map' ) . '</h3>' . stripslashes( preg_replace('/\s+/', ' ', $row['description'] ) ) . '</div>';
		$sg_locations .= '[' . esc_attr( $row['lat'] ) . ', ' . esc_attr( $row['lng'] ) . ', "' . addslashes( $content ) . '", ' . esc_attr( $row['id'] ) . '], ';
	}
	$sg_locations .= '];';
	
	$return =	'<script>' . $sg_locations . '
					var HS_sg_map_zoom = ' . esc_attr( get_option( 'HS_sg_map_zoom' ) ) . ';
					var HS_sg_map_lat = ' . esc_attr( get_option( 'HS_sg_map_lat' ) ) . ';
					var HS_sg_map_lng = ' . esc_attr( get_option( 'HS_sg_map_lng' ) ) . ';
				</script>';
	$return .=	'<input id="HS_sg_map_search" onkeydown="HS_prevent_submit( event )" class="controls" type="text">
				<div id="HS_sg_map" onload="activate_loadScript();" style="width:' . $width . '; height:' . $height . ';"><noscript><div class="error">' . __( 'Javascript is required to load the map!', 'HS_sg_map' ) . '</div></noscript></div>
				<script src="' . plugins_url( '/include/spin.js', __FILE__ ) . '"></script>
				<script>
					var opts = {
						  lines: 17 // The number of lines to draw
						, length: 40 // The length of each line
						, width: 15 // The line thickness
						, radius: 60 // The radius of the inner circle
						, scale: 1 // Scales overall size of the spinner
						, corners: 1 // Corner roundness (0..1)
						, color: "#F61111" // #rgb or #rrggbb or array of colors
						, opacity: 0.25 // Opacity of the lines
						, rotate: 0 // The rotation offset
						, direction: 1 // 1: clockwise, -1: counterclockwise
						, speed: 1.8 // Rounds per second
						, trail: 60 // Afterglow percentage
						, fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
						, zIndex: 2e9 // The z-index (defaults to 2000000000)
						, className: "HS_spinner" // The CSS class to assign to the spinner
						, top: "50%" // Top position relative to parent
						, left: "50%" // Left position relative to parent
						, shadow: true // Whether to render a shadow
						, hwaccel: false // Whether to use hardware acceleration
						, position: "absolute" // Element positioning
					}
					
					var target = document.getElementById( "HS_sg_map" )
					var spinner = new Spinner( opts ).spin( target );
				</script>';
	if( current_user_can( 'publish_posts' ) && isset( $_GET['page'] ) && $_GET['page'] == 'HS_sg_map_menu' ){
		if( isset( $args['selected_sg'] ) && is_numeric( $args['selected_sg'] ) ){
			$sg_marker = 'var sg_marker = "' . esc_attr( $args['selected_sg'] ) . '";';
			$sg_locations_html = '<input type="hidden" name="HS_sg_map_sg_lat" id="HS_sg_map_sg_lat"><input type="hidden" name="HS_sg_map_sg_lng" id="HS_sg_map_sg_lng">';
		}
		else{
			$sg_marker = '';
			$sg_locations_html = '';
		}
		$return .= '<script>var markers_draggable = true;' . $sg_marker . '</script>
					<input type="hidden" name="HS_sg_map_lat" id="HS_sg_map_lat">
					<input type="hidden" name="HS_sg_map_lng" id="HS_sg_map_lng">
					<input type="hidden" name="HS_sg_map_zoom" id="HS_sg_map_zoom">
					<input type="checkbox" name="HS_sg_map_change_coords">' . __( 'Save Map Section as Default', 'HS_sg_map' );
		$return .= $sg_locations_html;
	}
	return $return;
}
add_shortcode( 'HS_sg_map', 'HS_sg_map_frontend' );