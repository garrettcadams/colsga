;(function($, window, document, undefined){
    "use strict"
    if ( !$().piGetListsOfMailchimp )
    {
        $.fn.piGetListsOfMailchimp = function()
        {
            var $self = $(this), _oLists, _oData = {},  _list="", _val="";
            if ( $self.attr("id") )
            {
                $self.on("click", function()
                {   
                    $self.prop("disabled", true);
                    $self.html("Processing");
                    _val = $self.prev().val();

                    if ( _val !== "" )
                    {
                        $.ajax(
                            {
                                url: ajaxurl,
                                type: "POST",
                                data: {action: 'pi_mailchimp_get_lists', api_key: _val},
                                success: function(data, textStatus, jqXHR)
                                {
                                    _oData = JSON.parse(data);
                                    if ( _oData.type==="error" )
                                    {
                                        alert(_oData.msg);

                                    }else{
                                        _oLists = JSON.parse(_oData.data);

                                        $.each(_oLists, function(_id, _name)
                                        {
                                            _list += '<option value="'+_id+'">'+_name+'</option>';
                                        });

                                        $self.closest(".wrapper").next().find("#pi_mailchimp_lists").html(_list);
                                        
                                    }
                                    $self.html("Get Lists");
                                    $self.prop("disabled", false);
                                }

                            });
                    }else{
                        alert("Please enter maichimp api key!");
                    }

                    return false;
                });
            }
        }
    }

    $(document).ready(function () {
        $("#pi_get_list_id").piGetListsOfMailchimp();
    });

})(jQuery, window, document);