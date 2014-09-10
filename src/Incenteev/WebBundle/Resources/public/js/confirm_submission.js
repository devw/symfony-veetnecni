/*global jQuery, window, document */
(function ($, window, document) {
    'use strict';

    var confirmHandler = function confirmHandler (e) {
        var $this = $(this);
        var launch = window.confirm($this.attr('data-confirm-submission'));

        if (!launch) {
            e.preventDefault();
            $this.find('[type="submit"]').button('reset');
        }
    };

    $(document).on('submit.confirm-submission.data-api', 'form[data-confirm-submission]', confirmHandler);
})(jQuery, window, document);
