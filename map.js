var markers = [];
var map;
var infowindow;
var current_marker;
var markers_index = [];
var markers_locations = [];
var markers_content = [];
if( typeof( markers_draggable ) == 'undefined' ){
	var markers_draggable = false;
}

function drop() {
	for ( var i = 0; i < markers_index.length; i++ ) {
		addMarkerWithTimeout( markers_index[ i ], i * 100 );
	}
}

function addMarkerWithTimeout( index, timeout ) {
	window.setTimeout( function(){
		if( markers_draggable && typeof( sg_marker ) != 'undefined' && sg_marker == index ){
			var marker_draggable = true;
			var marker_color = '15AE15';
		}
		else{
			var marker_draggable = false;
			var marker_color = 'F95B5A';
		}
		
		var marker_icon = {
			url: "http://chart.apis.google.com/chart?chst=d_map_pin_letter_withshadow&chld=%E2%80%A2|" + marker_color,
			size: new google.maps.Size( 40, 37 ),
			origin: new google.maps.Point( 0, 0 ),
			anchor: new google.maps.Point( 14, 37 ),
			scaledSize: new google.maps.Size( 50, 40)
		};
		
		markers[ index ] = new google.maps.Marker({
				icon: marker_icon,
				position: markers_locations[ index ],
				map: map,
				draggable: marker_draggable,
				animation: google.maps.Animation.DROP
			});
		
		google.maps.event.addListener( markers[ index ], 'click', function(){
			if ( markers[ index ].getAnimation() != null ) {
				markers[ index ].setAnimation( null );
				infowindow.close();
			}
			else{
				if( typeof( current_marker ) != 'undefined' && current_marker !== null ){ current_marker.setAnimation( null ); }
				markers[ index ].setAnimation( google.maps.Animation.BOUNCE );
				infowindow.close();
				infowindow.setContent( markers_content[ index ] );
				infowindow.open( map, markers[ index ] );
				current_marker = markers[ index ];
			}
		});
		
		if( marker_draggable && sg_marker == index ){
			HS_bind_sg_coord( markers[ index ] );
			google.maps.event.addListener( markers[ index ], 'dragend', function(){ HS_bind_sg_coord( markers[ index ] ); } );
		}
		
		google.maps.event.addListener( infowindow, 'closeclick', function(){ markers[ index ].setAnimation( null ); });
	}, timeout);
}

function initialize(){
	infowindow = new google.maps.InfoWindow({});

	
	for ( var i = 0; i < sg_locations.length; i++ ){
		markers_index.push( sg_locations[ i ][3] );
		markers_locations[ sg_locations[ i ][3] ] = new google.maps.LatLng( sg_locations[ i ][0], sg_locations[ i ][1] );
		markers_content[ sg_locations[ i ][3] ] = sg_locations[ i ][2];
	}
	
	var map_center = new google.maps.LatLng( HS_sg_map_lat, HS_sg_map_lng );
	
	if( typeof( sg_marker ) != 'undefined' && sg_marker == -1 ){
		markers_locations.push( map_center );
		sg_marker = markers_locations.length -1;
		markers_content.push( [ '', sg_marker ] );
		markers_index.push( sg_marker );
	}
	
	var mapProp = {
		center: map_center,
		zoom: HS_sg_map_zoom
	};
	
	map = new google.maps.Map( document.getElementById( "HS_sg_map" ), mapProp );
	drop();
	
	if( markers_draggable ){
		google.maps.event.addListener( map, 'idle', HS_bind_map_coord );
	}
	
	/*****************/
	/*** SEARCHBOX ***/
	/*****************/
	// Create the search box and link it to the UI element.
	var input = /** @type {HTMLInputElement} */( document.getElementById( 'HS_sg_map_search' ) );
	
	if( typeof( input ) != undefined || input !== null ){
		map.controls[ google.maps.ControlPosition.TOP_LEFT ].push( input );
		var searchBox = new google.maps.places.SearchBox( /** @type {HTMLInputElement} */( input ) );

		// Listen for the event fired when the user selects an item from the
		// pick list. Retrieve the matching places for that item.
		google.maps.event.addListener( searchBox, 'places_changed', function(){console.log( 'fired' );
			var places = searchBox.getPlaces();

			if ( places.length == 0 ) {
				return;
			}
			
			var bounds = new google.maps.LatLngBounds();
			
			for (var i = 0, place; place = places[ i ]; i++) {
				bounds.extend( place.geometry.location );
			}
			map.fitBounds( bounds );
			
			/*var fit_bounds_listener = google.maps.event.addListener( map, "idle", function(){
				map.setZoom( 13 );
				google.maps.event.removeListener( fit_bounds_listener );
			});*///activate to adjust zoom level after every search
		});

		// Bias the SearchBox results towards places that are within the bounds of the
		// current map's viewport.
		google.maps.event.addListener( map, 'bounds_changed', function() {
			var bounds = map.getBounds();
			searchBox.setBounds( bounds );
		});
		
		input.style.display = 'block';
		/************************/
		/*** END OF SEARCHBOX ***/
		/************************/
	}
}

function loadScript() {
	var script = document.createElement( "script" );
	script.src = "http://maps.googleapis.com/maps/api/js?callback=initialize&libraries=places";
	document.body.appendChild( script );
}

window.onload = function(){
		if( typeof( document.getElementById( 'HS_sg_map' ) ) != 'undefined' && document.getElementById( 'HS_sg_map' ) !== null ){
			loadScript();
		}
	}

function HS_bind_map_coord(){
	document.getElementById( 'HS_sg_map_zoom' ).value = map.zoom;
	document.getElementById( 'HS_sg_map_lat' ).value = map.center.A;
	document.getElementById( 'HS_sg_map_lng' ).value = map.center.F;
}

function HS_bind_sg_coord( marker ){
	document.getElementById( 'HS_sg_map_sg_lat' ).value = marker.position.A;
	document.getElementById( 'HS_sg_map_sg_lng' ).value = marker.position.F;
}

function HS_prevent_submit( event ){
	if( event.keyCode == 13 ){
		event.preventDefault();
	}
}