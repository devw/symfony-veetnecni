/*global jQuery */
(function ($) {
    'use strict';

    var changeHandler = function changeHandler (e) {
        var form = $(e.currentTarget);
        var output = form.find(form.attr('data-total-target'));
        var inputs = form.find(form.attr('data-item-target'));
        var total = 0;

        inputs.each(function () {
            var value = parseFloat(this.value);

            if (isNaN(value)) {
                value = 0;
            }

            total += value;
        });

        output.text(Math.round(total * 100000) / 100000);
    };

    $(function () {
        $('body').on('change.total-calculator.data-api', 'form[data-item-target][data-total-target]', changeHandler);
    });
})(jQuery);
