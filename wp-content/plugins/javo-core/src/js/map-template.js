/**
 *
 * Map Template Script
 * @author javo Themes
 * @since 1.0.0
 * @description Lava Direction Map Template Script
 *
 */

(function ($) {
	"use strict";

	if( typeof jvbpd_core_map_param == 'undefined' ) {
		return;
	}

	var itemManager = function(data) {
		this.items = data || new Array();
	}

	itemManager.prototype.consturctor = itemManager;
	itemManager.prototype.add = function(data) {
		this.items.push(data);
	}
	itemManager.prototype.set = function(data) {
		this.items = data;
	}
	itemManager.prototype.unique = function() {
		var tmp = new Array();
		var res = new Array();
		$.each(this.items, function(itemIndex, item){
			if(!tmp[item.post_id]){
				tmp[item.post_id] = true;
				res.push(item);
			}
		});
		this.items = res;
	}

	var
		BTN_OK = $('[javo-alert-ok]').val(),
		ERR_LOC_ACCESS = jvbpd_core_map_param.strLocationAccessFail,
		NOW_ADDRESS_SEARCH = 'now-address-search',
		main_filters_func = function() {},
		main_filters = new main_filters_func;

	main_filters_func.prototype.filters = {};
	main_filters_func.prototype.clear = function() {
		this.filters = {};
	}
	main_filters_func.prototype.add = function(key, val) {
		if(typeof this.filters[key]=='undefined') {
			this.filters[key] = [];
		}
		this.filters[key].push(val);
	}
	main_filters_func.prototype.unique = function() {
		var self = this;
		$.each(self.filters, function(taxonomy, terms){
			self.filters[taxonomy] = terms.filter(function(val, ind, $this){
				return $this.indexOf(val) === ind;
			});
		});
	}

	window.jvbpd_map_box_func = {
		zoom_lvl_once : false,
		options: {

			// Lava Configuration
			config: {
				items_per: $('[name="javo-box-map-item-count"]').val()
			},

			// Google Map Parameter Initialize
			map_init: {
				map: {
					options: {
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						mapTypeControlOptions: {
							position: google.maps.ControlPosition.RIGHT_TOP
						},
						gestureHandling: 'cooperative',
						/* greedy, cooperative */
						scrollwheel: false,
						panControl: false,
						zoomControl: false,
						mapTypeControl: false,
						scaleControl: false,
						streetViewControl: false,
						rotateControl: false,
						fullscreenControl: false,
					},
					events: {
						click: function () {
							var obj = window.jvbpd_map_box_func;
							obj.close_ib_box();
						}
					}
				},
				_blank_panel: {
					options: {
						content: $('#jvbpd-map-inner-panel').html()
					}
				}
			},

			// Google Point Of Item(POI) Option
			map_style: [{
				featureType: "poi",
				elementType: "labels",
				stylers: [{
					visibility: "off"
				}]
			}]
		}, // End Options

		variable: {
			top_offset: parseInt($('header > nav').outerHeight() || 0) +
				parseInt($('#wpadminbar').outerHeight() || 0)

			// Topbar is entered into Header Navigation.
			// + $('.javo-topbar').outerHeight()

		}, // End Define Variables

		getOptions: function () {
			// Ajax
			this.ajaxurl = obj.args.ajaxurl;
			this.click_on_marker_zoom = parseInt(obj.args.marker_zoom_level) || false;
		},

		// Lava Maps Initialize
		init: function () {

				/*
				 *	Initialize Variables
				 */
				var obj = this;

				this.args = jvbpd_core_map_param;
				this.archive_filter_once = false;
				this.addressFiltered = false;
				this.filtering = false;

				this.getOptions();

				// Map Element
				this.el = $('.javo-maps-area');
				this.panel = $('.javo-maps-panel-wrap');

				if (!this.el.length) {
					this.el = $('<div>').addClass('javo-maps-area').appendTo('body');
				}

				if( this.el.data('cluster-radius') ) {
					this.args.cluster_level = this.el.data('cluster-radius');
				}

				// Google Map Bind
				this.el.gmap3(this.options.map_init);
				this.map = this.el.gmap3('get');

				$('.javo-maps-area-wrap').each(function () {
					var
						$this = $(this),
						settings = $this.data('settings'),
						mapInst = $('.javo-maps-area', $this).gmap3('get');

					if (settings.google_map_style) {
						mapInst.setOptions({
							styles: JSON.parse(settings.google_map_style)
						});
					} else {
						mapInst.setOptions({
							styles: obj.getStyles()
						});
					}

					if (settings.map_marker.url) {
						obj.args.map_marker = settings.map_marker.url;
					}

					if (0 < parseInt(settings.map_init_zoom)) {
						obj.args.map_zoom = parseInt(settings.map_init_zoom);
					}

					obj.args.first_filter = {};
					if (settings.first_filter) {
						obj.args.first_filter = settings.first_filter;
					}

				});

				// MouseWheel
				/*
				if( obj.args.allow_wheel )
					this.map.setOptions({ scrollwheel : true });
				*/

				//this.tags = $('[javo-map-all-tags]').val().toLowerCase().split( '|' );
				this.tags = new Array();

				// Layout
				this.layout();

				// Trigger Resize
				this.resize();

				// Setup Distance Bar
				this.setDistanceBar();
				this.menuFilters();

				// Hidden Footer
				$('.container.footer-top').remove();

				// Set Google Information Box( InfoBubble )
				this.setInfoBubble();

				// this.bindSearchShortcode();

				var
					is_cross_domain = obj.args.cross_domain == '1',
					json_hook = obj.args.json_hook,
					json_ajax_url = obj.args.json_file,
					json_security = obj.args.json_security,
					parse_json_url = json_ajax_url;

				if (is_cross_domain) {
					parse_json_url = this.ajaxurl;
					parse_json_url += "?action=" + json_hook;
					parse_json_url += "&fn=" + json_ajax_url.replace('.json', '');
					parse_json_url += "&security=" + json_security;
					parse_json_url += "&callback=?";
				}

				this.bindMobile();
				this.mapTriggerControls();

				// Events
				;
				$(document)
					.on('click', '.javo-hmap-marker-trigger', this.marker_on_list)
					//.on( 'change'	, 'select[name^="filter"]', this.filter_trigger )
					.on('click', '[data-javo-map-load-more]', this.load_more())
					// .on( 'click', 'button[name="map_order[order]"]', this.order_switcher() )
					// .on( 'change', '.module-switcher input[name="module_switcher"]', this.module_switcher() )
					.on('click', '[data-menu-filter] [data-value]', this.menu_filter())
					.on('click', '.javo-map-filter-order > label.btn', this.order_switcher())
					.on('change', 'input[name="map_order[orderby]"]', function () {
						obj.filter();
					})
					.on('keypress', '#javo-map-box-auto-tag', this.keyword_)
					.on('click', '[data-map-move-allow]', this.map_locker)
					.on('click', '#javo-map-box-search-button', this.search_button)
					.on('click', '.javo-my-position', this.getMyPosition())
					.on('click', '.current-search', this.currentBoundSearch())
					.on('click', '.reset-filter', this.filter_reset())
					.on('click', '.jvbpd-map-mobile-switch', this.mobile_type_switcher())
					.on('getMyPosition', this.getMyPosition())
					.on('keydown', '#javo-map-box-location-ac', this.setGetLocationKeyword)
					.on('keydown', '#javo-map-box-location-trigger', this.trigger_location_keyword())
					// .on( 'change', 'select[name^="list_filter"]', this.trigger_selectize_filter() )
					.on('javo:filter_reset', this.filter_reset())
					.on('javo:map_refresh', function () {
						obj.filter();
					})
					.on('javo:doing', this.doing())
					.on('javo:map_pre_get_lists', this.control_manager(true))
					.on('javo:map_get_lists_after', this.control_manager(false))
					.on('javo:map_info_window_loaded', this.info_window_events())
					.on('javo:marker_restore', this.marker_restore())
					.on('javo:marker_release', this.marker_release())

				;
				$(window)
					.on('resize', this.resize)
					.on('javo:init', this.setAutoComplete())
					// .on( 'javo:on_map_type_switched', this.map_type_switch() )
					// .on('javo:map/filter/before', this.control_manager(true))
					.on('javo:map/items/get/before', this.getItemBefore())
					.on('javo:map/items/get/after', this.getItemAfter())
					.on('javo:map/set_marker/after', this.setMapZoom());

				if ($('[data-first-featured]').data('first-featured')) {
					obj.args.panel_list_featured_first = 'enable';
				}

				$(window).trigger('javo:init', obj);

				obj.getListContentsProcess = false;

				// DATA
				$.getJSON(parse_json_url, function (response) {
						obj.items = response;
						obj.bindSearchShortcode();
						$.each(response, function (index, key) {
							obj.tags.push(key.post_title);
						});

						obj.setKeywordAutoComplete();
						$(document.body).trigger('javo:json_loaded', obj.items);
						// $(window).trigger('javo:json/itesm/all/load', obj.items);
						$(window).trigger('javo:json/itesm/all/load');
						if('' != $('#javo-map-box-location-trigger').val()) {
							var eventhandle = $.Event('keydown');
							eventhandle.keyCode = 13;
							$('#javo-map-box-location-trigger').trigger(eventhandle);
						}
					})
					.fail(function (xhr) {
						//console.log( xhr.responseText );
						obj.items = [];
						obj.filter();
					});

			} // End Initialize Function

			,
		clear_map: function () {
			//
			this.el.gmap3({
				clear: {
					name: ['marker', 'circle']
				}
			});
			this.close_ib_box();

		},

		getItem: function (post_id) {
			var result = false;
			$.each(this.items, function (itemIndex, itemData) {
				if (itemData.post_id == post_id) {
					result = itemData;
				}
			});
			return result;
		},

		close_ib_box: function () {
			if (typeof this.infoWindo != "undefined") {
				this.infoWindo.close();
			}
		},

		filter_trigger: function (e) {
			var obj = window.jvbpd_map_box_func;
			obj.filter();
		},

		trigger_selectize_filter: function () {
			var obj = this;
			return function () {
				var taxonomy = $(this).data('tax');
				if (taxonomy) {
					var sel = $(".javo-maps-search-wrap [name='map_filter[" + taxonomy + "]']").get(0);
					if (typeof sel !== 'undefined' && typeof sel.selectize !== 'undefined') {
						sel.selectize.setValue($(this).val());
					}
					main_filters.add(taxonomy, $(this).val());

				}
			}
		},

		map_type_switch: function () {
			var self = this;
			return function (event, type) {
				$('body').css('overflow', 'hidden');
				if (type == 'listings') {
					$('body').css('overflow-y', 'scroll');
				}
			}
		},

		getItemBefore: function () {
			return function (event, args) {
				args.controls.loadMore.prop('disabled', true).find('i').removeClass('hidden');
				args.controls.loadMore.removeClass('hidden');
				args.controls.list.output.addClass('ajax-processing');
			}
		},

		getItemAfter: function () {
			var self = this;
			return function (event, args) {
				$(document).trigger('lava:favorite');
				args.controls.loadMore.prop('disabled', false).find('i').addClass('hidden');
				self.moduleLink();
				if (!self.DisableListTypeLoaderEffect) {
					args.controls.list.output.removeClass('ajax-processing');
				}
				$('.jv-meta-distance').closest('.media-badges').addClass('hidden');
				if(self.geoLocationfilter) {
					$('.jv-meta-distance').removeClass('hidden').closest('.media-badges').removeClass('hidden');
				}
			}
		},

		setMapZoom : function() {
			var self = this;
			return function(event, map, option){
				var
					ZOOMLEVEL = parseInt(self.args.map_zoom || 16),
					MARKERLENGTH = option.marker.values.length,
					listner = google.maps.event.addListener(map, 'idle', function(){
						if(MARKERLENGTH<=1) {
							map.setZoom(ZOOMLEVEL);
						}
						google.maps.event.removeListener(listner);
					});
			}
		},

		layout: function () {
			var
				self = this,
				panels = {};

			// POI Setup
			if ($('[name="jvbpd_google_map_poi"]').val() == "off") {
				// Map Style
				this.map_style = new google.maps.StyledMapType(this.options.map_style, {
					name: 'Lava Box Map'
				});
				this.map.mapTypes.set('map_style', this.map_style);
				this.map.setMapTypeId('map_style');
			}

		}, // End Set Layout

		resize: function () {

			var
				obj = window.jvbpd_map_box_func,
				winX = $(window).width(),
				winY = 0,
				body = $('html body'),
				adminbarH = $('#wpadminbar').outerHeight(true);

			// winY += $('header.main').outerHeight(true);
			winY += $('nav.navbar-static-top').outerHeight(true);
			winY += $('#javo-maps-listings-switcher').outerHeight(true);
			winY += adminbarH;

			/*
			if( ! $('.javo-maps-area-wrap').hasClass( 'top-bottom-layout' ) ) {
				$('.javo-maps-area-wrap').css( 'top', winY );
				// $('.javo-maps-search-wrap' ).css( 'top', winY );
				// $('.javo-maps-panel-wrap').css( 'top', winY);
				$( '#javo-maps-wrap' ).css( 'top', winY);
			} */

			$('body').css('paddingTop', adminbarH + 'px');
			$(document).trigger('javo:map_template_reisze', winX);

		}, // End Responsive( Resize );

		setDistanceBar: function () {
			var
				self = this,
				args = self.args,
				sliderArgs = {
					elements: $('.javo-geoloc-slider, .javo-geoloc-slider-trigger'),
					unit: args.distance_unit || 'km',
					distance: 1000,
					max: args.distance_max || 50,
					final_max: 50,
					step: parseInt(args.distance_max) / 100,
					current: 3,
					getParams: function () {
						return {
							start: this.current,
							step: this.step,
							connect: 'lower',
							range: {
								'min': 0,
								'max': this.final_max
							},
							serialization: {
								lower: new Array(
									$.Link({
										target: '-tooltip-<div class="javo-slider-tooltip"></div>',
										method: function (v) {
											var template = '<span>{current_distance} {distance_unit}</span>';
											template = template.replace(/{current_distance}/g, v);
											template = template.replace(/{distance_unit}/g, sliderArgs.unit);
											$(this).html(template);
										},
										format: {
											decimals: 0,
											thousand: ',',
											encoder: function (value) {
												return parseFloat(value) / sliderArgs.distance;
											}
										}
									})
								)
							}
						};
					},
					events: {
						getLocation: function (control) {
							var obj = sliderArgs;
							return function (results) {

								var current_distance = parseInt($(control).val());

								if (!results) {
									alert(ERR_BED_ADDRESS);
									return false;
								};

								var
									latlng = results[0].geometry.location,
									result = [],
									data = self.items,
									geo_input = $('.javo-location-search');

								self.address = {};
								self.address.latLng = latlng;

								$(control).parent().addClass('open');

								self.items = self.apply_dist(self.items);

								$.each(self.items, function (i, k) {
									var c = self.setCompareDistance(new google.maps.LatLng(k.lat, k.lng), latlng);
									if ((c * obj.distance) <= current_distance) {
										result.push(data[i]);
									}
								});

								result.sort(self.compare('dist'));
								var $map = $(this);
								var nTimeId = setInterval(function(){
									self.map_clear(false);
									self.el.gmap3({
										clear: {
											name: 'circle'
										}
									});
									self.addressFiltered = true;
									window.__LAVA_MAP_BOX_TEMP__ = result;
									self.filter(result);

									$map.gmap3({
										circle: {
											options: {
												center: latlng,
												radius: current_distance,
												fillColor: '#2099CD',
												strokeColor: '#1A759C'
											}
										}
									}, {
										get: {
											name: 'circle',
											callback: function (c) {
												$map.gmap3('get').fitBounds(c.getBounds());
											}
										}
									});

									clearInterval(nTimeId);
								}, 300);
								/*
								self.addressFiltered = true;
								self.filter(result);
								self.map_clear(false);
								self.el.gmap3({
									clear: {
										name: 'circle'
									}
								});

								$(this).gmap3({
									circle: {
										options: {
											center: latlng,
											radius: current_distance,
											fillColor: '#2099CD',
											strokeColor: '#1A759C'
										}
									}
								}, {
									get: {
										name: 'circle',
										callback: function (c) {
											$(this).gmap3('get').fitBounds(c.getBounds());
										}
									}
								}); */
							}
						},
						set: function () {
							var obj = this;
							return function (event, value) {
								var
									element = this,
									address = $('[name="radius_key"]').val();

								if ($(this).data('list-type')) {
									address = $('#javo-map-box-location-trigger').val();
								}

								$('#javo-maps-listings-wrap').addClass(NOW_ADDRESS_SEARCH);

								if (!address) {
									self.getMyPosition().call();
									$('#javo-maps-listings-wrap').removeClass(NOW_ADDRESS_SEARCH);
									return false;
								}

								// console.log(sliderArgs.elements);
								sliderArgs.elements.each(function(){
									var $slider = $(this);
									$(this).val(value);
								})

								self.el.gmap3({
									getlatlng: {
										'address': address,
										callback: obj.getLocation(this)
									}
								});

							}
						}
					}
				};

			sliderArgs.elements.each(function () {
				var
					$this = $(this),
					data_current = 0;

				if ($this.hasClass('noUi-target')) {
					return true;
				}

				if ($this.data('unit')) {
					sliderArgs.unit = $this.data('unit');
				}

				if ($this.data('max')) {
					sliderArgs.max = $this.data('max');
				}

				sliderArgs.distance = sliderArgs.unit == 'km' ? 1000 : 1609.344;
				sliderArgs.final_max = parseInt(sliderArgs.max) * sliderArgs.distance;
				sliderArgs.step = parseInt(sliderArgs.final_max) / 100;
				sliderArgs.current = sliderArgs.distance * 10;

				if ($this.data('current')) {
					data_current = parseInt($this.data('current'));
					sliderArgs.current = sliderArgs.distance * data_current;
				}

				if ($this.is('.javo-geoloc-slider-trigger')) {
					$this.data('list-type', true);
				}
				$this
					.noUiSlider(sliderArgs.getParams())
					.css('margin', '15px 0')
					.on('set', sliderArgs.events.set());
			});

		}, // End Setup Distance noUISlider

		setAutoComplete: function () {
			var self = this;
			var radiusKey = '.javo-location-search, .field-google [name="radius_key"]';
			return function () {
				$(radiusKey).each(function (i, element) {
					var google_ac = new google.maps.places.Autocomplete(element);
					google_ac.addListener('place_changed', function () {
						var instance = self.distancebar_in_search();
						$(radiusKey).val($(element).val());
						instance();
						$('.javo-geoloc-slider').trigger('set');
					});
				});
			}

		}, // End Setup AutoComplete  Selectize

		map_locker: function (e) {
			e.preventDefault();
			var
				obj = window.jvbpd_map_box_func,
				isLock = $(this).hasClass('active'),
				strLock = $(this).data('lock'),
				strUnlock = $(this).data('unlock');

			if ( isLock ) {
				// Allow
				obj.map.setOptions({scrollwheel:true});
				$(this).find('i').removeClass('fa fa-lock').addClass('fa fa-unlock');
				$(this).prop('title', strLock);
				$(this).removeClass('active');
			}else{
				// Not Allowed
				obj.map.setOptions({scrollwheel:false});
				$(this).find('i').removeClass('fa fa-unlock').addClass('fa fa-lock');
				$(this).prop('title', strUnlock);
				$(this).addClass('active');
			}
		},

		/** GOOGLE MAP TRIGGER				*/
		setInfoBubble: function () {
			this.infoWindo = new InfoBubble({
				minWidth: 320,
				minHeight: 190,
				overflow: true,
				shadowStyle: 1,
				padding: 0,
				borderRadius: 5,
				arrowSize: 20,
				borderWidth: 0,
				disableAutoPan: false,
				hideCloseButton: false,
				arrowPosition: 50,
				arrowStyle: 0
			});
		}, // End Setup InfoBubble

		search_button: function (e) {
			e.preventDefault();
			var obj = window.jvbpd_map_box_func;
			if ($("#javo-map-box-location-ac").val()) {
				$('.javo-geoloc-slider').trigger('set');
			} else {
				obj.filter();
			}
		},

		bindSearchShortcode: function () {
			var
				self = this,
				las_instance,
				current_category,
				container_exists = $('.lv-map-template').length,
				container = container_exists ? $('.lv-map-template') : $('.navbar-static-top'),
				searchbar = $('.jvbpd-search-form-section', container),
				form = $('form.jvbpd-search-form-section', container),
				select_filters = $('select[data-tax]', searchbar),
				address_filter = $('input[name="radius_key"]', searchbar),
				keyword_filter = $('.field-keyword input[name="keyword"]', searchbar),
				category_filter = $('.field-ajax_search input[name="category"]', searchbar),
				ajax_search_filter = $('.field-ajax_search input[data-search-input]', searchbar);

			self.searchbar = searchbar;
			self.el_keyword = keyword_filter;

			keyword_filter.on('keypress', function(event){
				if (event.keyCode == 13) {
					self.filter();
				}
			});

			form.on('submit', function (e) {
				e.preventDefault();
				self.address = false;
				if (address_filter.val()) {
					var callback = self.distancebar_in_search();
					callback();
					$('.javo-geoloc-slider').trigger('set');
				} else {
					self.filter();
				}
			});

			ajax_search_filter.on('keypress', function (event) {
				if (event.keyCode == 13) {
					if (current_category != keyword_filter.val()) {
						category_filter.val('');
					}

					current_category = keyword_filter.val();
					las_instance = ajax_search_filter.closest('.lava-ajax-search-form-wrap').data('las-instance') || {};

					if (las_instance) {
						las_instance.el.addClass('ajax-loading');
						las_instance.cache().getResults(current_category, function (xhr) {
							form.trigger('submit');
							las_instance.el.removeClass('ajax-loading');
						});
					} else {
						self.filter();
					}
					return false;
				}
			});

			$(document).ready(function () {
				var
					selectCallback = function (event, ui) {
						$(window).trigger('javo:ajax-search-select', ui);
						category_filter.val('');
						if (ui.item.type == 'listing_category') {
							event.preventDefault();
							category_filter.val(ui.item.object_id);
							self.amenities_filter(ui.item.object_id);
							$(this).val(ui.item.label);
							self.filter();
						}
						return false;
					};
				if ($.fn.autocomplete != 'undefined' && ajax_search_filter.length) {
					ajax_search_filter.autocomplete('option', 'select', selectCallback);
				}

			});

			if (ajax_search_filter.length) {
				self.DisableListTypeLoaderEffect = true;
			}

			// $(window).on('javo:json/itesm/all/load', function () {
			//$(window).on('load', function () {
			(function(){
				var submitted = false;
				if (ajax_search_filter.val()) {
					las_instance = ajax_search_filter.closest('.lava-ajax-search-form-wrap').data('las-instance');
					if (typeof las_instance != 'undefined') {
						submitted = true;
						las_instance.el.addClass('ajax-loading');
						las_instance.cache().getResults(ajax_search_filter.val(), function (xhr) {
							form.trigger('submit');
							las_instance.el.removeClass('ajax-loading');
						});
					}
				}
				if (!submitted) {
					if (form.length) {
						form.trigger('submit');
					} else {
						self.filter();
					}
				}
				self.DisableListTypeLoaderEffect = false;
				return false;
			})();
		},

		keywordMatchesCallback: function (tags) {
			return function keywordFindMatches(q, cb) {
				var matches, substrRegex;

				substrRegex = new RegExp(q, 'i');
				matches = [];

				$.each(tags, function (i, tag) {
					if (substrRegex.test(tag)) {
						matches.push({
							value: tag
						});
					}
				});
				cb(matches);
			}
		},

		setKeywordAutoComplete: function () {
			this.el_keyword.typeahead({
				hint: false,
				highlight: true,
				minLength: 1
			}, {
				name: 'tags',
				displayKey: 'value',
				source: this.keywordMatchesCallback(this.tags)
			}).closest('span').css({
				width: '100%'
			});
		},

		// Main filter
		filter: function (data) {
			var obj = window.jvbpd_map_box_func ;
			main_filters.clear();

			obj.filtering = true;

			if ($('.javo-my-position').hasClass('active')) {
				var items = window.__LAVA_MAP_BOX_TEMP__ || obj.items;
			} else {
				var items = data || obj.items;
			}

			if( $('#javo-map-box-location-trigger').val() || $('[name="radius_key"]').val() ) {
				obj.geoLocationfilter = true;
			}else{
				obj.geoLocationfilter = false;
			}

			var _items = new itemManager(items);
			$(window).trigger('javo:map/filter/before', [_items, obj]);
			items = _items.items;

			items = obj.first_term_filter(items);
			items = obj.search_shortcode_filters(items);
			if(items === false) {
				return items;
			}
			items = obj.apply_keyword(items);
			items = obj.apply_multiple_filter($('[data-tax][type="checkbox"]' ), items);
			items = obj.apply_meta_filter($('[data-metakey]'), items);
			items = obj.apply_menu_filters(items);

			if (typeof obj.extend_filter == 'function') {
				items = obj.extend_filter(items);
			}

			/* Sort */
			{
				items = obj.apply_order( items );
			}

			_items.set(items);

			$(window).trigger('javo:map/filter/after', _items);

			/* Featured Item Filter */
			{
				items = _items.items;
				items = obj.featured_filter(items);
				_items.set(items);
			}

			main_filters.unique();
			_items.unique();
			items = _items.items;

			$(window).trigger('javo:map/filter/filters', main_filters);

			obj.setMarkers(items);
			$('.javo-maps-panel-list-output #products > .row').empty();
			$('#javo-listings-wrapType-container').empty();

			obj.apply_item = items;
			obj.append_list_item(0);

			$(document).trigger('javo:map_updated', [items]);
		},

		filter_reset: function () {
			var self = this;
			return function () {
				var
					el_location = $("#javo-map-box-location-ac, #javo-map-box-location-trigger"),
					el_terms = $("[name='jvbpd_map_multiple_filter'], [name='jvbpd_list_multiple_filter']"),
					el_col_filter = $(".javo-map-box-filter-items [data-dismiss-filter]");

				// Location initialize
				el_location.val('');

				// Other Column filter
				el_col_filter.trigger('click');

				// Other filter initialize
				el_terms.prop('checked', false);
			}
		},

		archiveFilter: function (items) {
			var
				self = this,
				filtered = false,
				output = new Array(),
				args = self.args;

			if (self.archive_filter_once) {
				return items;
			}

			if (typeof args.archive_page == "undefined") {
				return items;
			}

			$.each(items, function (index, data) {
				if (typeof data[args.archive_page.taxonomy] != 'undefined') {
					if (0 <= data[args.archive_page.taxonomy].indexOf(args.archive_page.term_id)) {
						output.push(items[index]);
					}
				}
			});
			self.archive_filter_once = true;
			return output;
		},

		mobile_type_switcher: function () {
			var
				self = this,
				container = $('.javo-maps-container'),
				list = $('.javo-maps-panel-wrap', container),
				map = $('.javo-maps-area-wrap', container),
				callback = function () {
					if ($(window).width() <= 767) {
						$.map(new Array(list, map), function (typeData) {
							typeData.addClass('hidden');
						});
					}
				}
			//callback();
			list.removeClass('hidden');
			return function (event) {
				var button = $(this);

				callback();
				$.map(new Array('map-active', 'list-active'), function (typeClass) {
					button.removeClass(typeClass);
				});

				if ($(this).data('current-type') == 'map') {
					list.removeClass('hidden');
					$(this).data('current-type', 'list').addClass('list-active');
				} else {
					map.removeClass('hidden');
					$(this).data('current-type', 'map').addClass('map-active');
					self.el.gmap3({
						trigger: 'resize'
					}, 'autofit');
				}
			}
		},

		apply_multiple_filter: function (terms, data) {
			var self = this;
			var result = new Array();
			var term = {};
			// var is_cond_and = self.args.amenities_filter == 'and';
			var termCondition = 'or';

			terms.each( function () {
				var $item = $(this);
				var taxonomy = $item.data('tax');

				if(!$item.is(':checked')) {
					return true;
				}

				if(typeof term[taxonomy] != 'undefined' ) {
					term[taxonomy].cond = $item.data('condition') || 'and';
					term[taxonomy].val.push($item.val());
				}else{
					term[taxonomy] = {
						cond : $item.data('condition') || 'and',
						val : new Array($item.val()),
					}
				}
			} );

			if (Object.keys(term).length) {
				$.each(data, function (itemIX, itemData) {
					var refTermCond = null;
					$.each(term, function (taxonomy, taxMap) {
						var termCond = null;
						for(var inTerms in taxMap.val) {
							var hasTerm = typeof itemData[taxonomy] != 'undefined' && itemData[taxonomy] &&  itemData[taxonomy].indexOf(taxMap.val[inTerms]) > -1;
							if(hasTerm) {
								term[taxonomy].hasTerm = true;
							}
							if('or' == termCondition) {
								termCond = termCond === null ? hasTerm : termCond || hasTerm;
							}else{
								termCond = termCond === null ? hasTerm : termCond && hasTerm;
							}
						}
						term[taxonomy].appendTerm = termCond;
					});

					var taxResult = null;
					var termResult = null;
					$.each(term, function(taxonomy, taxMap) {
						var cond = taxMap.cond || 'and';
						var exists = taxMap.hasTerm || false;
						var append = taxMap.appendTerm || false;
						if( 'or' == cond ) {
							taxResult = taxResult === null ? exists : taxResult || exists;
						}else{
							taxResult = taxResult === null ? exists : taxResult && exists;
						}
						termResult = termResult === null ? append : termResult && append;
					});
					if( true === taxResult && true === termResult ) {
						result.push(data[itemIX]);
					}
				});
			} else {
				result = data;
			}
			return result;
		},

		apply_meta_filter: function (filters, items) {
			if (filters.length) {
				$.each(filters, function (indexFilteri, element) {
					var
						result = [],
						strKey = $(this).data('tax') ? $(this).data('tax') : $(this).data('metakey'),
						strValue = $(this).val();

					if (strValue) {
						$.each(items, function (i, k) {
							if (k[strKey] && k[strKey].toString().indexOf(strValue) > -1) {
								result.push(items[i]);
							}
						});
						items = result;
						main_filters.add(strKey, strValue);
					}
				});
			}
			return items;
		},

		compate_keyword: function (data, keyword) {
			var
				self = this,
				result = new Array();

			if (keyword != "" && typeof keyword != "undefined") {
				keyword = keyword.toLowerCase();
				$.each(data, function (i, k) {
					if (
						self.tag_matche(k.tags, keyword) ||
						k.post_title.toLowerCase().indexOf(keyword) > -1
					) {
						result.push(data[i]);
					}
				});
			} else {
				result = data;
			}
			return result;
		},

		apply_keyword: function (data) {
			if (this.el_keyword.length) {
				data = this.compate_keyword(data, this.el_keyword.val());
			}
			return data;
		},

		filterCategoryAndTags: function (data) {
			var
				self = this,
				result = new Array(),
				key_result = new Array(),
				cat_result = new Array(),
				taxonomy = 'listing_category',
				searchbar = self.searchbar,
				cagetory = $('select[data-category-tag="' + taxonomy + '"]', searchbar),
				query = cagetory.val() || '',
				query_string = cagetory.text();

			result = self.compate_keyword(data, query_string);

			if (query != '') {
				$.each(data, function (i, k) {
					if (typeof k[taxonomy] != "undefined" && k[taxonomy]) {
						if (k[taxonomy].indexOf(query) > -1) {
							result.push(data[i]);
							return false;
						}
					}
				});
			}
			return result;
		},

		first_term_filter: function(data) {
			var self = this;
			var result = new Array();
			if(
				self.args.first_filter &&
				self.args.first_filter.taxonomy &&
				self.args.first_filter.term
			) {
				$.each(data, function (i, k) {
					if(
						typeof k[self.args.first_filter.taxonomy] != "undefined" &&
						 k[self.args.first_filter.taxonomy]
					){
						if (k[self.args.first_filter.taxonomy].indexOf(self.args.first_filter.term.toString()) > -1) {
							result.push(data[i]);
						}
					}
				});
				// items = result;
				main_filters.add(self.args.first_filter.taxonomy, self.args.first_filter.term);
				self.args.first_filter = false;
			}else{
				result = data;
			}
			return result;
		},

		search_shortcode_filters: function (data) {
			var self = this;
			var result = new Array();
			var
				find_words = new Array(),
				taxonomy = 'listing_category',
				container_exists = $('.lv-map-template').length,
				container = container_exists ? $('.lv-map-template') : $('.navbar-static-top'),
				searchbar = $('.jvbpd-search-form-section', container),
				ajax_search_filter = $('.field-ajax_search input[data-search-input]', searchbar),
				keyword_filter = $('.field-ajax_search input[name="keyword"]', searchbar),
				address_filter = $('[name="radius_key"]', searchbar),
				ajaxSearchInstance = ajax_search_filter.closest('.lava-ajax-search-form-wrap').data('las-instance') || {},
				query = $('input[name="category"]', searchbar).val() || '',
				locations = $('input[name="location"]', searchbar).val() || '';

			if( address_filter.val() ) {
				if(self.addressFiltered) {
					self.addressFiltered = false;
				}else{
					var callback = self.distancebar_in_search();
					callback();
					$('.javo-geoloc-slider').trigger('set');
					return false;
				}
			}

			query = query.toString();

			if (query != '') {
				$.each(data, function (i, k) {
					if (typeof k[taxonomy] != "undefined" && k[taxonomy]) {
						if (k[taxonomy].indexOf(query) > -1) {
							result.push(data[i]);
						}
					}
				});
				main_filters.add(taxonomy, [query]);
			} else {
				if (ajaxSearchInstance && keyword_filter.val() && ajaxSearchInstance.cache().get(keyword_filter.val())) {
					$.each(ajaxSearchInstance.cache().get(keyword_filter.val()), function (index, item) {
						if (item.type == 'listings' && item.object_id) {
							find_words.push(item.object_id);
						}
					});
					$.each(data, function (i, k) {
						if (find_words.indexOf(k.post_id.toString()) > -1) {
							result.push(data[i]);
						}
					});
				} else {
					result = data;
				}
			}
			return result;
		},

		order_raty: function (type) {
			return function (a, b) {
				if (type == 'reviewed') {
					return parseInt(a.rating_count || 0) > parseInt(b.rating_count || 0) ? -1 : parseInt(a.rating_count || 0) < parseInt(b.rating_count || 0) ? 1 : 0;
				} else {
					return parseFloat(a.rating || 0) > parseFloat(b.rating || 0) ? -1 : parseFloat(a.rating || 0) < parseFloat(b.rating || 0) ? 1 : 0;
				}
			}
		},

		apply_working_hours_filter: function (item) {
			var
				result = new Array(),
				currentTime = new Date(),
				currentDateIndex = parseInt((currentTime.getDay() + 6) % 7);

			$.each(item, function (index, data) {
				if (data.working_hours) {
					var workData = data.working_hours[currentDateIndex];
					if (workData.isActive) {
						if(typeof workData.timeFrom == 'object') {
							$.each(workData.timeFrom, function(timeFromIndex, timeFromValue) {
								var
									openHour = new Date(),
									closeHour = new Date(),
									parseOpenHour = workData.timeFrom[timeFromIndex].split(':'),
									parseCloseHour = workData.timeTill[timeFromIndex].split(':');

								openHour.setHours(parseOpenHour[0]);
								openHour.setMinutes(parseOpenHour[1]);
								closeHour.setHours(parseCloseHour[0]);
								closeHour.setMinutes(parseCloseHour[1]);

								if (openHour < currentTime && currentTime < closeHour) {
									result.push(item[index]);
								}
							});
						}else{
							var
								openHour = new Date(),
								closeHour = new Date(),
								parseOpenHour = workData.timeFrom.split(':'),
								parseCloseHour = workData.timeTill.split(':');

							openHour.setHours(parseOpenHour[0]);
							openHour.setMinutes(parseOpenHour[1]);
							closeHour.setHours(parseCloseHour[0]);
							closeHour.setMinutes(parseCloseHour[1]);

							if (openHour < currentTime && currentTime < closeHour) {
								result.push(item[index]);
							}
						}
					}
				}
			});
			return result;
		},

		apply_menu_filters: function (items) {
			var self = this;

			items = self.objToArray(items);

			$('[data-menu-filter]').each(function (filterIndex, filterData) {
				var
					results = new Array(),
					filterName = $(this).data('menu-filter'),
					filterValue = $(this).data('filter-value'),
					filterType = $(this).data('filter-type');

				if (!filterValue) {
					return true;
				}

				if (filterName == 'rating') {
					if (['high', 'low'].indexOf(filterValue) >= 0) {
						items.sort(self.order_raty());
						if ('low' == filterValue) {
							items.reverse();
						}
					} else if (typeof filterValue == 'number') {
						results = $.grep(items, function (itemData) {
							return parseInt(itemData.rating) == filterValue;
						});
						items = results;
					}
					return true;
				}

				if (filterName == 'order' && filterValue) {
					items.sort(self.compare(filterValue));
					if (filterType == 'desc') {
						items.reverse();
					}
					return true;
				}

				if (filterName == 'reviewed' && $('[data-value]', this).hasClass('active')) {
					items.sort(self.order_raty('reviewed'));
					return true;
				}

				if (filterName == 'favorite' && $('[data-value]', this).hasClass('active')) {
					items.sort(self.compare('favorite'));
					return true;
				}

				if (filterName == 'openhour' && $('[data-value]', this).hasClass('active')) {
					items = self.apply_working_hours_filter(items);
					return true;
				}

				if (filterName == 'price_range') {
					results = $.grep(items, function (itemData) {
						return itemData.price_range == filterValue;
					});
					items = results;
					return true;
				}

			});

			return items;
		},

		apply_menu_filters_: function (item) {
			var
				self = this,
				results = new Array();

			$('[data-menu-filter]').each(function (i, k) {
				var
					button,
					result = new Array(),
					is_filtered = false,
					filter = $(this).data('menu-filter'),
					value = $(this).data('filter-value');

				if ('rating' == filter && value != '') {
					switch (value) {
						case 'high':
							result = self.objToArray(item);
							result.sort(self.order_raty());
							is_filtered = true;
							break;
						case 'low':
							result = self.objToArray(item);
							result.sort(self.order_raty());
							result.reverse();
							is_filtered = true;
							break;
						case 5:
						case 4:
						case 3:
						case 2:
						case 1:
							$.each(item, function (ii, data) {
								if (parseInt(data.rating) == parseInt(value)) {
									result.push(item[ii]);
								}
							});
							is_filtered = true;
					}
				} else if ('order' == filter && value != '') {
					result = self.objToArray(item);
					result.sort(self.compare(value));
					is_filtered = true;
				} else if ('reviewed' == filter && value != '') {
					if ($('[data-value]', this).hasClass('active')) {
						result = self.objToArray(item);
						result.sort(self.order_raty('reviewed'));
						is_filtered = true;
					}
				} else if ('favorite' == filter && value != '') {
					if ($('[data-value]', this).hasClass('active')) {
						result = self.objToArray(item);
						result.sort(self.compare('favorite'));
						is_filtered = true;
					}
				} else if ('openhour' == filter && value != '') {
					if ($('[data-value]', this).hasClass('active')) {
						result = self.apply_working_hours_filter(item);
						is_filtered = true;
					}
				}
				if (is_filtered) {
					item = result;
				}
			});

			return item;
		},

		tag_matche: function (str, keyword) {
			var i = 0;
			if (str != "") {
				for (i in str) {
					if (str[i].toLowerCase().match(keyword)) {
						return true;
					}
				}
			}
			return false;
		},

		keyword_: function (e) {
			var obj = window.jvbpd_map_box_func;
			if (e.keyCode == 13) {
				if ($("#javo-map-box-location-ac").val()) {
					$('.javo-geoloc-slider').trigger('set');
				} else {
					obj.filter();
				}
			}
		},

		featured_filter: function (data) {

			var
				obj = this,
				result = new Array(),
				features = new Array(),
				others = new Array();

			if (obj.args.panel_list_featured_first != 'enable')
				return data;

			$.each(data, function (i, k) {
				k.f && typeof k.f != 'undefined' && k.f.toString() == '1' ? features.push(data[i]) : others.push(data[i]);
			});

			result = $.merge(features, others);

			return result;
		},

		setMarkers: function (response) {

			var item_markers = new Array();
			var obj = window.jvbpd_map_box_func;
			var bounds = new google.maps.LatLngBounds();

			obj.map_clear(true);

			if (response) {
				$.each(response, function (i, item) {

					if (typeof item != "undefined" && item.lat != "" && item.lng != "") {
						var
							default_marker = obj.args.map_marker,
							map_marker = item.icon || default_marker;

						if (obj.args.marker_type == 'default')
							map_marker = default_marker;

						item_markers.push({
							//latLng		: new google.maps.LatLng( item.lat, item.lng )
							lat: item.lat,
							lng: item.lng,
							options: {
								icon: map_marker
							},
							id: "mid_" + item.post_id,
							data: item
						});
						bounds.extend(new google.maps.LatLng(item.lat, item.lng));
					}
				});
			}

			if (item_markers.length > 0) {

				var _opt = {
					marker: {
						values: item_markers,
						events: {
							click: function (m, e, c) {

								var map = $(this).gmap3('get');
								obj.infoWindo.setContent($("#javo-map-loading-template").html());
								obj.infoWindo.open(map, m);

								if (obj.click_on_marker_zoom)
									map.setCenter(m.getPosition());

								//obj.ajaxurl

								$.post(
										obj.ajaxurl, {
											action: "jvbpd_map_info_window_content",
											post_id: c.data.post_id
										},
										function (response) {
											var str = '',
												nstr = '';
											if (response.state == "success") {
												str = $('#javo-map-box-infobx-content').html();
												str = str.replace(/{post_id}/g, response.post_id || '');
												str = str.replace(/{post_title}/g, response.post_title || '');
												str = str.replace(/{post_date}/g, response.post_date || '');
												str = str.replace(/{permalink}/g, response.permalink || '');
												str = str.replace(/{thumbnail}/g, response.thumbnail);
												str = str.replace(/{category}/g, response.category || nstr);
												str = str.replace(/{location}/g, response.location);
												str = str.replace(/{phone}/g, response.phone || nstr);
												str = str.replace(/{mobile}/g, response.mobile || nstr);
												str = str.replace(/{website}/g, response.website || nstr);
												str = str.replace(/{email}/g, response.email || nstr);
												str = str.replace(/{address}/g, response.address || nstr);
												str = str.replace(/{author_name}/g, response.author_name || nstr);
												str = str.replace(/{featured}/g, (typeof response.featured != 'undefined' ? 'featured-item' : ''));
												str = str.replace(/{type}/g, response.type || nstr);
												str = str.replace(/{meta}/g, response.meta || nstr);
												str = str.replace(/{rating}/g, response.rating || '');

											} else {
												str = "error";
											}
											$("#javo-map-info-w-content").html(str);
											$(document).trigger('jvbpd_sns:init');
											$(document).trigger('javo:map_info_window_loaded');
										}, 'json'
									)
									.fail(function (response) {
										//$.jvbpd_msg({ content: $( "[javo-server-error]" ).val(), delay: 10000 });
										// alert( $( "[javo-server-error]" ).val() );
										console.log(response.responseText);
									});
							} // End Click
						} // End Event
					} // End Marker
				}

				if (obj.args.cluster != "disable") {
					_opt.marker.cluster = {
						radius: parseInt(obj.args.cluster_level || 50),
						0: {
							content: '<div class="javo-map-cluster admin-color-setting">CLUSTER_COUNT</div>',
							width: 52,
							height: 52
						},
						events: {
							click: function (c, e, d) {
								var $map = $(this).gmap3('get');
								var maxZoom = new google.maps.MaxZoomService();
								var c_bound = new google.maps.LatLngBounds();

								// IF Cluster Max Zoom ?
								maxZoom.getMaxZoomAtLatLng(d.data.latLng, function (response) {
									if (response.zoom <= $map.getZoom() && d.data.markers.length > 0) {
										var str = '';

										str += "<ul class='list-group'>";

										str += "<li class='list-group-item disabled text-center'>";
										str += "<strong>";
										str += obj.args.strings.multiple_cluster;
										str += "</strong>";
										str += "</li>";

										$.each(d.data.markers, function (i, k) {
											str += "<a onclick=\"window.jvbpd_map_box_func.marker_trigger('" + k.id + "');\" ";
											str += "class='list-group-item'>";
											str += k.data.post_title;
											str += "</a>";
										});

										str += "</ul>";
										obj.infoWindo.setContent(str);
										obj.infoWindo.setPosition(c.main.getPosition());
										obj.infoWindo.open($map);

									} else {
										if (d.data.markers) {
											$.each(d.data.markers, function (i, k) {
												c_bound.extend(new google.maps.LatLng(k.lat, k.lng));
											});
										}

										$map.fitBounds(c_bound);
										/*
										$map.setCenter( c.main.getPosition() );
										$map.setZoom( $map.getZoom() + 2 );
										*/
									}
								}); // End Get Max Zoom
							} // End Click
						} // End Event
					} // End Cluster
				} // End If

				var init_mapZoom = parseInt(obj.args.map_zoom || 0);

				if (init_mapZoom > 0 && !obj.zoom_lvl_once) {

					_opt.map = {
						options: {
							zoom: init_mapZoom
						}
					}

					/**
					if (!isNaN(parseFloat(this.args.map_init_position_lat)) && !parseFloat(isNaN(this.args.map_init_position_lat))) {
						// _opt.map.options.center =
					} */
					_opt.map.options.center = _opt.map.options.center || bounds.getCenter();
					obj.disableAutofit = true;
				}

				obj.extend_mapOption = {};

				$(document).trigger('javo:parse_marker', obj);

				_opt = $.extend(true, {}, _opt || {}, obj.extend_mapOption || {});

				/*
				if (!obj.disableAutofit) {
					this.el.gmap3(_opt, "autofit");
				} else {
					this.el.gmap3(_opt);
					obj.disableAutofit = false;
				}
				if(obj.zoom_lvl_once&&!obj.disableAutofit){
					this.el.gmap3('get').setZoom(init_mapZoom);
					obj.zoom_lvl_once = true;
				} */
				this.el.gmap3(_opt, "autofit");
				$(window).trigger('javo:map/set_marker/after', new Array(this.el.gmap3('get'), _opt));
				return false;
			}
		},

		map_clear: function (marker_with) {
			var elements = new Array('rectangle');
			if (!$('.javo-my-position').hasClass('active'))
				elements.push('circle');

			if (marker_with)
				elements.push('marker');

			this.el.gmap3({
				clear: {
					name: elements
				}
			});
			this.iw_close();
		},

		iw_close: function () {
			if (typeof this.infoWindo != "undefined") {
				this.infoWindo.close();
			}
		},

		load_more: function (e) {
			var obj = window.jvbpd_map_box_func;
			return function (e) {
				e.preventDefault();
				if (!$(this).data('origin-text')) {
					$(this).data('origin-text', $(this).text());
				}
				obj.append_list_item(obj.loaded_);
			}
		},

		append_list_item: function (offset) {
			$(window).trigger('javo:map/items/prepare', this );
			var
				self = this,
				jv_integer,
				ids = new Array(),
				obj = window.jvbpd_map_box_func,
				data = obj.apply_item || new Array(),
				args = {
					controls: {
						loadMore: $('[data-javo-map-load-more]'),
						not_found_template: $('script#javo-map-not-found-data').html(),
						map: {
							output: $('.javo-maps-panel-list-output #products > .row'),
						},
						list: {
							output: $('#javo-listings-wrapType-container'),
						}
					},
					parametter: {
						template_id: obj.args.template_id,
						post_id: new Array(),
						map: {
							module: $('.javo-maps-panel-list-output').data('module') || false,
							columns: $('.javo-maps-panel-list-output').data('columns') || 0,
						},
						list: {
							module: $('.list-block-wrap').data('module') || false,
							columns: $('.list-block-wrap').data('columns') || 0,
						},
					}
				};

			var limit = parseInt(obj.args.loadmore_amount || 12);

			if (self.getListContentsProcess) {
				return false;
			}
			self.getListContentsProcess = true;

			jv_integer = 0;
			this.loaded_ = limit + offset;

			if (data) {
				$.each(data, function (i, k) {
					jv_integer++;
					if (jv_integer > obj.loaded_) {
						return false;
					}
					if (typeof k != "undefined" && jv_integer > offset) {
						ids.push(k.post_id);
					}
				});
			}

			args.parametter.post_id = ids;
			$("[data-dismiss]", args.controls.list.output).remove();

			$(document).trigger('javo:map_pre_get_lists');
			$(window).trigger('javo:map/items/get/before', [args, obj]);

			$.post(
					obj.ajaxurl, {
						action: 'jvbpd_map_list',
						post_ids: args.parametter.post_id,
						template: args.parametter.loadMore,
						mapModule: args.parametter.map.module,
						mapColumns: args.parametter.map.columns,
						listModule: args.parametter.list.module,
						listColumns: args.parametter.list.columns,
					},
					function (response) {
						var mapOutput, listOutput;

						mapOutput = listOutput = args.controls.not_found_template;

						if (response && response.map)
							mapOutput = response.map;

						if (response && response.list) {
							listOutput = obj.renderList(response.list);
						}

						args.controls.map.output.append(mapOutput);

						if (!self.DisableListTypeLoaderEffect) {
							args.controls.list.output.append(listOutput);
						}

						$(document).trigger('javo:map_get_lists_after');
						$(window).trigger('javo:map/items/get/after', [args, obj]);

						if (obj.loaded_ >= data.length) {
							args.controls.loadMore.text(args.controls.loadMore.data('nomore')).prop('disabled', true).addClass('disabled hidden');
						} else {
							args.controls.loadMore.text(args.controls.loadMore.data('origin-text')).prop('disabled', false).removeClass('disabled');
						}
						obj.filtering = false;
						// setTimeout(function(){obj.filtering = false;}, 5000);
					}, "JSON"
				)
				.fail(function (response) {
					console.log(response.responseText);
					obj.filtering = false;
					// setTimeout(function(){obj.filtering = false;}, 5000);
				}) // Fail
				.always(function () {
					self.getListContentsProcess = false;
					obj.filtering = false;
					//setTimeout(function(){obj.filtering = false;}, 5000);
					obj.resize();
				}) // Complete
		},

		list_replace: function (str, data) {
			var is_featured = data.f === '1' ? 'space_featured' : false;

			str = str.replace(/{post_id}/g, data.post_id);
			str = str.replace(/{post_title}/g, data.post_title || '');
			str = str.replace(/{excerpt}/g, data.post_content || '');
			str = str.replace(/{thumbnail_large}/g, data.thumbnail_large || '');
			str = str.replace(/{permalink}/g, data.permalink || '');
			str = str.replace(/{avatar}/g, data.avatar || '');
			str = str.replace(/{rating}/g, data.rating || 0);
			str = str.replace(/{favorite}/g, data.favorite || '');
			str = str.replace(/{location}/g, data.location || '');
			str = str.replace(/{date}/g, data.post_date || '');
			str = str.replace(/{author_name}/g, data.aurthor_name || '');
			str = str.replace(/{featured}/g, is_featued);
			//
			str = str.replace(/{category}/g, data.category || '');
			str = str.replace(/{type}/g, data.type || '');
			return str;
		},

		renderList: function (results) {
			var
				self = this,
				result = $('<div>').append(results),
				addressSearchbar = $('.field-google input[name="radius_key"]');

			result.find('[data-post-id]').each(function () {
				var
					aa,
					post_id = $(this).data('post-id') || 0,
					content = $(this).html(),
					latLng = new google.maps.LatLng(self.getItem(post_id).lat, self.getItem(post_id).lng),
					curlatLng = obj.address ? obj.address.latLng : new google.maps.LatLng(0, 0),
					calcurator = google.maps.geometry.spherical.computeDistanceBetween(curlatLng, latLng),
					distance_km = 0;

				distance_km = parseFloat(calcurator / 10000);

				$('.jv-meta-distance', content).addClass('hidden');

				/* miles, km, nautical, metres, feet, yards */
				// content = content.replace( /{distance}/g, google.maps.Distance( 'km', calcurator ) + ' km' );
				if (0 !== curlatLng.lat() && 0 !== curlatLng.lng()) {
					aa = $('.jv-meta-distance', content).filter(function () {
						$(this).html(distance_km.toFixed(2) + ' km');
					});
				}
				$(this).html($(content).html(function () {
					$('.jv-meta-distance', this).html(distance_km.toFixed(2) + ' km');
				}));
			});
			return result.children();
		},

		trigger_marker: function (e) {
			var obj = window.jvbpd_map_box_func;
			obj.el.gmap3({
				map: {
					options: {
						zoom: parseInt($('[javo-marker-trigger-zoom]').val())
					}
				}
			}, {
				get: {
					name: "marker",
					id: $(this).data('id'),
					callback: function (m) {
						google.maps.event.trigger(m, 'click');
					}
				}
			});
		},

		order_switcher: function () {
			var obj = this;
			return function (e) {
				e.preventDefault();

				var
					_current = $(this).data('order') || 'desc',
					_after = _current == 'desc' ? 'asc' : 'desc';

				if (!$(this).hasClass('active'))
					return;

				$('span.desc, span.asc', this).addClass('hidden');
				$('span.' + _after, this).removeClass('hidden');
				$(this).data('order', _after);

				obj.filter();
			}
		},

		mapTriggerControls : function() {
			var
				self = this,
				controls = $('.jvbpd-map-control');

			controls.each( function(){
				var
					$this = $(this),
					thisTitle = $this.attr("title");
				if( thisTitle ) {
					$(this).tooltip({title:thisTitle.toUpperCase()});
				}
			})


			$('.jvbpd-map-control.control-zoom-in').on( 'click', function() {
				var
					wrap = $(this).closest('.javo-maps-area-wrap'),
					thisMap = $('.javo-maps-area', wrap),
					googleMap = thisMap.gmap3('get');
				googleMap.setZoom(googleMap.getZoom() - 1);
			});

			$('.jvbpd-map-control.control-zoom-out').on( 'click', function() {
				var
					wrap = $(this).closest('.javo-maps-area-wrap'),
					thisMap = $('.javo-maps-area', wrap),
					googleMap = thisMap.gmap3('get');
				googleMap.setZoom(googleMap.getZoom() + 1);
			});

			$('a.dropdown-item', $('.jvbpd-map-control.control-map-type').closest('.btn-group') ).on( 'click', function() {
				var
					type = google.maps.MapTypeId.ROADMAP,
					wrap = $(this).closest('.javo-maps-area-wrap'),
					thisMap = $('.javo-maps-area', wrap),
					googleMap = thisMap.gmap3('get');

				switch( $(this).data('type')) {
					case 'satellite': type = google.maps.MapTypeId.SATELLITE; break;
					case 'hybrid': type = google.maps.MapTypeId.HYBRID; break;
					case 'terrain': type = google.maps.MapTypeId.TERRAIN; break;
					case 'roadmap': default:
						type = google.maps.MapTypeId.ROADMAP;
				}

				googleMap.setMapTypeId(type);
			});

		},

		bindMobile: function () {
			var
				self = this,
				template = $("script#jvbpd-map-mobile-buttons").html(),
				resultDIV = $('.javo-maps-panel-list-output'),
				parentSection = resultDIV.closest('section.elementor-element'),
				search_forms = $('.elementor .jvbpd-search-form-section');

			$(template).insertBefore(parentSection);
			if ($(window).width() > 767) {
				// search_forms.remove();
			}
			self.bindMobileEvents();
		},

		bindMobileEvents: function () {
			var
				self = this,
				searchFilter = $('.javo-maps-search-wrap'),
				output = $('.javo-maps-panel-list-output'),
				header_wrap = $('.lv-map-template'),
				search_in_header = $('.jvbpd-search-form-section', header_wrap);

			if ($(window).width() < 767) {
				searchFilter.addClass('hidden');
			}

			// $( '.field-google .javo-geoloc-trigger' ).on( 'click', self.distancebar_in_search() );
			$('input[name="radius_key"]', search_in_header).on('click', self.distancebar_in_search());
		},

		distancebar_in_search: function (disable) {
			var self = this;
			return function (event) {
				var
					distanceBar,
					headerFilterContainer = $('.lv-map-template'),
					headerSearchForm = $('.jvbpd-search-form-section', headerFilterContainer),
					addressFilter = $('.field-google', headerSearchForm),
					template = $('script#jvbpd-map-distance-bar').html() || '';

				if (disable) {
					event.preventDefault();
				}
				if(!addressFilter.length){
					return false;
				}

				if (!addressFilter.hasClass('loaded-distance')) {
					addressFilter.addClass('loaded-distance').append(template);
					self.setDistanceBar();

					$('button[data-close]', addressFilter).on('click', function (event) {
						distanceBar.removeClass('open');
					});
				}
				distanceBar = $('.jvbpd-map-distance-bar-wrap', headerSearchForm);
				distanceBar.addClass('open');
				return true;
			}
		},

		module_switcher: function () {
			var
				self = this,
				container = $('.javo-maps-panel-list-output'),
				wrap = $('#products', container),
				ONEROW = 'one-row';
			return function (e) {
				var is_active = $(this).hasClass('active');
				if ($(this).val() == 'list') {
					wrap.addClass(ONEROW);
				} else {
					wrap.removeClass(ONEROW);
				}
			}
		},

		menu_filter: function () {
			var self = this;
			return function (e) {
				var
					$this = $(this),
					order = $this.data('type') || '',
					filter = $this.closest('[data-menu-filter]');
				filter.data({
					'filter-value': $this.data('value') || '',
					'filter-type': order,
				});
				$this.data('type', (order == 'desc' ? 'asc' : 'desc'));
				$this.closest('.dropdown-item').removeClass('asc desc');
				$this.closest('.dropdown-item').addClass($this.data('type'));
				self.filter();
			}
		},

		menuFilters: function () {
			var near_me = $('[data-menu-filter="nearme"]');

			near_me.on('click', function () {
				navigator.geolocation.getCurrentPosition(function (position) {
					var
						address = '',
						geocoder = new google.maps.Geocoder,
						geolocate = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

					geocoder.geocode({
						'location': geolocate
					}, function (results, status) {
						if (status === 'OK') {
							address = results[1].formatted_address;
							$('[name="radius_key"]').val(address);
							$('.javo-geoloc-slider').trigger('set');
						} else {
							alert(strings.geolocation_fail);
						}
					});
				});

			});

			$('.dropdown-menu', near_me).on('click', function (event) {
				event.stopPropagation();
			});

			$('[data-close]', near_me).on('click', function () {
				near_me.removeClass('show open');
				$(this).closest('.dropdown-menu').removeClass('show open');
			});


		},

		objToArray: function (data) {
			var result = new Array();
			$.each(data, function (i, r) {
				result.push(data[i]);
			});
			return result;
		},

		side_out: function () {
			var panel = $(".javo-maps-favorites-panel");
			var btn = $(".javo-mhome-sidebar-onoff");
			var panel_x = -(panel.outerWidth()) + 'px';
			var btn_x = 0 + 'px';

			panel.clearQueue().animate({
				marginLeft: panel_x
			}, 300);
			btn.clearQueue().animate({
				marginLeft: btn_x
			}, 300);
		},

		side_move: function () {
			var panel = $(".javo-maps-favorites-panel");
			var btn = $(".javo-mhome-sidebar-onoff");
			var panel_x = 0 + 'px';
			var btn_x = panel.outerWidth() + 'px';
			panel.clearQueue().animate({
				marginLeft: panel_x
			}, 300);
			btn.clearQueue().animate({
				marginLeft: btn_x
			}, 300);
		},

		compare: function (orderBy) {
			var obj = this;
			return function (a, b) {
				if (obj.args.panel_list_random == 'enable') {
					orderBy = 'random';
				}

				switch (orderBy) {
					case 'random':
						return Math.floor((Math.random() * (1 + 1 + 1)) - 1);
					case 'name':
						return a.post_title > b.post_title ? -1 : a.post_title < b.post_title ? 1 : 0;
						break;
					case 'dist':
						return a.distance > b.distance ? -1 : a.distance < b.distance ? 1 : 0;
						break;
					case 'favorite':
						return parseInt(a.save_count || 0) > parseInt(b.save_count || 0) ? -1 : parseInt(a.save_count || 0) < parseInt(b.save_count || 0) ? 1 : 0;
						break;
					case 'date':
					default:
						return false;
				}
			}
		},

		apply_dist: function (data) {
			var
				result = [],
				curlatLng = obj.address ? obj.address.latLng : new google.maps.LatLng(0, 0);

			if (0 !== curlatLng.lat() && 0 !== curlatLng.lng()) {
				$.each(data, function (i, k) {
					var latLng = new google.maps.LatLng(k.lat, k.lng);
					data[i].distance = google.maps.geometry.spherical.computeDistanceBetween(curlatLng, latLng);
					result.push(data[i]);
				});
			} else {
				result = data;
			}
			return result;
		},

		apply_order: function (data) {
			var
				result = [],
				o = $("input[name='map_order[orderby]']:checked").closest('label').data('order') ||'desc',
				t = $("input[name='map_order[orderby]']:checked").val();

			result = this.objToArray(data);
			result.sort(this.compare(t));

			if (o.toLowerCase() == 'desc') {
				result.reverse();
			}
			return result;
		},

		marker_on_list: function (e) {
			e.preventDefault();

			var obj = window.jvbpd_map_box_func;

			obj.marker_trigger($(this).data('id'));

			if (obj.click_on_marker_zoom)
				obj.map.setZoom(obj.click_on_marker_zoom);
		},

		marker_trigger: function (marker_id) {
			this.el.gmap3({
				get: {
					name: "marker",
					id: marker_id,
					callback: function (m) {
						google.maps.event.trigger(m, 'click');
					}
				}
			});
		}, // End Cluster Trigger

		trigger_location_keyword: function () {
			var self = this;
			return function (event) {
				if (event.keyCode == 13) {
					var
						instance = obj.distancebar_in_search(),
						address_exists = false;
					address_exists = instance();

					if( !address_exists){
						self.setDistanceBar();
					}

					$('[name="radius_key"], #javo-map-box-location-trigger').val($(this).val());
					$('.javo-geoloc-slider-trigger').trigger('set');
				}
			}
		},

		setGetLocationKeyword: function (e) {
			var
				obj = window.jvbpd_map_box_func,
				data = obj.items,
				el = $("input#javo-map-box-location-ac"),
				distance_slider = $(".javo-geoloc-slider");

			$("#javo-map-box-location-trigger").val(el.val());

			if (e.keyCode == 13) {

				if (el.val() != "") {
					distance_slider.trigger('set');
				} else {
					obj.filter(data);
				}
				e.preventDefault();
			}
		},

		setCompareDistance: function (p1, p2) {
			// Google Radius API
			var R = 6371;
			var dLat = (p2.lat() - p1.lat()) * Math.PI / 180;
			var dLon = (p2.lng() - p1.lng()) * Math.PI / 180;
			var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
				Math.cos(p1.lat() * Math.PI / 180) * Math.cos(p2.lat() * Math.PI / 180) *
				Math.sin(dLon / 2) * Math.sin(dLon / 2);
			var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
			var d = R * c;
			return d;
		},

		getMyPosition: function () {
			var
				obj = this,
				distance_slider = $(".javo-geoloc-slider, .javo-geoloc-slider-trigger");
			return function (e) {
				var input = $('.javo-location-search, .field-google [name="radius_key"], .javo-map-box-location-trigger');
				if(self.isGetMyPositioning) {
					return;
				}
				self.isGetMyPositioning = true;

				obj.el.gmap3({
					getgeoloc: {
						callback: function (latlng) {
							if (!latlng) {
								// $.jvbpd_msg({content: ERR_LOC_ACCESS, button: BTN_OK });
								alert(ERR_LOC_ACCESS);
								obj.isGetMyPositioning = false;
								return false;
							};
							$(this).gmap3({
								getaddress: {
									latLng: latlng,
									callback: function (results) {
										var strValue = results && results[1].formatted_address;
										input.val(strValue);
										obj.isGetMyPositioning = false;
										distance_slider.trigger('set');
										$(document).trigger('javo:myposUpdate', strValue);
									}
								}
							});

						} // Callback
					} // Get Geolocation
				});
			}
		},

		currentBoundSearch: function () {

			var self = this;
			return function () {

				var
					results = new Array(),
					bounds = self.map.getBounds();

				$.each(self.items, function (i, k) {

					if (bounds.contains(new google.maps.LatLng(k.lat, k.lng))) {
						results.push(self.items[i]);
					}
				});

				self.filter(results);
			}
		},
		doing: function () {
				return function (e) {
					e.preventDefault();
				}
			}

			,
		control_manager: function (block) {
				var
					obj = this;
				return function () {
					obj.map_control(block);
					obj.list_control(block);
				}
			}

			,
		map_control: function (block) {
			var
				obj = this,
				container = $('.javo-maps-search-wrap'),
				distanceBar = container.find('.javo-geoloc-slider'),
				input = container.find('input'),
				terms = $('input[type="checkbox"][data-tax]'),
				dropdown = container.find('select'),
				buttons = container.find('button'),
				toggleBtn = container.find('.javo-map-box-filter-items span'),
				ajaxSearch = $('.field-ajax_search [data-search-input]'),
				geoButton = container.find('.javo-my-position'),
				elements = new Array(
					distanceBar,
					ajaxSearch,
					input,
					terms,
					toggleBtn,
					buttons,
					geoButton
				);

			dropdown.each(function (index, element) {
				var objSelect = element.selectize;

				if (typeof objSelect == 'undefined')
					return;

				if (block) {
					objSelect.disable();
				} else {
					objSelect.enable();
				}
			});

			for (var element in elements) {
				elements[element].attr('disabled', block);
				if (block) {
					elements[element].addClass('disabled');
				} else {
					elements[element].removeClass('disabled');
				}
			}

			/*
			$( '.module[data-post-id] .javo-infow-brief' ).on( 'click', function( e ) {
				var
					modal = $( '#javo-infow-brief-window' ),
					body = $( '.modal-body', modal ),
					button = $( this ),
					item_id = button.closest( '.module' ).data( 'post-id' );
				if( button.data( 'preview_instance' ) ) {
					return;
				}
				button.data( 'preview_instance', true );
				obj.waitButton( button, false );
				obj.ajax( 'map_brief', { post_id: item_id }, function( data ) {
					body.html( data.html ).scrollTop(0);
					modal.off( 'shown.bs.modal' ).on( 'shown.bs.modal', function() {
						obj.preview_map( body );
					} );
					modal.modal( 'show' );
					obj.waitButton( button, true );
				} );
			} ); */

		},

		preview_map: function (container) {
			var
				obj = this,
				map = $('.jvbpd-preview-map', container),
				latLng = new google.maps.LatLng(map.data('lat') || 0, map.data('lng') || 0);

			map.height(300).gmap3({
				map: {
					options: {
						'center': latLng,
						'zoom': 16
					}
				},
				marker: {
					'latLng': latLng,
					options: {
						'icon': obj.args.map_marker
					}
				}
			});
		},

		list_control: function (block) {
			var
				container = $('.jvbpd_map_list_sidebar_wrap'),
				distanceBar = container.find('.javo-geoloc-slider'),
				input = container.find('input'),
				dropdown = container.find('select'),
				buttons = container.find('button'),
				geoButton = container.find('.javo-my-position'),
				elements = new Array(
					distanceBar,
					input,
					dropdown,
					buttons,
					geoButton
				);

			for (var element in elements) {
				elements[element].attr('disabled', block);
				if (block) {
					elements[element].addClass('disabled');
				} else {
					elements[element].removeClass('disabled');
				}
			}
			// $('[data-toggle="tooltip"]').tooltip();

		},

		marker_release: function() {
			var self = this;
			return function(event, data){
				var
					markerInfo = self.getMarker(data.module_id),
					marker = markerInfo.marker,
					hoverEvent = self.el.data('block-hover'),
					hoverMapLevel = self.el.data('block-hover-map-lv');
				if(marker){
					switch(hoverEvent){
						// case 'bounce' : marker.setAnimation(google.maps.Animation.BOUNCE); break;
					}
					if('yes' == self.el.data('block-hover-move-map')) {
						self.map.setZoom( parseInt(hoverMapLevel) );
						self.map.setCenter(marker.getPosition());
					}
				}
			}
		},

		marker_restore: function() {
			var self = this;
			return function(event, data){
				var
					markerInfo = self.getMarker(data.module_id),
					marker = markerInfo.marker;
				if( marker ){
					/*
					if( null !== marker.getAnimation() ) {
						marker.setAnimation(google.maps.Animation.DROP);
					} */
				}
			}
		},

		moduleLink: function () {
			var
				obj = this,
				type = obj.args.link_type,
				_marker = '',
				moduleID = 0,
				markerInfo = {},
				callback = function (_this) {
					return function () {
						var currentModule = _this ? $(this).data('post-id') : $(this).closest('.jvbpd-module[data-post-id]').data('post-id');
						if (moduleID == currentModule)
							return;

						if (moduleID) {
							obj.swapIcon(moduleID, markerInfo.icon, markerInfo.zIndex);
							$(document).trigger('javo:marker_restore', {
								module_id: moduleID,
								constructor: obj
							});
						}

						moduleID = currentModule;
						markerInfo = obj.getMarker(moduleID);

						if (obj.click_on_marker_zoom){
							obj.map.setZoom(obj.click_on_marker_zoom);
						}

						if( 'replace_marker' == obj.el.data('block-hover')){
							_marker = obj.el.data('block-hover-marker');
						}

						obj.swapIcon(currentModule, _marker, 9999, !_this);
						$(document).trigger('javo:marker_release', {
							module_id: moduleID,
							constructor: obj
						});
					}
				};

			// type = 'type3'; /* Fixed */
			$(".jvbpd-module[data-post-id]").each(function() {
				var $this = $(this);
				if(!$this.hasClass('bind-link-event')){
					/*
					switch(type){
						case 'type2' : $this.on('hover', callback(true)); break;
						case 'type3' : $('.move-marker', $this ).on('click', callback()); break;
						default:
					} */
					$this.on('hover', callback(true));
					$('.move-marker', $this ).on('click', callback());
					$this.addClass('bind-link-event');
				}
			});
		},


		getMarker: function (id) {
				var strReturn = {
					icon: false,
					zIndex: 0,
					marker:null,
				};
				this.el.gmap3({
					get: {
						name: 'marker',
						id: 'mid_' + id,
						callback: function (marker) {
							if (!marker)
								return;
							strReturn.icon = marker.getIcon();
							strReturn.zIndex = marker.getZIndex();
							strReturn.marker = marker;
						}
					}
				});
				return strReturn;
			}

			,
		swapIcon: function (id, url, posZ, popup) {
			var
				self = this,
				map = self.map;
			self.el.gmap3({
				get: {
					name: 'marker',
					id: 'mid_' + id,
					callback: function (marker) {
						if (!marker) {
							return;
						}

						marker.setIcon(url);

						if (posZ !== false){
							marker.setZIndex(posZ);
						}

						/*
						if (!map.getBounds().contains(marker.getPosition()))
							map.setCenter(marker.getPosition());
						*/

						if (popup){
							new google.maps.event.trigger(marker, 'click');
						}

					}
				}
			});
		},

		getStyles: function () {
			var
				primary_color = obj.args.map_primary_color,
				arrStyle = obj.args.map_style_json;

			if (typeof arrStyle != 'undefined' && arrStyle != '')
				return JSON.parse(arrStyle);
			return [{
				featureType: "all",
				elementType: "all",
				stylers: [{
					color: "#c0e8e8"
				}]
			}, {
				featureType: "administrative",
				elementType: "labels",
				stylers: [{
					color: "#4f4735"
				}, {
					visibility: "simplified"
				}]
			}, {
				featureType: "landscape",
				elementType: "all",
				stylers: [{
					color: "#e9e7e3"
				}]
			}, {
				featureType: "poi",
				elementType: "labels",
				stylers: [{
					color: "#fa0000"
				}, {
					visibility: "off"
				}]
			}, {
				featureType: "road",
				elementType: "labels.text.fill",
				stylers: [{
					color: "#73716d"
				}, {
					visibility: "on"
				}]
			}, {
				featureType: "road.highway",
				elementType: "all",
				stylers: [{
					color: "#ffffff"
				}, {
					weight: "0.50"
				}]
			}, {
				featureType: "road.highway",
				elementType: "labels.icon",
				stylers: [{
					visibility: "off"
				}]
			}, {
				featureType: "road.highway",
				elementType: "labels.text.fill",
				stylers: [{
					color: "#73716d"
				}]
			}, {
				featureType: "water",
				elementType: "geometry.fill",
				stylers: [{
					color: "#7dcdcd"
				}]
			}];

		},

		waitButton: function (button, available) {
			var _DISABLE = 'disabled';
			if (available) {
				$('i', button).removeClass('fa-spinner fa-spin');
				button.removeClass(_DISABLE).removeAttr(_DISABLE).prop(_DISABLE, false);
			} else {
				$('i', button).addClass('fa-spinner fa-spin');
				button.addClass(_DISABLE).attr(_DISABLE, _DISABLE).prop(_DISABLE, true);
			}
		},

		ajax: function (_action, _param, callback, failcallback) {
			var param = $.extend(true, {}, {
				action: 'jvbpd_' + _action
			}, _param);
			$.post(this.ajaxurl, param, function (data) {
					if (typeof callback == 'function') {
						callback(data);
					}
				}, 'json')
				.fail(function (xhr, err) {
					if (typeof failcallback == 'function') {
						failcallback(xhr, err);
					}
				});
		},

		info_window_events: function () {
			var
				obj = this,
				modal = $('#javo-infow-brief-window'),
				body = $('.modal-body', modal);
			return function () {
				$('.javo-infow-brief', obj.el).on('click', function (e) {
					var
						button = $(this),
						item_id = button.data('id');

					obj.waitButton(button, false);
					obj.ajax('map_brief', {
						post_id: item_id
					}, function (data) {
						body.html(data.html);
						modal.modal('show');
						obj.preview_map(modal);
						obj.waitButton(button, true);
					});
				});
				$('.javo-infow-contact', obj.el).on('click', function (e) {
					var button = $(this);

					obj.waitButton(button, false);
					obj.ajax('map_contact_form', {}, function (data) {
						body.html(data.html);
						modal.modal('show');
						obj.waitButton(button, true);
						obj.after_ajax_contactForm_rebind(body);
					});
				});
			}
		},

		after_ajax_contactForm_rebind: function (container) {
			if (typeof $.fn.wpcf7InitForm == 'function') {
				$('div.wpcf7 > form', container).wpcf7InitForm();
			}
		}
	}

	var obj = window.jvbpd_map_box_func || {};

	obj.amenities_filter = function (_terms) {

		$('.amenities-filter-area').each(function () {
			var
				$this = $(this),
				buff = {
					map: '',
					list: ''
				};
			$this.html('<div class="col-md-12">' + $("#javo-map-loading-template").html() + '</div>');
			$.post(obj.ajaxurl, {
				action: 'lava_lv_listing_manager_get_amenities_fields',
				terms: _terms,
				object_id: 0
			}, function (xhr) {

				if (0 < xhr.output.length) {
					$.each(xhr.output, function (intDataID, MetaData) {
						var template;
						if ($(this).hasClass('list-type')) {
							template = '<div class="checkbox"><label><span class="check"></span><input type="checkbox" name="jvbpd_list_multiple_filter" value="{val}" data-tax="listing_amenities" data-title="{label}" class="tclick" {checked}>{label}</label></div>';
						} else {
							template = '<div class="col-md-4 col-sm-6 filter-terms"><label><input type="checkbox" name="jvbpd_map_multiple_filter" value="{val}" data-tax="listing_amenities" data-title="{label}"{checked}>{label}</label></div>';
						}

						template = template.replace(/{val}/g, MetaData.term_id || 0);
						template = template.replace(/{label}/g, MetaData.name || '');
						template = template.replace(/{checked}/g, (MetaData.checked ? ' checked="checked"' : ''));
						if ($(this).hasClass('list-type')) {
							buff.list += template;
						} else {
							buff.map += template;
						}
					});

					$this
						.html(buff.map)
						.filter('.list-type')
						.html(buff.list);

					$('input[name="jvbpd_map_multiple_filter"]').on('click', function () {
						obj.filter();
					});

				} else {
					$this.html('<div class="col-md-12">' + xhr.empty + '</div>');
				}
				$(document).trigger('javo:map_createMark');
			}, 'JSON');
		});
	}

	// obj.append_function = function() {

	$(window).on('javo:init', function () {
		$('select.javo-selectize-option').each(function () {
			var
				selectizeOptions = {
					maxItems: $(this).data('max-items') || null,
					mode: $(this).data('mode') || false,
					plugins: new Array('remove_button')
				},
				selectize = $($(this).selectize(selectizeOptions)).get(0).selectize;

			selectize.on('change', function () {
				if ($(this).is('[data-tax="listing_category"]')) {
					obj.amenities_filter($(this).val());
				}
				if ($('#javo-map-box-location-ac').val()) {
					$('.javo-geoloc-slider').trigger('set');
				} else {
					if(!window.jvbpd_core_map_block_filter){
						obj.filter();
					}
				}
			});
			$(document).on('javo:filter_reset', function () {
				selectize.clear();
			});
		});
		obj.amenities_filter();

		// $( ".javo-map-box-advance-filter-wrap" ).sticky({ topSpacing: 150 });
		// Extend Filters Trigger
		$(document)
			.on('javo:map_createMark', function (e) {
				var
					container = $(".javo-maps-advanced-filter-wrap"),
					filters = $(".javo-map-box-filter-items"),
					meta = $("select", container),
					strX = '&times;',
					keyword = $("#javo-map-box-auto-tag", container),
					taxonomy = {},
					output = new Array();

				e.preventDefault();

				/* KeyWord */
				{
					if (keyword.val()) {
						output.push("<span data-dismiss-filter='keyword'>Keyword " + strX + "</span>");
					}
				}

				/* Meta Dropdown */
				{
					$.each(meta, function (i, k) {
						var
							name = $(this).data('name'),
							filterID = $(this).data('metakey');

						if ($(this).val())
							output.push(
								"<span data-dismiss-filter='" + filterID + "'> " +
								name + ' ' + strX + "</span>"
							);

					});
				}

				/* Mutiple Fileter Toogle */
				{
					$("[name='jvbpd_map_multiple_filter']").each(function () {
						if ($(this).is(':checked'))
							taxonomy[$(this).data('title')] = $(this).data('tax');
					});

					$.each(taxonomy, function (tax_name, tax_id) {
						output.push("<span data-dismiss-filter=\"" + tax_id + "\">");
						output.push(tax_name + " &times;");
						output.push("</span>");
					});
				}

				filters.html(output.join('&nbsp;'));
			})
			.on(
				"click", "#javo-map-box-advance-filter",
				function (e, f) {

					var
						panel = $(".javo-maps-search-wrap"),
						output = $(".javo-maps-panel-list-output"),
						advanced = $(".javo-maps-advanced-filter-wrap"),
						button = $(".javo-map-box-advance-filter-apply");

					e.preventDefault();

					f = f || false;

					if (!this._run) {
						advanced.hide();
						button.hide();
						this._run = true;
					}

					if (panel.hasClass('collapsed')) {
						button.hide('fast');
						advanced.slideUp('fast');
						panel.removeClass('collapsed');
						output.removeClass('hidden');
						if (obj.panel.hasClass('map-layout-top')) {
							$(document).trigger('javo:map_createMark');

							if (!f)
								obj.filter();
						}
					} else {
						advanced.slideDown(
							'fast',
							function () {
								button.show();
							}
						);
						panel.addClass('collapsed');
						output.addClass('hidden');
					}
					obj.resize();
					$('.javo-maps-panel-wrap').trigger('scroll');
					return;
				}
			)
			.on(
				'click', '.javo-map-box-btn-advance-filter-apply',
				function (e) {
					e.preventDefault();
					$(document).trigger('javo:map_createMark');
					$("#javo-map-box-advance-filter").trigger('click');
					if ($("#javo-map-box-location-ac").val()) {
						$('.javo-geoloc-slider').trigger('set');
					} else {
						obj.filter();
					}
					return;
				}
			)
			.on(
				'click', '[data-dismiss-filter]',
				function (e) {
					e.preventDefault();

					var
						eID = $(this).data('dismiss-filter'),
						selector = null,
						container = $(".javo-maps-advanced-filter-wrap")

					switch (eID) {
						case 'keyword':
							/* Keyword */
							{
								selector = $('#javo-map-box-auto-tag', container);
								selector.val('');
							}
							break;

						case 'price':

							selector = $("[data-min], [data-max]", container);
							selector.val('');
							break;

						default:
							/* Multiple Filter Unset */
							{
								selector = $("[name='jvbpd_map_multiple_filter'][data-tax='" + eID + "']");
								selector.prop('checked', false);
							}
							/* Dropdown Filter Unset */
							{
								selector = $("select[data-metakey='" + eID + "']");
								selector.val('');
								if (typeof selector[0] != 'undefined') {
									var selectize = selector[0].selectize;
									selectize.clear();
								}
							}
					}

					$(this).remove();
					if ($("#javo-map-box-location-ac").val()) {
						$('.javo-geoloc-slider').trigger('set');
					} else {
						obj.filter();
					}
					return;
				}
			)
			.on(
				'click', '#javo-map-box-advance-filter-reset',
				function (e) {
					$("#javo-map-box-location-ac").val();

					$(document)
						.trigger('javo:map_refresh')
						.trigger('javo:filter_reset');

				}
			)
			.on(
				'click', '.javo-map-box-advance-filter-trigger',
				function (e) {
					e.preventDefault();
					$("#javo-map-box-advance-filter").trigger('click');
				}
			);
	});

	obj.selectize_filter = function (data, elements, taxonomy) {
		var
			value_exists = false,
			result = new Array();
		$.each(data, function(dataIndex, dataValue){
			elements.each(function(elIndex, el){
				var
					$this = $(this),
					values = typeof $this.val() == 'string' ? new Array($this.val()) : $this.val();
				if(!$this.val()){
					return true;
				}
				value_exists = true;
				$.each(values, function(valIndex, term_values){
					if(typeof dataValue[taxonomy] != 'undefined' && dataValue[taxonomy]){
						if(dataValue[taxonomy].indexOf(term_values) > -1) {
							result.push(data[dataIndex]);
							return false;
						}
					}
				});
			});
		});
		return value_exists ? result : data;
	}

	obj.price_filter = function (data) {
		var
			results,
			price = $(".jvbpd-noui-price-filter"),
			min = parseInt($("[data-min]", price).val()),
			max = parseInt($("[data-max]", price).val());

		if (!isNaN(min) && min > 0) {
			results = [];
			$.each(data, function (index, arrMeta) {
				var price = parseInt(arrMeta.price);
				if( 0 < parseInt(arrMeta.sale_price)){
					price = parseInt(arrMeta.sale_price);
				}
				if (!isNaN(price) && (price >= min))
					results.push(data[index]);
			});
			data = results;
		}

		if (!isNaN(max) && max > 0) {
			results = [];
			$.each(data, function (index, arrMeta) {
				var price = parseInt(arrMeta._price);
				if (!isNaN(price) && (price <= max))
					results.push(data[index]);
			});
			data = results;
		}
		return data;
	}


	obj.extend_filter = function (items) {
		$.each(obj.args.selctize_terms, function (i, tax_selector) {
			// items = obj.selectize_filter( items, $( "[name='map_filter[" + tax_selector + "]']" ), tax_selector );
			//if (tax_selector != 'listing_category') {
				items = obj.selectize_filter(items, $(".jvbpd-search-form-section select[data-tax=" + tax_selector + "]"), tax_selector);
			// }
		});
		// items = obj.price_filter(items);
		return items;
	}

	$('.javo-maps-panel-wrap').on(
		'scroll',
		function () {

			var
				container = $(this),
				container_offset = container.offset().top,
				filter = $('.javo-map-box-advance-filter-wrap'),
				filter_offset = filter.offset().top;

		}
	);

	$(document).on('javo:map_updated', function (event, items) {
		var
			element = $('.control-panel-in-map .total-count .count'),
			output = '';

		output += items.length || 0;
		output += ' ' + element.data('suffix');
		element.html(output);
	});

	window.jvbpd_map_box_func.init();

	var jvbpd_multi_listing = function () {

		if (!window.__LAVA_MULTI_LISTINGS__)
			this.init();
	}

	jvbpd_multi_listing.prototype = {

		constructor: jvbpd_multi_listing,

		init: function () {
			window.__LAVA_MULTI_LISTINGS__ = 1;

			this.args = jvbpd_core_map_param;

			this.showListings();
			this.innerSwitch();
			this.setCommonAction();

			$(document)
				.on('javo:apply_sticky', this.setStickyContainer());
				// .on('javo:map_updated', this.showResult());

			if ($('body').hasClass('mobile-ajax-top'))
				$(document).on('javo:map_template_reisze', this.mobileFilter());

			$('.javo-maps-panel-wrap').on('scroll', this.stickyOnPanel());

			$(document).ready(this.setStickyContainer());
		},

		setStickyContainer: function () {
			return function () {
				if ($("#javo-mhome-item-info-wrap").length) {
					$("#javo-mhome-item-tabs-navi").sticky({
						topSpacing: $(".javo-mhome-item-info-wrap").offset().top
					});
				}
			}
		},

		stickyOnPanel: function () {
			var obj = this;
			return function () {
				var
					panel = $(".javo-maps-panel-wrap"),
					button = $("#javo-map-box-advance-filter", panel),
					container = $(".javo-map-box-advance-filter-wrap"),
					sticked_container = $(".javo-map-box-advance-filter-wrap-fixed"),
					container_offset = container.offset().top;

				sticked_container.css({
					top: panel.offset().top,
					width: panel.width()
				}).hide();

				if (container_offset < 0) {
					if (!obj.sticked) {
						container.children().appendTo(sticked_container);
						obj.sticked = true;
					}
					sticked_container.show();
				} else {
					if (obj.sticked) {
						sticked_container.children().appendTo(container);
						obj.sticked = false;
					}
				}
			}
		},

		showResult: function () {
			return function (event, items) {
				var
					output = $(".javo-maps-panel-list-output #products > .row"),
					template = $("#javo-map-not-found-data").html();

				if (!items.length)
					output.html(template);
			}
		},

		showListings: function () {
			var
				obj = this,
				elements = $("#javo-maps-wrap, #javo-listings-wrap"),
				switcher = "[type='radio'][name='m']",
				callback = function (e) {
					e.preventDefault();
					var type = $(switcher + ":checked").val();

					elements.addClass('hidden');
					$("#javo-" + type + '-wrap').removeClass('hidden');
					obj.process(type);

					$(".javo-maps-area").gmap3({
						trigger: 'resize'
					}, 'autofit');
					$(window).trigger('javo:on_map_type_switched', type);
					return;
				}
			callback({
				preventDefault: function () {}
			});
			$(document).on('change', switcher, callback);
		},


		innerSwitch: function () {

			var
				container = $('#javo-maps-listings-wrap'),
				switcher_wrap = $('.javo-map-box-inner-switcher'),
				map_wrap = $('#javo-maps-wrap', container),
				inner_switcher = $('> button', switcher_wrap),
				map_area = $('.javo-maps-area-wrap', map_wrap),
				panel_area = $('.javo-maps-panel-wrap', map_wrap);

			inner_switcher.on('click', function () {

				var strType = $('.hidden', this).data('value');

				$(this).data('value', strType);
				$('span', this).addClass('hidden');
				$('span[data-value="' + strType + '"]').removeClass('hidden');

				if (strType == 'map') {
					map_area.addClass('mobile-active').find('.javo-maps-area').gmap3({
						trigger: 'resize'
					}, 'autofit');
					panel_area.removeClass('mobile-active');
				} else {
					map_area.removeClass('mobile-active');
					panel_area.addClass('mobile-active');
				}

			}).trigger('click');
		},


		mobileFilter: function () {

			var
				obj = this,
				minMoved = false,
				maxMoved = false,
				panel = $('.javo-maps-panel-wrap'),
				filter_wrap = $('.javo-maps-search-wrap', panel),
				filters = $('> .row:not(.javo-map-box-advance-filter-wrap)', filter_wrap),
				advance_filters = $('.javo-maps-advanced-filter-wrap', filter_wrap);

			// Disable oneline-type
			if (panel.hasClass('jv-map-filter-type-bottom-oneline'))
				return false;

			return function (e, width) {

				if (parseInt(width) < 767) {

					if (!minMoved) {
						filters.prependTo(advance_filters);
						minMoved = true;
					}
					maxMoved = false;
				} else {
					if (!maxMoved) {
						filters.prependTo(filter_wrap);
						maxMoved = true;
					}
					minMoved = false;
				}
			}

		},

		setCommonAction: function () {

			var
				obj = window.jvbpd_map_box_func,
				map = $('#javo-maps-wrap'),
				term_elements = "[name='jvbpd_list_multiple_filter']",
				key_elements = $('#filter-keyword input.jv-keyword-trigger'),
				mypos_trigger = $('.my-position-trigger'),
				option_elements = $(".jvbpd_map_list_sidebar_wrap [data-metakey]");

			$(term_elements).each(function () {
				$(this).on('click', function () {
					var
						current_term = $(this).val(),
						current_checked = $(this).is(':checked'),
						selectors = '[name="jvbpd_map_multiple_filter"][value="{value}"], [name="jvbpd_list_multiple_filter"][value="{value}"]';

					selectors = selectors.replace(/{value}/g, current_term);
					$(selectors).prop('checked', current_checked);
					obj.filter();
				});
			});

			var geoParam = parseInt($('input[name="radius_param"]').val()) || 0;
			if(0 < geoParam) {
				// $('.javo-geoloc-slider').val(geoParam);
				var instance = obj.distancebar_in_search();
				instance();
				$(window).on('javo:json/itesm/all/load', function (event) {
					event.preventDefault();
					$('.javo-geoloc-slider').val(geoParam * 1000);;
					$('.javo-geoloc-slider').trigger('set');
				});
			}

			// Listings > My Position Trigger
			mypos_trigger.on('click', function (event) {
				event.preventDefault();

				$(document).trigger('getMyPosition');

				var instance = obj.distancebar_in_search();
				instance();
				$(document).on('javo:myposUpdate', function (event) {
					event.preventDefault();
					$('.javo-geoloc-slider').trigger('set');
				});


				/*
				var instance = obj.distancebar_in_search();
				instance();
				$( '.javo-geoloc-slider' ).trigger( 'set' ); */
				/*
				var mypos = $( ".javo-my-position", map );
				mypos.trigger( 'click' ); */
			});

			// Listings > Keyword Filter Trigger
			key_elements.on('keypress',
				function (e) {
					var mapKeyword = $("#javo-map-box-auto-tag", map);
					if (e.keyCode == 13) {
						mapKeyword.val($(this).val());
						obj.filter();
					}
				}
			);

			// Listings > Optional Trigger
			option_elements.on('change',
				function (e) {
					var target = $("select[data-metakey='" + $(this).data('metakey') + "']", map);
					target.val($(this).val());
					if ($("#javo-map-box-location-ac").val()) {
						$('.javo-geoloc-slider').trigger('set');
					} else {
						if(!window.jvbpd_core_map_block_filter) {
							obj.filter();
						}
					}
				}
			);
		},

		process: function (type) {
			var obj = this;
			window.jvbpd_map_box_func.list_type = type;
			window.jvbpd_map_box_func.resize();
		} // End Initialize Function


	}

	new jvbpd_multi_listing;

})(jQuery);