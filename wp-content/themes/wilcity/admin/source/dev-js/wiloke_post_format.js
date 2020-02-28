/**
 * Wiloke Post Format Ui
 * @copyright Wiloke Team
 * This file just include If Wiloke Post Format Ui plugin is deactivated
 */

(function($) {
	"use strict";
	$.fn.wilokePostFormatMediaPopup = function (opts) {
		let $self = $(this),
			defaults = {
				parent: '',
				title: 'My Photos',
				button: 'Select',
				multiple: true,
				isPreview: true,
				buttonClass: null,
				return: 'id',
				init: function(){},
				select: function() {}
			},
			options = $.extend(defaults, $self.data(), opts),
			$insert = '',
			WilokePostFormatMediaPopup = {
				$el: $self,
				options: options,
				$button: $('.wiloke-button', $self),
				$input: $('.wiloke-media-value', $self),
				template: '<li class="wiloke-image"><img src="" alt=""/><div class="wiloke-control-wrap"><i class="wiloke-edit dashicons dashicons-edit"></i><i class="wiloke-close dashicons dashicons-no-alt"></i></div></li>',
				$showImage: '',
				media: null,
				init: function () {
					let _this = this;

					if ( _this.options.isPreview )
					{
						_this.createHTML();
					}

					_this.setMedia();
					_this.events();
				},
				createHTML: function () {
					let $parent;

					if (this.options.parent === '') {
						$parent = this.$el;
					}else{
						if ($(this.options.parent).length) {
							$parent = $(this.options.parent)
						}
						else {
							$parent = this.$el;
						}
					}

					if (!$('ul.list-wiloke-image-media', $parent).length)
					{
						$parent.prepend('<ul class="list-wiloke-image-media"></ul>');
					}

					if ( !this.options.isPreview )
					{
						$('.list-wiloke-image-media', $parent).addClass("hidden");
					}

					this.$showImage = $('.list-wiloke-image-media', $parent);
				},
				events: function () {
					let _this = this;

					_this.setValueToInput();

					if (_this.options.multiple && _this.options.isPreview) {
						_this.$showImage.sortable({
							//placeholder: 'wiloke-sort-placeholder',
							revert: 200,
							containment: "parent",
							update: function () {
								_this.$input.trigger('change');
							}
						});
					}

					if ( _this.options.buttonClass !== null )
					{
						$self.on('click', _this.options.buttonClass, function(event){
							event.preventDefault();
							_this.$input = $('.wiloke-media-value', $self);
							_this.setValueToInput();
							$insert = _this.$showImage;

							_this.media.open();
						})
					}else{
						_this.$button.on( 'click', function (event) {
							event.preventDefault();
							$insert = _this.$showImage;
							_this.media.open();
						});
					}

					if ( _this.options.isPreview )
					{
						_this.$showImage.on('click', '.wiloke-edit', function (event) {
							event.preventDefault();
							$insert = $(this).closest('.wiloke-image');
							_this.media.open();
						});

						_this.$showImage.on('click', '.wiloke-close', function (event) {
							event.stopPropagation();
							event.preventDefault();
							$(this).closest('li').remove();
							_this.$input.trigger('change');
						});
					}


					_this.media.on('select', function () {
						let selection  = _this.media.state().get('selection');

						if ( _this.options.isPreview ){
							selection.each (function (attachment, id) {
								attachment = attachment.toJSON();
								let url = attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url ? attachment.sizes.thumbnail.url: attachment.url,
									$image = $(_this.template).attr('data-id', attachment.id).attr('data-url', attachment.url);

								$('img', $image).attr('src', url);
								_this.$showImage.removeClass('hidden');
								// Select in case multiple set is true
								if (_this.options.multiple)
								{
									if ($insert.hasClass('list-wiloke-image-media'))
									{
										$insert.append($image);
									}else{
										if (id === 0) {
											if ( _this.options.return === 'url' )
											{
												$insert.attr('data-url', attachment.url);
											}else{
												$insert.attr('data-id', attachment.id);
											}
											$('img', $insert).attr('src', url);
										}
										else {
											$insert.after($image);
										}
									}
								}else{
									_this.$showImage.empty().append($image);
								}
							});
							_this.$input.trigger('change');
						}else{
							selection.each (function (attachment, id) {
								attachment = attachment.toJSON();
								_this.$input.val(attachment.url);
							})
						}

					});

				},
				setMedia: function () {
					let _this = this;

					_this.media = wp.media({
						title: options.title,
						button: {
							text: options.button
						},
						multiple: options.multiple
					});
				},
				setValueToInput: function()
				{
					let _this = this;

					if ( _this.options.isPreview )
					{
						_this.$input.change( function ()
						{
							let listID = [];
							_this.$showImage.children('.wiloke-image').each( function ()
							{
								if ($(this).data(_this.options.return)) {
									listID.push($(this).data(_this.options.return));
								}
							});

							listID = listID.join(',');
							_this.$input.val(listID);
						});
					}
				}
			};

		return WilokePostFormatMediaPopup.init();
	};

	$.fn.wilokePostFormatAudioPopup = function (opts) {
		let $self = $(this),
			defaults = {
				title: 'My Audio',
				button: 'Select',
				afterInit: function(obj) {}
			},
			options = $.extend(defaults, $self.data(), opts);

		let WilokePostFormatAudioPopup = {
			$el: $self,
			options: options,
			audio: null,
			$button: $('.wiloke-button', $self),
			$input: $('.wiloke-audio-value', $self),
			init: function () {
				this.createAudio();
				this.events();
				// Apply callback
				this.options.afterInit(this);
			},
			createAudio: function () {
				let _this = this;
				_this.audio = wp.media({
					title: _this.options.title,
					button: {
						text: _this.options.button
					},
					library: {
						type: 'audio' // video, audio, image or file
					},
					multiple: false
				});
			},
			events: function () {
				let _this = this;
				_this.$button.click( function () {
					_this.audio.open();
				});
				_this.audio.on('select', function () {
					let selection = _this.audio.state().get('selection');
					selection.each (function (attachment, id) {
						_this.$input.val(attachment.toJSON().url).trigger('change');
					});
				});
			}
		};
		return WilokePostFormatAudioPopup.init();
	};

	$.fn.wilokeCheckValidationVideo = function (opts) {
		let $self = $(this),
			defaults = {

			},
			options = $.extend(defaults, $self.data(), opts);

		let PiValidation = {
			$el : $self,
			options: options,
			$notice: null,
			$showImage: null,
			init: function () {
				this.createHTML();
				this.events();
			},
			createHTML: function () {
				let _this = this;

				// Append element show notice
				if (!_this.$el.next('.wiloke-show-notice').length) {
					_this.$el.after('<ul class="wiloke-show-notice"><li class="wiloke-notice-button wiloke-url-pass"><i class="fa fa-check"></i></li><li class="wiloke-notice-button wiloke-url-not-pass"><i class="fa fa-close"></i></li></ul>')
				}
				_this.$notice = _this.$el.next('.wiloke-show-notice');

				// Append element show image preview
				if (!_this.$notice.next('.wiloke-show-image').length) {
					_this.$notice.after('<div class="wiloke-show-image"><img src="" alt=""/></div>');
				}
				_this.$imageShow = _this.$notice.next('.wiloke-show-image');
			},
			events : function () {
				let _this = this;

				_this.$el.change( function () {
					let url = $(this).val(), id = '', type = '';

					if (url.search('youtube') !== -1) {
                        url = url.match('(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})');
						if (url && url[1]) {
							id = url[1];
							type = 'youtube';
						}
					}
					else if (url.search('vimeo') !== -1) {
						url = url.match('(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*');
						if (url && url[5]) {
							id = url[5];
							type = 'vimeo';
						}
					}
					else {
						type = '';
					}

					$('.wiloke-notice-button',_this.$notice).removeClass('active');
					_this.$imageShow.removeClass('wiloke-ajax-loading').hide();
					if (type !== '') {
						$('.wiloke-url-pass',_this.$notice).addClass('active');
						let src = '';
						if (type === 'youtube') {
							src = 'http://img.youtube.com/vi/' + id +'/0.jpg';
							$('img', _this.$imageShow).attr('src', src);
							_this.$imageShow.show();
						}
						else if (type === 'vimeo'){
							_this.$imageShow.addClass('wiloke-ajax-loading');
							$.ajax({
								type:'GET',
								url: 'http://vimeo.com/api/v2/video/' + id + '.json',
								jsonp: 'callback',
								dataType: 'jsonp',
								success: function(data){
									if (_this.$imageShow.hasClass('wiloke-ajax-loading')) {
										let thumbnail_src = data[0].thumbnail_large;
										_this.$imageShow.removeClass('wiloke-ajax-loading');
										$('img', _this.$imageShow).attr('src', thumbnail_src);
									}
									_this.$imageShow.show();
								}
							});
						}
					}
					else {
						if (_.isEmpty($(this).val())) {
							$('.wiloke-url-not-pass',_this.$notice).addClass('active');
						}
					}
				});
			}
		}
		return PiValidation.init();
	};

	$.fn.wilokePostFormat = function (opts) {
		let defaults = {
				init: function () {},
				active: function () {}
			},
			options = $.extend(defaults, opts),
			$self = $(this);

		let WilokePostFormat = {
			$el: $self,
			$controls: $('#wiloke-controls-post-format', $self),
			$handles : $('#wiloke-handle-post-format', $self),
			media: null,
			options: options,
			init: function () {
				let _this = this;

				_this.$el.show(200);
				_this.setMedia();
				_this.events();

				// Hidden format Div
				$('#formatdiv').hide();
				$('li.active', _this.$controls).trigger('click');

			},
			events: function () {
				let _this = this;

				$('li', _this.$controls).click( function (event) {
					event.preventDefault();
					let href = $('a', this).attr('href'),
						$handle = $(href),
						format = $(this).data('format');

					if ($handle.length) {
						$handle.trigger('click');
					}
					$('li', _this.$controls).removeClass('active');
					$(this).addClass('active');
					// Show handle of post format
					_this.$handles.children().hide();
					let $format = $('#wiloke-post-format-' + format);
					$format.show();

					// Focus element
					let $focus = '';
					switch (format) {
						case 'video':
							$focus = $('#wiloke-video-field-url', $format);

							break;
						case 'audio':
							$focus = $('#wiloke-audio-field-url', $format);
							break;
						case 'quote':
							$focus = $('#wiloke-quote-field-content', $format);
							break;
					};
					if ($focus.length) {
						$focus.focus();
						let thisVal = $focus.val();
						$focus.val('').val(thisVal)
					}
				});

				// Gallery
				let $gallery = $('#wiloke-post-format-gallery'),
					$typeGallery = $('.wiloke-type-gallery', $gallery);

				$typeGallery.change( function () {
					let val = $(this).val(),
						$select = $(this).prev();

					$select.val(val);
				}).trigger('change');
				$typeGallery.prev().change( function () {
					$typeGallery.val($(this).val());
				});

				// Video
				let $videoUrl = $('#wiloke-video-field-url')
				$videoUrl.wilokeCheckValidationVideo();
				if ($videoUrl.val() !== '') {
					$videoUrl.trigger('change');
				}
			},
			setMedia: function () {
				let _this = this;
				$('.wiloke-button-media', _this.$handles).each( function() {
					$(this).wilokePostFormatMediaPopup();
				});
				$('.wiloke-button-audio', _this.$handles).each( function () {
					$(this).wilokePostFormatAudioPopup({
						afterInit: function (obj) {
							obj.$input.change( function () {
								$('#wiloke-audio-field-url', _this.$handles).val($(this).val()).trigger('change');
							});
						}
					});
				})
			}
		};
		return WilokePostFormat.init();
	};
	$(document).ready( function () {
		$('#wiloke-post-format-wrapper').wilokePostFormat({});
	});
})(jQuery);