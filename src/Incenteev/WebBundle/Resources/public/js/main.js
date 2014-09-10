/*global jQuery, window, document, Modernizr, setTimeout */
(function ($, Modernizr, document, window) {
    'use strict';

    var initialize = function () {
        // Chosen
        $('[data-tid="chosen"]').chosen();

        $('.view-more').expander({
            slicePoint: 300,
            expandText: window.Translator.get('messages:layout.action.view_more'),
            userCollapseText: window.Translator.get('messages:layout.action.view_less')
        });

        //Input Type Date Support
        if (!Modernizr.inputtypes.date || Modernizr.mq('(min-width: 768px)')) {
            var inputs = $('.date-picker').datepicker({
                language: $(document.documentElement).attr('lang'),
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            //Changes input type to text for web browsers supporting native datepickers, so that we don't have a conflict
            //between native ones and those using jQuery.
            if (Modernizr.inputtypes.date) {
                inputs.each(function () {
                    this.type = 'text';
                });
            }
        }

        // Fading out messages/notices
        setTimeout(function () {
            $('#flash-messages').fadeOut('1000');
        }, 5000);

        // Bootstrap Colorpicker
        $('.color-picker').colorpicker({
            format: 'hex'
        });
    };

    $(function () {
        var $body = $('body');
        var $document = $(document);

        // Quick fix for the bootstrap-dropdown which does not work on mobile devices
        // it stops the touch event on a dropdown from bubbling to the next element
        // reference: https://github.com/twitter/bootstrap/issues/5094
        $body.on('touchstart.dropdown', '.dropdown-menu', function (event) {
            event.stopPropagation();
        });

        // Quick fix for mobile devices
        // form element inside a draggable area cannot be edit without this code
        $body.on('click', '.ui-sortable input, .ui-sortable textarea', function () {
            $(this).focus();
        });

        $document.on('changeColor.color-picker.data-api', '.color-picker', function (event) {
            var $element = $(event.currentTarget);
            var target = $element.attr('data-target');
            if (target) {
                $(target).css('backgroundColor', event.color.toHex());
            }
        });

        // pjax
        // TODO for now the link selector refers just to the left menu-sidebar/set-up section
        $document.pjax('a[data-tid="pjax"]', {
            container: '#container',
            fragment: '#container'
        });

        // Bootstrap Tooltip
        $body.tooltip({
            selector: '[rel=tooltip]'
        });

        // Bootstrap Popover
        $body.popover({
            selector: '[rel=popover]',
            trigger: 'hover',
            html: true
        });

        initialize();
    }).on('pjax:end', initialize);
})(jQuery, Modernizr, document, window);
