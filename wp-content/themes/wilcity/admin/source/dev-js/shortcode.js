!function($) {

}(window.jQuery);

function wiloke_vc_list_of_posts(sel)
{
    var $ = jQuery,
        $title = $(sel).closest('.wiloke_vc_list_of_posts').find('.wiloke_post_title span');

    $(sel).closest('.wiloke_vc_list_of_posts_wrapper').find('.wpb_vc_param_value').val( $( sel ).val() );
    $(sel).closest('.wiloke_vc_list_of_posts').find('.wiloke-pattern-bg').removeClass('wiloke-vc-checked');
    $(sel).next().addClass('wiloke-vc-checked');

    $title.html($(sel).next().attr('title'));
}

function wiloke_ajax_loadmore()
{
    var $ = jQuery;
    var paged = 1;

    $(".wiloke_vc_ajax_list_of_posts").on('click', function(){
        var $this = $(this),
            id    = $this.attr('id');
            oInfo =  WilokeAdminGlobal.ajaxinfo[id];

            if ( oInfo != '' )
            {
                oInfo = $.parseJSON(oInfo);
            }

            console.log(paged);

            if ( paged  == 1 )
            {
                oInfo.paged = 2;
            }else{
                oInfo.paged = paged;
            }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {args: oInfo, action:'wiloke_loadmore_posts', post_type: oInfo.post_type},
            success: function (res)
            {
               if ( res != '' )
               {
                   res = $.parseJSON(res);

                   if ( res.data )
                   {
                       var oData = res.data;
                       for ( var i=0; i<= (oData.length - 1); i++ )
                       {
                            var render_html = '<label>';
                            render_html += '<input type="radio" value="'+oData[i].post_id+'" onclick="wiloke_vc_list_of_posts(this)">';
                            render_html += '<span title="'+oData[i].title+'" class="wiloke-pattern-bg" style="background:url('+oData[i].thumbnail+')"></span>';
                            render_html += '</label>';
                            $this.siblings('.wiloke_vc_list_of_posts').append(render_html);

                            if ( paged == '-1' )
                            {
                                $this.remove();
                            }else{
                                paged = res.next_page;
                            }

                       }
                   }
                   
               }
            }
        })
    })
}
