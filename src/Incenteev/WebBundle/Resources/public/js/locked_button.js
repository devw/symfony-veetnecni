/*global jQuery, window, document */
(function ($, window, document) {
    'use strict';

    var translator = window.Translator;

    var toggleLockButton = function toggleLockButton (e) {
        e.preventDefault();
        var $this = $(this);
        var target = $this.closest('.btn-group').find('.button-locked');

        if (target.attr('disabled')) {
            target.removeAttr('disabled').removeClass('disabled');
            target.closest('.btn-group').tooltip('disable').tooltip('hide');
        } else {
            target.attr('disabled', 'disabled').addClass('disabled');
            target.closest('.btn-group').tooltip('enable').tooltip('show');
        }
        $this.find('i').toggleClass('sprite-icon-locked sprite-icon-unlocked');
    };

    $(function initLockButton () {
        var lockedButtons = $('.button-locked');
        lockedButtons.wrap('<div class="btn-group button-locked-group active" rel="tooltip" data-title="' + translator.get('messages:contest.help.button_disabled') + '"></div>');
        lockedButtons.after('<button type="button" class="btn button-add-on" data-toggle="locked-button"><i class="sprite-icon-locked"></i></button>');
        lockedButtons.attr('disabled', 'disabled').addClass('disabled');
    });

    $(document).on('click.locked-button.data-api', '[data-toggle="locked-button"]', toggleLockButton);

})(jQuery, window, document);
