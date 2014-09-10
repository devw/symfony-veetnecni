/*global jQuery, window */
(function ($, _, humane) {
    'use strict';

    humane.info = humane.spawn({ addnCls: 'humane-info' });
    humane.success = humane.spawn({ addnCls: 'humane-success' });
    humane.warning = humane.spawn({ addnCls: 'humane-warning' });
    humane.error = humane.spawn({ addnCls: 'humane-error' });

    humane.timeout = 4000;

    // Adding listeners
    $(function () {
        _.forEach($('#flash-messages').find('.message'), function (message) {
            var $message = $(message);
            $message.detach();
            $message.find('button.close').remove();
            if ($message.hasClass('message-error')) {
                humane.error($message.text());
            } else if ($message.hasClass('message-warning')) {
                humane.warning($message.text());
            } else if ($message.hasClass('message-info')) {
                humane.info($message.text());
            } else if ($message.hasClass('message-success')) {
                humane.success($message.text());
            } else {
                humane.log($message.text());
            }

            $message.remove();
        });
    });
})(jQuery, window._, window.humane);
