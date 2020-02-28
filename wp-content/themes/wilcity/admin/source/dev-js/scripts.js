;(function ($) {
    "use strict";
    
    window.wilokeAdmin = {};

    if ( !$().wilokeColorPicker )
    {
        $.fn.wilokeColorPicker = function()
        {
        	if ( !$().spectrum() ){
        		return false;
	        }

	        let isShowPalette = false;
	        let _palette;
            if ( WilokeAdminGlobal.ColorPalatte !== null )
            {
                if( typeof WilokeAdminGlobal.ColorPalatte === 'object' )
                {
                     _palette = WilokeAdminGlobal.ColorPalatte;
                }else{
                     _palette = $.parseJSON(WilokeAdminGlobal.ColorPalatte);
                }
            }else{
                _palette = null;
            }

            if ( $().spectrum ) {
	            $(this).spectrum({
		            preferredFormat: "rgb",
		            showAlpha: true,
		            change: function(color) {
			            if ( color !== null ){
				            $(this).attr('value', color.toRgbString());
			            }else{
				            $(this).attr('value', '');
			            }
		            },
		            move: function (color) {
			            $(this).next().find('.sp-preview-inner').data('color', color.toRgbString()).change();
		            },
		            hide: function () {
			            $(this).change();
		            },
		            allowEmpty: true,
		            showInput: true,
		            showPalette: isShowPalette,
		            palette: _palette
	            });
            }

        }
    }

    // Wiloke Tab View
    if(typeof vc !== 'undefined' && typeof vc.shortcode_view !== 'undefined')
    {
	    let Shortcodes = vc.shortcodes;
        window.WilokeTabView = vc.shortcode_view.extend({
            new_tab_adding: false,
            events: {
                'click .add_tab': 'addTab',
                'click > .vc_controls .vc_control-btn-delete': 'deleteShortcode',
                'click > .vc_controls .vc_control-btn-edit': 'editElement',
                'click > .vc_controls .vc_control-btn-clone': 'clone'
            },
            initialize: function (params) {
                window.WilokeTabView.__super__.initialize.call(this, params);
                _.bindAll(this, 'stopSorting');
            },
            render: function () {
                window.WilokeTabView.__super__.render.call(this); //make sure to call __super__. To execute logic fron inherited view. That way you can extend original logic. Otherwise, you will fully rewrite what VC will do at this event
                this.$tabs = this.$el.find('.wpb_tabs_holder');
                this.createAddTabButton();
                return this;
            },
            ready: function (e) {
                window.WilokeTabView.__super__.ready.call(this, e);
                return this;
            },
            createAddTabButton: function () {
	            let new_tab_button_id = (+new Date() + '-' + Math.floor(Math.random() * 11));
                this.$tabs.append('<div id="new-tab-' + new_tab_button_id + '" class="new_element_button"></div>');
                this.$add_button = $('<li class="add_tab_block"><a href="#new-tab-' + new_tab_button_id + '" class="add_tab" title="' + window.i18nLocale.add_tab + '"></a></li>').appendTo(this.$tabs.find(".tabs_controls"));
            },
            addTab: function (e) {
                e.preventDefault();
                this.new_tab_adding = true;
	            let tab_title = window.i18nLocale.tab,
                    tabs_count = this.$tabs.find('[data-element_type=single_tab]').length,
                    tab_id = (+new Date() + '-' + tabs_count + '-' + Math.floor(Math.random() * 11));
                vc.shortcodes.create({
                    shortcode: 'single_tab',
                    params: {title: tab_title, tab_id: tab_id},
                    parent_id: this.model.id
                });
                return false;
            },
            stopSorting: function (event, ui) {
                let shortcode;
                this.$tabs.find('ul.tabs_controls li:not(.add_tab_block)').each(function (index) {
	                let href = $(this).find('a').attr('href').replace("#", "");
                    shortcode = vc.shortcodes.get($('[id=' + $(this).attr('aria-controls') + ']').data('model-id'));
                    vc.storage.lock();
                    shortcode.save({'order': $(this).index()}); // Optimize
                });
                shortcode.save();
            },
            changedContent: function (view) {
	            let params = view.model.get('params');
                if (!this.$tabs.hasClass('ui-tabs')) {
                    this.$tabs.tabs({
                        select: function (event, ui) {
                            return $(ui.tab).hasClass('add_tab');
                        }
                    });
                    this.$tabs.find(".ui-tabs-nav").prependTo(this.$tabs);
                    this.$tabs.find(".ui-tabs-nav").sortable({
                        axis: (this.$tabs.closest('[data-element_type]').data('element_type') === 'test_element' ? 'y' : 'x'),
                        update: this.stopSorting,
                        items: "> li:not(.add_tab_block)"
                    });
                }
                if (view.model.get('cloned') === true) {
                    let cloned_from = view.model.get('cloned_from'),
                        $tab_controls = $('.tabs_controls > .add_tab_block', this.$content),
                        $new_tab = $("<li><a href='#tab-" + params.tab_id + "'>" + params.title + "</a></li>").insertBefore($tab_controls);
                    this.$tabs.tabs('refresh');
                    this.$tabs.tabs("option", 'active', $new_tab.index());
                } else {
                    $("<li><a href='#tab-" + params.tab_id + "'>" + params.title + "</a></li>")
                        .insertBefore(this.$add_button);
                    this.$tabs.tabs('refresh');
                    this.$tabs.tabs("option", "active", this.new_tab_adding ? $('.ui-tabs-nav li', this.$content).length - 2 : 0);

                }
                this.new_tab_adding = false;
            },
            cloneModel: function (model, parent_id, save_order) {
                let shortcodes_to_resort = [],
                    new_order = _.isBoolean(save_order) && save_order === true ? model.get('order') : parseFloat(model.get('order')) + vc.clone_index,
                    model_clone,
                    new_params = _.extend({}, model.get('params'));
                if (model.get('shortcode') === 'single_tab') _.extend(new_params, {tab_id: +new Date() + '-' + this.$tabs.find('[data-element-type=single_tab]').length + '-' + Math.floor(Math.random() * 11)});
                model_clone = Shortcodes.create({
                    shortcode: model.get('shortcode'),
                    id: vc_guid(),
                    parent_id: parent_id,
                    order: new_order,
                    cloned: (model.get('shortcode') === 'single_tab'),
                    cloned_from: model.toJSON(),
                    params: new_params
                });
                _.each(Shortcodes.where({parent_id: model.id}), function (shortcode) {
                    this.cloneModel(shortcode, model_clone.get('id'), true);
                }, this);
                return model_clone;
            }
        });
    }

    function select2Ajax() {
        let $select2 = $('.js_select2_ajax');
        $select2.select2({
            ajax: {
                type: 'GET',
                url: ajaxurl,
                delay: 250,
	            minimumInputLength: 2,
                data: function (params) {
                    return {
                        action: 'select2_get_posts',
                        post_type: $select2.parent().data('query'),
                        s: params.term
                    }
                },
	            processResults: function (data, params) {
		            return {
			            results: data.data
		            };
	            },
                cache: true
            }
        })
    }

	function select2UserAjax() {
		let $select2 = $('.js_select2_select_user_ajax');
		$select2.select2({
			ajax: {
				type: 'GET',
				url: ajaxurl,
				delay: 250,
				minimumInputLength: 2,
				data: function (params) {
					return {
						action: 'select_user_via_ajax',
						username: $select2.parent().data('query'),
						s: params.term
					}
				},
				processResults: function (data, params) {
					return {
						results: data.data
					};
				},
				cache: true
			}
		})
	}

	function select2WithoutAjax() {
		let $select2 = $('.js_select2_without_ajax');
		$select2.each(function () {
			let $this = $(this);
			$this.select2();

			if ( $this.data('fillto') ){
				$this.on("select2:select", function(evt) {
					$('[name="'+$this.data('fillto')+'"]').val(evt.params.data.id);
				});
			}
		})
	}

    $(document).ready(function () {
        $('.wiloke-colorpicker').each(function () {
            $(this).wilokeColorPicker();
        });

        let $sortAble = $('.wiloke-sortable');
        if ($sortAble.length > 0) {
	        $sortAble.sortable();
        }

        let $sidebarInfoRepeat = $("#sidebarinfo_repeat");
        if ($sidebarInfoRepeat.length > 0) {
	        $sidebarInfoRepeat.wilokePostFormatMediaPopup({
                return: 'url',
                multiple: false,
                isPreview: false,
                buttonClass: '.wiloke-button'
            });
        }

        $(document).on('click', '#wiloke-vc-listofterms-toggle-select', function (event) {
            event.preventDefault();

            if($(this).prev().find('option:selected').length) {
                $(this).prev().find('option:selected').each(function () {
                    $(this).prop('selected', false);
                })
            }else{
                $(this).prev().find('option').each(function () {
                    $(this).prop('selected', true);
                })
            }
        });

        if ( $().select2() ){
	        select2Ajax();
	        select2UserAjax();
	        select2WithoutAjax();
        }

    });


})(jQuery);