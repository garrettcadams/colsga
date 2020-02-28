/**
 * Live Preview get Lat & Long
 *
 * @since 1.0
 * @author Wiloke
 */
(function ($) {
    "use strict";

	function decimalAdjust(type, value, exp) {
		// If the exp is undefined or zero...
		if (typeof exp === 'undefined' || +exp === 0) {
			return Math[type](value);
		}
		value = +value;
		exp = +exp;
		// If the value is not a number or the exp is not an integer...
		if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
			return NaN;
		}
		// Shift
		value = value.toString().split('e');
		value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
		// Shift back
		value = value.toString().split('e');
		return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
	}

	// Decimal round
	if (!Math.round10) {
		Math.round10 = function(value, exp) {
			return decimalAdjust('round', value, exp);
		};
	}

    $.fn.WilokeMapExtendAdmin = function (opts) {
        let self = this,
            defaults = {
                initMap: function () {},
                beforeResize: function () {},
                afterResize: function () {},
                dragMarker : function () {},
                dragEndMarker: function () {}
            },
            options = $.extend(defaults, opts);
            self.WilokeMapAdmin = {
                options : options,
	            focusDetectMyLocation: false,
	            oPlaces: null,
	            oLatLng: {
		            lat: 21.004977,
		            lng: 105.840884
	            },
                el: self,
                init: function () {
                    this.stopParseGeocode = false;
                    this.oTranslation = typeof WILOKE_GLOBAL !== 'undefined' ? WILOKE_LISTGO_TRANSLATION : {
	                    noresults: 'No results found',
	                    geocodefailed: 'Geocoder failed due to:',
                    };
                    this.getCurrentPosition();
                    this.createHTML();
                    this.createMap();
                    this.listenEvent();
	                this.usingMyLocation();
                },
                createHTML: function () {
                    this.wrapperMap = $('<div class="pi-map-wrapper"></div>');
                    this.iconMarker = $('<div class="icon-marker"></div>');
                    this.inputLocation = $('#wiloke-location');
                    this.inputLatLong  = $("#wiloke-latlong");
                    this.inputWebsite = $('#listing_website');
                    this.inputPlaceInfo = $('#wiloke-place-information');
                    this.inputPhone = $('#listing_phone');
                    this.$usingMyLocation = $('#wiloke-fill-my-location');
	                this.$listingPreview = $('.add-listing-group-preview'),
		            this.$mapPreview = $('.add-listing-group-preview-map'),
                    this.el.append(this.wrapperMap);
                    this.el.append(this.searchButton);
                    this.el.append(this.iconMarker);
                },
                mapStyle(){
                    return  new google.maps.StyledMapType([
                        {elementType: 'geometry', stylers: [{color: '#ebe3cd'}]},
                        {elementType: 'labels.text.fill', stylers: [{color: '#523735'}]},
                        {elementType: 'labels.text.stroke', stylers: [{color: '#f5f1e6'}]},
                        {
                            featureType: 'administrative',
                            elementType: 'geometry.stroke',
                            stylers: [{color: '#c9b2a6'}]
                        },
                        {
                            featureType: 'administrative.land_parcel',
                            elementType: 'geometry.stroke',
                            stylers: [{color: '#dcd2be'}]
                        },
                        {
                            featureType: 'administrative.land_parcel',
                            elementType: 'labels.text.fill',
                            stylers: [{color: '#ae9e90'}]
                        },
                        {
                            featureType: 'landscape.natural',
                            elementType: 'geometry',
                            stylers: [{color: '#dfd2ae'}]
                        },
                        {
                            featureType: 'poi',
                            elementType: 'geometry',
                            stylers: [{color: '#dfd2ae'}]
                        },
                        {
                            featureType: 'poi',
                            elementType: 'labels.text.fill',
                            stylers: [{color: '#93817c'}]
                        },
                        {
                            featureType: 'poi.park',
                            elementType: 'geometry.fill',
                            stylers: [{color: '#a5b076'}]
                        },
                        {
                            featureType: 'poi.park',
                            elementType: 'labels.text.fill',
                            stylers: [{color: '#447530'}]
                        },
                        {
                            featureType: 'road',
                            elementType: 'geometry',
                            stylers: [{color: '#f5f1e6'}]
                        },
                        {
                            featureType: 'road.arterial',
                            elementType: 'geometry',
                            stylers: [{color: '#fdfcf8'}]
                        },
                        {
                            featureType: 'road.highway',
                            elementType: 'geometry',
                            stylers: [{color: '#f8c967'}]
                        },
                        {
                            featureType: 'road.highway',
                            elementType: 'geometry.stroke',
                            stylers: [{color: '#e9bc62'}]
                        },
                        {
                            featureType: 'road.highway.controlled_access',
                            elementType: 'geometry',
                            stylers: [{color: '#e98d58'}]
                        },
                        {
                            featureType: 'road.highway.controlled_access',
                            elementType: 'geometry.stroke',
                            stylers: [{color: '#db8555'}]
                        },
                        {
                            featureType: 'road.local',
                            elementType: 'labels.text.fill',
                            stylers: [{color: '#806b63'}]
                        },
                        {
                            featureType: 'transit.line',
                            elementType: 'geometry',
                            stylers: [{color: '#dfd2ae'}]
                        },
                        {
                            featureType: 'transit.line',
                            elementType: 'labels.text.fill',
                            stylers: [{color: '#8f7d77'}]
                        },
                        {
                            featureType: 'transit.line',
                            elementType: 'labels.text.stroke',
                            stylers: [{color: '#ebe3cd'}]
                        },
                        {
                            featureType: 'transit.station',
                            elementType: 'geometry',
                            stylers: [{color: '#dfd2ae'}]
                        },
                        {
                            featureType: 'water',
                            elementType: 'geometry.fill',
                            stylers: [{color: '#b9d3c2'}]
                        },
                        {
                            featureType: 'water',
                            elementType: 'labels.text.fill',
                            stylers: [{color: '#92998d'}]
                        }
                    ], {name: 'Styled Map'});
                },
	            usingMyLocation(){
                	let self = this;
					this.$usingMyLocation.on('click', (event=>{
						this.$usingMyLocation.addClass('active');
						if (navigator.geolocation) {
							navigator.geolocation.getCurrentPosition((position)=>{
								self.geocoder.geocode({
									location: {
										lat: position.coords.latitude,
										lng: position.coords.longitude,
									}
								}, ((results, status)=>{
									if ( status === 'OK' ){
										if (results[0]) {
											self.inputLocation.attr('value', results[0].formatted_address);
											self.inputLatLong.attr('value', position.coords.latitude+','+position.coords.longitude);
											self.inputPlaceInfo.val(self.b64EncodeUnicode(JSON.stringify(results[0])));
											self.$listingPreview.css({
												'opacity': 0,
												'visibility': 'hidden'
											});

											self.$mapPreview.css({
												'opacity': 1,
												'visibility': 'visible'
											});
										}else{
											alert(self.oTranslation.noresults);
										}
									}else{
										alert(self.oTranslation.geocodefailed + ' ' + status);
									}
								}));
								self.$usingMyLocation.removeClass('active');
							});
						}
					}));

					$('body').on('gotGeocodeLocation', ((event, oGeocode)=>{
						this.focusDetectMyLocation = false;
					}));
	            },
	            detectingPosition(position){
		            this.oLatLng = {
			            lat: position.coords.latitude,
			            lng: position.coords.longitude
		            };

		            let currentLocation = window.location.href;
		            let protocal = currentLocation.search('https') !== false ? 'https' : 'http';
		            $.getJSON(protocal+'://freegeoip.net/json/', {
			            lat: position.coords.latitude,
			            lng: position.coords.longitude,
			            type: 'JSON'
		            }, (result=>{
			            let instDate = new Date();
			            localStorage.setItem('listgo_mylocation', JSON.stringify(this.oLatLng));
			            localStorage.setItem('listgo_mygeocode', JSON.stringify(result));
			            localStorage.setItem('listgo_mylocation_created_at', instDate.getMinutes());
			            $('body').trigger('gotGeocodeLocation', [{geocode: result}]);
		            }));
	            },
                getCurrentPosition: function(){
	                if ( $('#adminmenumain').length ){
                        return false;
	                }
	                let instDate = new Date();
	                let currentLocation = localStorage.getItem('listgo_mylocation'),
                        createdAt = localStorage.getItem('listgo_mylocation_created_at');

	                if ( !this.focusDetectMyLocation && !_.isNull(createdAt) && !_.isUndefined(createdAt) && (instDate.getMinutes() - createdAt) <= 30 ){
		                this.oLatLng = $.parseJSON(currentLocation);
	                }else{
		                if (navigator.geolocation) {
			                navigator.geolocation.getCurrentPosition((position)=>{
				                this.detectingPosition(position);
			                });
		                }
                    }
                },
                createMap: function () {
                    // Create default bounds
                    let defaultBounds, latDef = '', longDef = '', latLngDef, parseLatLong;
                    this.geocoder = new google.maps.Geocoder();
                    this.location = new google.maps.LatLng(this.oLatLng);

                    this.mapOptions =  {
                        center: this.location,
	                    zoom: 7,
	                    mapTypeControlOptions: {
		                    mapTypeIds: ['roadmap', 'satellite', 'hybrid', 'terrain',
			                    'styled_map']
	                    }
                    };

                    this.map = new google.maps.Map(this.wrapperMap[0], this.mapOptions);
	                this.map.mapTypes.set('styled_map', this.mapStyle());
	                this.map.setMapTypeId('styled_map');

                    // Create Marker
                    this.marker = new google.maps.Marker ({
                        map: this.map,
                        draggable: true
                    });

                    // Create info Window
                    this.infoWindow =  new google.maps.InfoWindow();

                    if ( !_.isUndefined(this.inputLatLong.val()) && !_.isEmpty(this.inputLatLong.val()) ){
                        parseLatLong = this.inputLatLong.val().split(',');
                        latDef  = parseLatLong[0];
                        longDef = parseLatLong[1];
                    }

                    if ( latDef === '' || longDef === '' )
                    {
                        latLngDef = new google.maps.LatLng(this.oLatLng);
                        defaultBounds = new google.maps.LatLngBounds(
                            latLngDef,
                            latLngDef
                        );
                    }else{
                        latLngDef = new google.maps.LatLng(latDef, longDef);
                        defaultBounds = new google.maps.LatLngBounds(
                            latLngDef,
                            latLngDef
                        );

                        this.marker.setPosition(latLngDef);
                        this.infoWindow.setContent(latDef + ',' + longDef);
                        this.infoWindow.open(this.map, this.marker);
                    }

                    this.map.fitBounds(defaultBounds);

                    // Create the search box and link it to the UI element.

                    /*pirateS*/
                    this.search = new google.maps.places.SearchBox(document.getElementById('wiloke-location'));
					// Callback beforeResize
                    options.initMap(this.el, options);
                    // End Callback
                },
                setPhone: function (place) {
					if ( this.inputPhone.length && this.inputPhone.val() === '' ){
						this.inputPhone.val(place.international_phone_number);
					}
                },
	            setWebsite: function (place) {
		            if ( this.inputWebsite.length && this.inputWebsite.val() === '' ){
			            this.inputWebsite.val(place.website);
		            }
	            },
	            b64EncodeUnicode: function(str) {
				    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
					    return String.fromCharCode('0x' + p1);
				    }));
			    },
                setPlaceInfo: function (place) {
	                if ( this.inputPlaceInfo.length ){
		                this.inputPlaceInfo.val(this.b64EncodeUnicode(JSON.stringify(place)));
	                }
                },
                geocodePosition: function(pos) {
                    let _this = this;

                    if ( _this.stopParseGeocode ){
                        _this.stopParseGeocode = false;
                        return;
                    }

                    _this.geocoder.geocode({
                        latLng: pos
                    }, function(responses) {
                        if (responses && responses.length > 0) {
                            _this.marker.formatted_address = responses[0].formatted_address;
                        } else {
                            _this.marker.formatted_address = 'Cannot determine address at this location.';
                        }

                        _this.inputLocation.val(_this.marker.formatted_address);
                    });
                },
                listenEvent : function () {
                    let _this = this;

                    _this.inputLocation.on('updateLatLng', function (event, action) {
                        if (_this.location && action && action.changeLocation === true) {
                            let lat = Math.round10(_this.location.lat(), -4),
                                long = Math.round10(_this.location.lng(), -4);
                            _this.inputLatLong.attr('value', lat+','+long);

	                        _this.resize();
                            if ( !_.isEmpty(_this.oPlaces) ){
	                            _this.setPhone(_this.oPlaces);
	                            _this.setWebsite(_this.oPlaces);
	                            _this.setPlaceInfo(_this.oPlaces);
                            }
                        }
                    });

                    // Listen for the event fired when the user selects an item from the
                    // pick list. Retrieve the matching places for that item.
                    google.maps.event.addListener(_this.search, 'places_changed', function() {
                        let places = _this.search.getPlaces();
                        if (places.length === 0) {
	                        _this.location = places[0].geometry.location;
	                        _this.oPlaces = places[0];
                            return;
                        }

                        // For each place, get the icon, place name, and location.
                        let bounds = new google.maps.LatLngBounds();
                        for (let i = 0, place; place = places[i]; i++) {
                            bounds.extend(place.geometry.location);
                            _this.location = place.geometry.location;
                            _this.oPlaces = place;
                        }

                        _this.map.fitBounds(bounds);
                        _this.marker.setPosition(_this.location);
                        _this.stopParseGeocode = true;
                        google.maps.event.trigger (_this.marker, 'dragend');
                        /*Add Location: pirateS*/
                    });

                    // Bias the SearchBox results towards places that are within the bounds of the
                    // current map's viewport.
                    google.maps.event.addListener(_this.map, 'bounds_changed', function() {
                        let bounds = _this.map.getBounds();
                        _this.search.setBounds(bounds);
                    });

                    google.maps.event.addListener(_this.marker, 'dragend', function() {
                        options.dragMarker();
                    });

                    google.maps.event.addListener(_this.marker, 'dragend', function() {
                        _this.location = _this.marker.getPosition();
                        _this.geocodePosition(_this.location);
                        let lat = Math.round10(parseFloat(_this.location.lat()), -4),
	                        long = Math.round10(parseFloat(_this.location.lng()), -4);

                        _this.infoWindow.setContent(lat + ', ' + long);
                        _this.infoWindow.open(_this.map, _this.marker);
                        options.dragEndMarker();
	                    _this.inputLocation.trigger('updateLatLng', {changeLocation: true});
                    });

                    google.maps.event.addDomListener(window, 'resize', function() {
                        _this.resize();
                    });
                },
                resize : function () {
                    // Callback beforeResize
                    options.beforeResize(this.el, options);
                    // End Callback

                    google.maps.event.trigger(this.map, "resize");
                    this.map.setCenter(this.location);

                    // Callback afterResize
                    options.afterResize(this.el, options);
                    // End Callback afterResize
                }
            };
            self.WilokeMapAdmin.init();
    };

    $(window).load(function(){
        let $wilokeMap = $('#wiloke-map');
        if ( $wilokeMap.length ){
	        $wilokeMap.WilokeMapExtendAdmin({
		        initMap: function () {},
		        beforeResize: function () {},
		        afterResize: function () {},
		        dragMarker : function () {},
		        dragEndMarker: function () {}
	        });

	        $(window).trigger('resize');
        }

    })
})(jQuery);