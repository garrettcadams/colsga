(function($) {
    $(function() {
        
        /****************  colour picker *****************/            
        $( '.bdotcom_bc_wp_color_picker' ).wpColorPicker();
        
        /****************  start media uploader *****************/   
        var file_frame;
        
          jQuery('.bdotcom_bc_mbe_img_uploader_button').live('click', function( event ){
        
            event.preventDefault();
        
            // If the media frame already exists, reopen it.
            if ( file_frame ) {
              file_frame.open();
              return;
            }
        
            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
              title: jQuery( this ).data( 'uploader_title' ),
              button: {
                text: jQuery( this ).data( 'uploader_button_text' ),
              },
              multiple: false  // Set to true to allow multiple files to be selected
            });
        
            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {
              // We set multiple to false so only get one image from the uploader
              attachment = file_frame.state().get('selection').first().toJSON();
        
              // Do something with attachment.id and/or attachment.url here
               jQuery('#bdotcom_bc_mbe_img_path').val( attachment.url );
            });
        
            // Finally, open the modal
            file_frame.open();
          });
  
        /****************  end media uploader *****************/ 
        
  
        /****************  start field onchange *****************/
       
       $('#bdotcom_bc_mbe_button').change( function(){
            if( $( '#bdotcom_bc_mbe_button:checked').val() ) {
                $( '#bdotcom_bc_mbe_button_block' ).show('slow');
            } else { 
                $( '#bdotcom_bc_mbe_button_block' ).hide('slow');
            }   
       });
       
      /*$('#bdotcom_bc_mbe_themes').change( function(){
            if( $('#bdotcom_bc_mbe_themes').val() == 'custom_theme'  ) {
                $( '#bdotcom_bc_mbe_img_path_wrapper' ).show('slow');
                $( '#bdotcom_bc_theme_preview' ).hide('slow');
            } else {
                //var bdotcom_bc_ajax_loader = '<img src=\"' + objectL10n.bdotcom_bc_images_js_path + '/"ajax-loader32x32.gif" >';               
                var bdotcom_bc_ajax_loader = '<img class="bdotcom_bc_ajax_loader_image" src=\"'+ objectL10n.bdotcom_bc_images_js_path +'/ajax-loader32x32.gif\" >';    
                
                $('#bdotcom_bc_theme_preview').prepend( bdotcom_bc_ajax_loader ); 
                var data = {// run the preview
                    action: 'bdotcom_bc_theme_preview', // The function for handling the request                
                    bdotcom_bc_mbe_themes: $('#bdotcom_bc_mbe_themes').val(), // banner theme  
                    bdotcom_bc_ajax_nonce: $('#bdotcom_bc_ajax_nonce').text() // The security nonce  
                };
                $.post(ajaxurl, data, function(response) {
                    $('#bdotcom_bc_theme_preview').html( response );
                });
                $( '#bdotcom_bc_theme_preview' ).show('slow'); 
                $( '#bdotcom_bc_mbe_img_path_wrapper' ).hide('slow'); 
                
            }//if( $('#bdotcom_bc_mbe_themes').val() == 'custom_theme'  )           
       });*/



      $('#bdotcom_bc_show_defaults_themes').click( function(){
                $( '#bdotcom_bc_mbe_img_path_wrapper' ).fadeOut('slow');
                $( '#bdotcom_bc_default_themes_box' ).fadeIn('slow');                      

       });

      $('#bdotcom_bc_custom_theme').click( function(){
                $( '#bdotcom_bc_mbe_img_path_wrapper' ).fadeIn('slow');
                $( '#bdotcom_bc_default_themes_box' ).fadeOut('slow');
                $( '#bdotcom_bc_theme_preview' ).hide();
                $( '#bdotcom_bc_mbe_themes' ).val('custom_theme');      

       });

      $('.bdotcom_bc_thumbnail').click( function(){
                /*Get the name of the image to be placed into the hidden input */
                //var bdotcom_bc_default_theme_image_name = this.src ;
                //var bdotcom_bc_default_theme_image_name_array = this.src.split("/");

                //var bdotcom_bc_last_default_image = bdotcom_bc_default_theme_image_name_array[bdotcom_bc_default_theme_image_name_array.length - 1] ;
                $( '#bdotcom_bc_mbe_themes' ).val( this.id );
                $('#bdotcom_bc_theme_preview').empty();
                /* load the image thumbnail into the bdotcom_bc_theme_preview */
                 var bdotcom_bc_ajax_loader = '<img class="bdotcom_bc_ajax_loader_image" src=\"'+ objectL10n.bdotcom_bc_images_js_path +'/ajax-loader32x32.gif\" >';    
                
                $('#bdotcom_bc_theme_preview').show().prepend( bdotcom_bc_ajax_loader ); 
                var data = {// run the preview
                    action: 'bdotcom_bc_theme_preview', // The function for handling the request                
                    bdotcom_bc_mbe_themes: this.src, // banner theme  
                    bdotcom_bc_ajax_nonce: $('#bdotcom_bc_ajax_nonce').text() // The security nonce  
                };
                $.post(ajaxurl, data, function(response) {
                    $('#bdotcom_bc_theme_preview').html( response );
                });

                /* close the box with thumbnails*/
                $('#bdotcom_bc_default_themes_box').fadeOut( "slow" );

      });

      $( '#bdotcom_bc_default_themes_box_black_overlay' ).on( 'click','', function(e){ //user click on remove text*/         
          e.preventDefault(); 
          $( this ).parent( 'div' ).fadeOut( "slow" );         
          
      });


 
       /****************  end field onchange *****************/  
        
      /****************  start ajax preview *****************/ 
      $('#bdotcom_bc_mbe_preview_button').click( function(){
            $('#bdotcom_bc_mbe_preview_wrapper').css('opacity', '0.5');

            var data = {

                action: 'bdotcom_bc_preview', // The function for handling the request                
                bdotcom_bc_mbe_post_id: $('#bdotcom_bc_mbe_post_id').val(), // post  ID
                bdotcom_bc_ajax_nonce: $('#bdotcom_bc_ajax_nonce').text(), // The security nonce
                bdotcom_bc_mbe_aid: $('#bdotcom_bc_mbe_aid').val(), // affiliate ID  
                //bdotcom_bc_mbe_logo_var: $('.bdotcom_bc_mbe_logo_var:checked').val(), // logo variant 
                bdotcom_bc_mbe_button: $( '#bdotcom_bc_mbe_button:checked').val(),//have we a button on banner ? // if unchecked will sent an undefined value
                bdotcom_bc_mbe_button_copy: $( '#bdotcom_bc_mbe_button_copy').val(),// button copy                  
                bdotcom_bc_mbe_button_copy_colour : $( '#bdotcom_bc_mbe_button_copy_colour').val(),// button copy  colour   
                bdotcom_bc_mbe_button_bg: $( '#bdotcom_bc_mbe_button_bg').val(),// button bg
                bdotcom_bc_mbe_button_border_colour: $( '#bdotcom_bc_mbe_button_border_colour').val(),// button border color
                bdotcom_bc_mbe_button_border_width: $( '#bdotcom_bc_mbe_button_border_width').val(),// button border width
                bdotcom_bc_mbe_themes: $('#bdotcom_bc_mbe_themes').val(), // banner theme
                bdotcom_bc_mbe_img_path:  $('#bdotcom_bc_mbe_img_path').val(), // banner custom image path
                bdotcom_bc_mbe_copy: $('#bdotcom_bc_mbe_copy').val(), // banner copy
                bdotcom_bc_mbe_copy_colour: $('#bdotcom_bc_mbe_copy_colour').val(), // banner copy colour
                //bdotcom_bc_mbe_copy_bg_color: $('#bdotcom_bc_mbe_copy_bg_color').val(), // banner copy background colour
                bdotcom_bc_mbe_edit_css: $('#bdotcom_bc_mbe_edit_css').val(), // custom css  
                bdotcom_bc_mbe_banner_link: $('#bdotcom_bc_mbe_banner_link').val(), // link 
                bdotcom_bc_mbe_label  : $('#bdotcom_bc_mbe_label').val() // label           
            };

            $.post(ajaxurl, data, function(response) {

                $('#bdotcom_bc_mbe_preview_wrapper').html( response );
                $('#bdotcom_bc_mbe_preview_wrapper').css('opacity', '1');
            });     

                       
      });//$('#bdotcom_bc_mbe_preview_button').click( function(){
       
     /****************  end  ajax preview *****************/
    
     /****************  start label field auto fill *****************/
    $('#title').blur(function(){
        //console.log("onblur fired");
        var bdotcom_bc_title =  $('#title').val();
        if( bdotcom_bc_title.length  > 0  ) {
            // here needs to replace eventual blank spaces with '-'
            $( '#bdotcom_bc_mbe_label' ).val( bdotcom_bc_title.replace(/\s|'/g, '-') );
        }        
        else {
            $( '#bdotcom_bc_mbe_label' ).val( '' );
        }
    });
    /****************  end label field auto fill *****************/
                                    
        /*$( window ).resize( function() {
            $('#width_test').html( $( window ).width() );
         });*/
               
    });//$(function() {
})(jQuery);

