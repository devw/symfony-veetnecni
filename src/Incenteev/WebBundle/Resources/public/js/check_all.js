/*global jQuery, document */
(function ($, document) {
    'use strict';

    var checkAll = function checkAll (e) {
        e.preventDefault();
        var $this = $(this);
        var target = $this.attr('data-target');
        $(target).prop('checked', $this.prop('checked'));
    };

    $(document).on('change.check-all.data-api', '[data-toggle="check-all"]', checkAll);
})(jQuery, document);
