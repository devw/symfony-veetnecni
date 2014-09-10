/*global jQuery, window, document */
(function ($, window, document) {
    'use strict';

    var unloadCheckedFormSelector = 'form[data-check-unload]';

    var checkerFactory = function checkerFactory (collection, whitelistedElement) {
        return function checker () {
            var changed = false;

            collection.each(function () {
                if (this === whitelistedElement) {
                    return;
                }

                var $this = $(this);

                if ($this.data('initialForm') != $this.serialize()) {
                    changed = true;
                    $this.addClass('changed');
                } else {
                    $this.removeClass('changed');
                }
            });

            return changed;
        };
    };

    var catcher = function () {
        var checker = checkerFactory($(unloadCheckedFormSelector));

        if (checker()) {
            return window.Translator.get('messages:form.check.unload');
        }
    };

    var catcherOnPjax = function () {
        var checker = checkerFactory($(unloadCheckedFormSelector));

        if (checker()) {
            return window.confirm(window.Translator.get('messages:form.check.unload'));
        }
    };

    var initialize = function () {
        var $forms = $(unloadCheckedFormSelector);

        $forms.each(function () {
            var $this = $(this);

            $this.data('initialForm', $this.serialize());
        });
    };

    $(function () {
        var $window = $(window);
        initialize();
        $window.on('beforeunload', catcher);

        $(document).on('submit', unloadCheckedFormSelector, function formSubmitChecker () {
            var checker = checkerFactory($(unloadCheckedFormSelector), this);

            // Remove the catcher if there is no other modified forms, to let this form be submitted.
            if (!checker()) {
                $window.off('beforeunload', catcher);
            }
        }).on('change', unloadCheckedFormSelector, function formChangeChecker () {
            var checker = checkerFactory($(this));

            checker();
        }).on('pjax:beforeSend', catcherOnPjax);
    }).on('pjax:success', initialize);
})(jQuery, window, document);
