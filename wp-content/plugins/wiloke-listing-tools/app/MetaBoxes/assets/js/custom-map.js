(function( $ ) {
	'use strict';

	var maps = [];

	$(document).ready(function () {
		$( '.cmb-type-wiloke-map' ).each( function() {
			initializeMap( $( this ) );
		});
	});


	var xhrTimezone = null;
	function getTimezone(lat, lng) {
		if ( xhrTimezone !== null && xhrTimezone.status !== 200 ){
			xhrTimezone.abort();
		}

		xhrTimezone = jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'wilcity_get_timezone_by_latlng',
				latLng: lat + ',' + lng
			},
			success: function (response) {
				if ( response.success ){
					$('[name="wilcity_timezone"]').val(response.data);
					$('[name="timezone"]').val(response.data);
				}
			}
		})
	}

	function initializeMap( mapInstance ) {
		var searchInput = mapInstance.find( '.pw-map-search' );
		var mapCanvas = mapInstance.find( '.pw-map' );
		var latitude = mapInstance.find( '.pw-map-latitude' );
		var longitude = mapInstance.find( '.pw-map-longitude' );
		var latLng = new google.maps.LatLng( 54.800685, -4.130859 );
		var zoom = 5;

		// If we have saved values, let's set the position and zoom level
		if ( latitude.val().length > 0 && longitude.val().length > 0 ) {
			latLng = new google.maps.LatLng( latitude.val(), longitude.val() );
			zoom = 17;
		}

		// Map
		var mapOptions = {
			center: latLng,
			zoom: zoom
		};
		var map = new google.maps.Map( mapCanvas[0], mapOptions );

		// Marker
		var markerOptions = {
			map: map,
			draggable: true,
			title: 'Drag to set the exact location'
		};
		var marker = new google.maps.Marker( markerOptions );

		if ( latitude.val().length > 0 && longitude.val().length > 0 ) {
			marker.setPosition( latLng );
		}

		// Search
		var autocomplete = new google.maps.places.Autocomplete( searchInput[0] );
		autocomplete.bindTo( 'bounds', map );

		var geocoder = new google.maps.Geocoder();

		google.maps.event.addListener( autocomplete, 'place_changed', function() {
			var place = autocomplete.getPlace();
			if ( ! place.geometry ) {
				return;
			}

			if ( place.geometry.viewport ) {
				map.fitBounds( place.geometry.viewport );
			} else {
				map.setCenter( place.geometry.location );
				map.setZoom( 17 );
			}

			marker.setPosition( place.geometry.location );

			latitude.val( place.geometry.location.lat() );
			longitude.val( place.geometry.location.lng() );
			getTimezone(place.geometry.location.lat(), place.geometry.location.lng());
		});

		$( searchInput ).keypress( function( event ) {
			if ( 13 === event.keyCode ) {
				event.preventDefault();
			}
		});

		// Allow marker to be repositioned
		google.maps.event.addListener( marker, 'dragend', function() {
			geocoder.geocode({latLng: marker.getPosition()}, function(responses) {
				if (responses && responses.length > 0) {
					searchInput.val(responses[0].formatted_address);
				}
			});

			latitude.val(marker.getPosition().lat());
			longitude.val(marker.getPosition().lng());
			getTimezone(marker.getPosition().lat(), marker.getPosition().lng());
		});

		maps.push( map );
	}

	// Resize map when meta box is opened
	if ( typeof postboxes !== 'undefined' ) {
		postboxes.pbshow = function () {
			var arrayLength = maps.length;
			for (var i = 0; i < arrayLength; i++) {
				var mapCenter = maps[i].getCenter();
				google.maps.event.trigger(maps[i], 'resize');
				maps[i].setCenter(mapCenter);
			}
		};
	}

	// When a new row is added, reinitialize Google Maps
	$( '.cmb-repeatable-group' ).on( 'cmb2_add_row', function( event, newRow ) {
		var groupWrap = $( newRow ).closest( '.cmb-repeatable-group' );
		groupWrap.find( '.cmb-type-pw-map' ).each( function() {
			initializeMap( $( this ) );
		});
	});

})( jQuery );