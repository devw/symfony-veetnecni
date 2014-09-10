/*global jQuery, Placeholders */
(function ($, Placeholders) {
    'use strict';

    //BootStrap buttons
    $('body').on('submit', 'form', function (event) {
        var $currentTarget = $(event.currentTarget);

        $currentTarget.find('.button-submit').button('loading');
    });

    //Placeholders polyfill
    Placeholders.init({
        live: true
    });

    // jQuery Autosize. It enables automatic height for textarea elements.
    $('textarea').autosize();

    // Form FOSCommentBundle
    var focusedClass = 'is-form-focused';
    var focusCommentForm = function (event) {
        $(event.currentTarget).addClass(focusedClass);
    };
    var unfocusCommentForm = function (event) {
        var $formElement = $(event.currentTarget);
        var messageElementValue = $formElement.find('[data-tid="post-message"]').val();

        if (!messageElementValue.length) {
            $formElement.removeClass(focusedClass);
        }
    };
    var onAddMessage = function (event) {
        var $formElement = $(event.currentTarget);

        $formElement.find('.button-submit').button('reset');
        $formElement.removeClass(focusedClass);
    };
    var focusReplyForm = function (event) {
        $(event.currentTarget).closest('article').find('[data-tid="post-message"]').focus();
    };

    $(function () {
        $('body')
            .on('focus', '.fos_comment_comment_new_form', focusCommentForm)
            .on('blur', '.fos_comment_comment_new_form', unfocusCommentForm)
            .on('click', '.fos_comment_comment_reply', focusReplyForm)
            .on('fos_comment_show_form', '.fos_comment_comment_reply_show_form', focusReplyForm)
            .on('fos_comment_new_comment', '.fos_comment_comment_new_form', onAddMessage)
        ;
    });
})(jQuery, Placeholders);
