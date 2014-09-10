/*global jQuery, document, setTimeout*/
(function ($, document) {
    'use strict';

    var setTimeAgo = function () {
        $(this).find('time').timeago();
    };

    $(document).on('fos_comment_load_thread', '[data-thread]', setTimeAgo);
    $(document).on('fos_comment_add_comment', '.fos_comment_comment_new_form, .fos_comment_comment_replies', function (event, commentHtml) {
        var comment_id =  '#' + $(commentHtml).attr('id');

        setTimeAgo.apply($('[data-thread]').find(comment_id));
    });
})(jQuery, document);
