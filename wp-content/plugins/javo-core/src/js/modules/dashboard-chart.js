(function ($) {
    var jvbpdMyPage = function () {
        this.setChart();
        // this.setEventUploader();
    }

    jvbpdMyPage.prototype.constructor = jvbpdMyPage;
    jvbpdMyPage.prototype.setChart = function () {
        var
            obj = this,
            elements = $('canvas.bp-mydahsobard-report-chart');
        if (!elements.length) {
            return false;
        }

        elements.map(function () {
            var element = $(this);
            new Chart(element.get(0).getContext('2d'), {
                type: element.data('graph') || 'line',
                data: obj.parseChartData(element.data('values'), element.data('limit'), element.data('type')),
                options: {
                    responsive: true,
                    title: {
                        display: false,
                        text: 'Chart.js Line Chart'
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    },
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: element.data('x')
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: element.data('y')
                            }
                        }]
                    }
                }
            });
        });
    }

    jvbpdMyPage.prototype.parseChartData = function (data, limit, type) {
        var
            obj = this,
            current_time = new Date(),
            start_time = new Date(),
            chart_data = {
                months: new Array(),
                color: new Array(),
                data: new Array()
            },
            _limit = parseInt(limit) || 0,
            _type = parseInt(type) || 0;

        switch (_type) {
            case 0:
                start_time.setDate(current_time.getDate() - _limit);
                break;

            case 2:
                start_time.setMonth(current_time.getMonth() - _limit);
                break;
        }


        while (start_time < current_time) {

            switch (_type) {

                case 0:
                    start_time.setDate(start_time.getDate() + 1);
                    chart_data.months.push(
                        start_time.getFullYear().toString() +
                        ('0' + (start_time.getMonth() + 1).toString()).split(-2) +
                        ('0' + (start_time.getDate() + 1).toString()).split(-2)
                    );
                    break;

                case 2:
                    start_time.setMonth(start_time.getMonth() + 1);
                    chart_data.months.push(start_time.getFullYear().toString() + ('0' + (start_time.getMonth() + 1).toString()).split(-2));
                    break;

            }
        }

        $.each(data, function (intPostID, arrChartMeta) {
            var
                count_data = new Array(),
                start_time = new Date();

            switch (_type) {
                case 0:
                    start_time.setDate(current_time.getDate() - _limit);
                    break;
                case 2:
                    start_time.setMonth(current_time.getMonth() - _limit);
                    break;
            }
            while (start_time < current_time) {
                var _thisItemCount = 0;

                switch (_type) {
                    case 0:
                        start_time.setDate(start_time.getDate() + 1);
                        break;
                    case 2:
                        start_time.setMonth(start_time.getMonth() + 1);
                        break;
                }

                $.each(arrChartMeta.values, function (intvalueKey, arrValueMeta) {
                    var _thisItemTime;
                    switch (_type) {
                        case 0:
                            _thisItemTime = new Date(arrValueMeta.period.substring(0, 4) + '-' + arrValueMeta.period.substring(4, 6) + '-' + arrValueMeta.period.substring(6));
                            break;
                        case 2:
                            _thisItemTime = new Date(arrValueMeta.period.substring(0, 4) + '-' + arrValueMeta.period.substring(4) + '-01');
                            break;
                    }

                    if (_type == 0 && (start_time.getFullYear() == _thisItemTime.getFullYear() && start_time.getMonth() == _thisItemTime.getMonth() && start_time.getDate() == _thisItemTime.getDate())) {
                        _thisItemCount = arrValueMeta.count;
                        return false;
                    }

                    if (_type == 2 && (start_time.getFullYear() == _thisItemTime.getFullYear() && start_time.getMonth() == _thisItemTime.getMonth())) {
                        _thisItemCount = arrValueMeta.count;
                        return false;
                    }
                });
                count_data.push(_thisItemCount);
            }

            chart_data.data.push({
                label: arrChartMeta.title,
                backgroundColor: arrChartMeta.color,
                borderColor: arrChartMeta.color,
                data: count_data,
                fill: '+2',
            });
            chart_data.color.push(arrChartMeta.color);
        });

        return {
            labels: chart_data.months,
            datasets: chart_data.data

        };
    }

    jvbpdMyPage.prototype.setEventUploader = function() {

        var obj = this;

         /* Apply jquery ui sortable on gallery items */
        $( "#lava-multi-uploader" ).sortable({
            revert: 100,
            placeholder: "sortable-placeholder",
            cursor: "move"
        });

        /* initialize uploader */
        var uploaderArguments = {
            browse_button: 'select-images',          // this can be an id of a DOM element or the DOM element itself
            file_data_name: 'lava_multi_uploader',
            drop_element: 'lava-multi-uploader-drag-drop',
            url: obj.param.ajaxurl + '?action=' + obj.param.event_hook + 'upload_detail_images',
            filters: {
                mime_types : [
                    { title : 'image', extensions : "jpg,jpeg,gif,png" }
                ],
                max_file_size: '10000kb',
                prevent_duplicates: true
            }
        };


        var uploader = new plupload.Uploader( uploaderArguments );
        uploader.init();

        $('#select-images').click(function(event){
            event.preventDefault();
            event.stopPropagation();
            uploader.start();
        });

        /* Run after adding file */
        uploader.bind('FilesAdded', function(up, files) {
            var html = '';
            var galleryThumb = "";
            plupload.each(files, function(file) {
                galleryThumb += '<div id="holder-' + file.id + '" class="gallery-thumb">' + '' + '</div>';
            });
            document.getElementById('lava-multi-uploader').innerHTML += galleryThumb;
            up.refresh();
            uploader.start();
        });


        /* Run during upload */
        uploader.bind('UploadProgress', function(up, file) {
            document.getElementById( "holder-" + file.id ).innerHTML = '<span>' + file.percent + "%</span>";
        });


        /* In case of error */
        uploader.bind('Error', function( up, err ) {
            document.getElementById('errors-log').innerHTML += "<br/>" + "Error #" + err.code + ": " + err.message;
        });


        /* If files are uploaded successfully */
        uploader.bind('FileUploaded', function ( up, file, ajax_response ) {
            var response = $.parseJSON( ajax_response.response );

            if ( response.success ) {

                var galleryThumbHtml = '<img src="' + response.url + '" alt="" />' +
                '<a class="remove-image" data-event-id="' + 0 + '"  data-attachment-id="' + response.attachment_id + '" href="#remove-image" ><i class="fa fa-trash-o"></i></a>' +
                '<a class="mark-featured" data-event-id="' + 0 + '"  data-attachment-id="' + response.attachment_id + '" href="#mark-featured" ><i class="fa fa-star-o"></i></a>' +
                '<input type="hidden" class="gallery-image-id" name="gallery_image_ids[]" value="' + response.attachment_id + '"/>' +
                '<span class="loader"><i class="fa fa-spinner fa-spin"></i></span>';

                document.getElementById( "holder-" + file.id ).innerHTML = galleryThumbHtml;

                bindThumbnailEvents();  // bind click event with newly added gallery thumb
            } else {
                // log response object
                console.log ( response );
            }
        });

        /* Bind thumbnails events with newly added gallery thumbs */
        var bindThumbnailEvents = function () {

            // unbind previous events
            $('a.remove-image').unbind('click');
            $('a.mark-featured').unbind('click');

            // Mark as featured
            $('a.mark-featured').click(function(event){

                event.preventDefault();

                var $this = $( this );
                var starIcon = $this.find( 'i');

                if ( starIcon.hasClass( 'fa-star-o' ) ) {   // if not already featured

                    $('.gallery-thumb .featured-img-id').remove();      // remove featured image id field from all the gallery thumbs
                    $('.gallery-thumb .mark-featured i').removeClass( 'fa-star').addClass( 'fa-star-o' );   // replace any full star with empty star

                    var $this = $( this );
                    var input = $this.siblings( '.gallery-image-id' );      //  get the gallery image id field in current gallery thumb
                    var featured_input = input.clone().removeClass( 'gallery-image-id' ).addClass( 'featured-img-id' ).attr( 'name', 'featured_image_id' );     // duplicate, remove class, add class and rename to full fill featured image id needs

                    $this.closest( '.gallery-thumb' ).append( featured_input );     // append the cloned ( featured image id ) input to current gallery thumb
                    starIcon.removeClass( 'fa-star-o' ).addClass( 'fa-star' );      // replace empty star with full star

                }

            }); // end of mark as featured click event


            // Remove gallery images
            $('a.remove-image').click(function(event){

                event.preventDefault();
                var $this = $(this);
                var gallery_thumb = $this.closest('.gallery-thumb');
                var loader = $this.siblings('.loader');

                loader.show();

                var removal_request = $.ajax({
                    url: obj.param.ajaxurl,
                    type: "POST",
                    data: {
                        property_id : $this.data('event-id'),
                        attachment_id : $this.data('attachment-id'),
                        action : obj.param.event_hook + 'remove_detail_images',
                    },
                    dataType: "html"
                });

                removal_request.done(function( response ) {
                    var result = $.parseJSON( response );
                    if( result.attachment_removed ){
                        gallery_thumb.remove();
                    } else {
                        document.getElementById('errors-log').innerHTML += "<br/>" + "Error : Failed to remove attachment";
                    }
                });

                removal_request.fail(function( jqXHR, textStatus ) {
                    alert( "Request failed: " + textStatus );
                });

            });  // end of remove gallery thumb click event

        };  // end of bind thumbnail events

        bindThumbnailEvents(); // run it first time - required for property edit page
    }
    new jvbpdMyPage;
})(window.jQuery);