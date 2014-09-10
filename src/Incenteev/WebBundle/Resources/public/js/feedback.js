/*global jQuery, window */
(function ($, window) {
    'use strict';

    var formElement;
    var messageElement;
    var submitButtonElement;
    var focusedClass = 'is-form-focused';
    var translator = window.Translator;
    var humane = window.humane;

    var focusFeedbackForm = function (event) {
        formElement = $(event.currentTarget);
        messageElement = formElement.find('[data-tid="message"]');
        submitButtonElement = formElement.find('[type="submit"]');

        formElement.addClass(focusedClass);
    };

    var unfocusFeedbackForm = function () {
        if (!messageElement.val().length) {
            formElement.removeClass(focusedClass);
        }
    };

    var sendFeedback = function (event) {
        var message = messageElement.val();

        if (formElement.checkValidity && !formElement.checkValidity()) {
            onFeedbackFail();
        } else {
            submitButtonElement.button('loading');
            $.ajax({
                url: formElement.attr('action'),
                type: 'POST',
                dataType: 'json',
                data: {
                    message: message
                }
            }).done(onFeedbackSuccess.bind(this)).fail(onFeedbackError.bind(this));
        }

        event.preventDefault();
    };

    var onFeedbackSuccess = function () {
        messageElement.val('');
        formElement.removeClass(focusedClass);
        submitButtonElement.button('reset');
        humane.success(translator.get('messages:feedback.flash.sent'));
    };

    var onFeedbackFail = function () {
        messageElement.focus();
        submitButtonElement.button('reset');
    };

    var onFeedbackError = function (jqXHR) {
        onFeedbackFail();
        if (412 === jqXHR.status) {
            humane.error(translator.get('messages:feedback.flash.empty'));
        } else {
            humane.error(translator.get('messages:feedback.flash.error'));
        }
    };

    // Adding listeners
    $(function () {
        $('body')
        .on('focus', '.form-feedback', focusFeedbackForm)
        .on('blur', '.form-feedback', unfocusFeedbackForm)
        .on('submit', '.form-feedback', sendFeedback);
    });
})(jQuery, window);
