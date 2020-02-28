(function($){

    var javoMailChimp = function( el ) {
        this.form = $( el );
        this.init();
    }

    javoMailChimp.prototype.constructor = javoMailChimp;
    javoMailChimp.prototype.init = function() {
        var self = this;
        self.form.on( 'submit', self.submit() );
    }

    javoMailChimp.prototype.submit = function() {
        var self = this;
        return function( event ) {
            event.preventDefault();

            var wp_once	= $( this ).find( "input[name='lynk_mailchimp_security']" ).val();
            var cm_list	= $( this ).find( "input[name='cm_list']" ).val();
            var ajaxurl	= $( this ).find( "input[name='ajaxurl']" ).val();
            var ok_msg	= $( this ).find( "input[name='ok_msg']" ).val();
            var no_msg	= $( this ).find( "input[name='no_msg']" ).val();
            var param	= {
                action		: 'lynk_mailchimp'
                , mc_email	: $('#lynk_cmp_email').attr('value')
                , yname		: $('#lynk_cmp_name').attr('value')
                , list		: cm_list
                , nonce		: wp_once
            };

            $.ajax({
                url			: ajaxurl
                , type		: 'POST'
                , data		: param
                , dataType	: 'JSON'
                , success	: function( xhr ) {
                    jQuery.lynk_msg({ content: xhr.message, delay:10000 });
                    console.log( xhr );
                },
                error: function( xhr ) {
                    jQuery.lynk_msg({ content: no_msg, delay:10000 });
                }
            });


        }
    }

    $( 'form#newsletter-form' ).each( function() {
        $( this ).data( 'javo-mailchimp', new javoMailChimp( this ));
    });

})(jQuery);