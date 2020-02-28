;(function($){
    $(document).ready(function () {
        $("#wiloke-import-contactform7").on('click', function(event){

            event.preventDefault();

            var $this = $(this);

            if ( $this.data('is_ajax') == true )
            {
                return false;
            }

            $this.data('is_ajax', true);

            $this.html('Processing...');
            
            $.ajax({
                type: 'POST',
                data: {action:'wiloke_contactform7_demo', 'import_of': $this.closest("#wilokecontactform7").find('[name="contactform7"]').val()},
                url: ajaxurl,
                success: function (contactform) {
                    $('#wpcf7-form').html(contactform);
                    $this.data('is_ajax', false);
                    $this.html('Import');
                }
            });

        })
    })
})(jQuery);