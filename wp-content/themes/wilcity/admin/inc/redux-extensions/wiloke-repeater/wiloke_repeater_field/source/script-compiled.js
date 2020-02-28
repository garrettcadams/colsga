;(function ($) {
    "use strict";

    $(document).ready(function () {
        $('.wiloke-redux-addnew').on('click', function (event) {
            event.preventDefault();
            var _contentHTML = $(this).next().html();
            $(_contentHTML).find('input').attr('id', '');
            var $wilokeAppendTo = $(this).parent().prev();

            $wilokeAppendTo.append(_contentHTML);

            if ($wilokeAppendTo.hasClass('wiloke-sortable')) {
                $wilokeAppendTo.sortable("destroy");
                $wilokeAppendTo.sortable({
                    cursor: "move"
                });
            }
        });

        $('.wiloke-redux-add-new-group').each(function () {
            var $template = $(this).prev().find('.wiloke-redux-group-item:first').html();
            $(this).data('template', $template);
        });

        $('.wiloke-redux-repeater-wrapper').on('click', '.wiloke-redux-delete', function (event) {
            event.preventDefault();
            var _isConfirm = confirm('Do you want to remove this group?');

            if (_isConfirm) {
                var $target = $(this).closest('.wiloke-redux-group-item');
                $target.slideUp(function () {
                    $target.remove();
                });
            }
        });

        $('.wiloke-redux-add-new-group').on('click', function (event) {
            event.preventDefault();
            var _template = $(this).data('template'),
                $parent = $(this).prev();
            _template = '<div class="wiloke-redux-group-item new-item">' + _template + '</div>';

            $parent.append(_template);
            var $target = $('.wiloke-redux-group-item.new-item');

            $target.find('.regular-text, [class*="upload"]').attr('value', '');
            $target.find('.redux-option-image').attr('src', '');
            $target.find(':selected').each(function () {
                $(this).prop('selected', false);
            });

            $target.find('input:checked').each(function () {
                $(this).prop('checked', false);
            });

            $target.find('input, select').attr('id', '');

            $target.find('[name]').each(function () {
                var _name = $(this).attr('name'),
                    newName = _name.replace(/\[[0-9]\]/gi, '[' + Math.floor(Date.now() / 1000) + ']');
                $(this).attr('name', newName);
            });

            if ($target.find('.media_upload_button').length) {
                $target.find('.media_upload_button').each(function () {
                    var $this = $(this),
                        uniqueID = Math.floor(Date.now() / 1000);

                    $this.attr('id', 'bg' + uniqueID + '-media');
                    $this.next().attr({
                        id: 'reset_bg' + uniqueID,
                        rel: 'bg' + uniqueID
                    });
                    $this.closest('.wiloke-field').find('input').each(function () {
                        var itemClass = $(this).attr('class'),
                            suffix = itemClass.replace('upload-', '');

                        if (itemClass.search('upload-') != -1) {
                            $(this).attr('id', 'wiloke_themeoptions[bg' + uniqueID + '][' + suffix + ']');
                        } else if (itemClass.search('upload') != -1) {
                            $(this).attr('id', 'wiloke_themeoptions[bg' + uniqueID + '][url]');
                        }
                        redux.field_objects.media.init($(this).closest('.redux-container-media'));
                    });
                });
            }

            $target.removeClass('new-item');

            if ($parent.hasClass('wiloke-sortable')) {
                $parent.sortable('destroy');
                $parent.sortable({
                    cursor: "move"
                });
            }
        });
    });
})(jQuery);

//# sourceMappingURL=script-compiled.js.map