(function($){
    var jvbpd_admin = function() {
        this.init();
    }

    jvbpd_admin.prototype.constructor = jvbpd_admin;
    jvbpd_admin.prototype.init = function() {
        var self = this;
        self.megaMenuSettings();
    }

    jvbpd_admin.prototype.megaMenuSettings = function() {
        $('.jvbpd-field.field-post_type select').on('change', function(event){
            var
                $this = $(this),
                value = $this.val() || 'post',
                parent = $this.closest('.menu-item-settings');

            $('.jvbpd-field.field-tax-post, .jvbpd-field.field-tax-lv_listing', parent).addClass('hidden');
            $('.jvbpd-field.field-tax-' + value, parent).removeClass('hidden');
        }).trigger('change');
    }

    window.jvbpd_admin = new jvbpd_admin;
})(jQuery);