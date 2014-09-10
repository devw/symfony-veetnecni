/*global jQuery, window*/
(function ($, window, marked) {
    'use strict';

    // Prepend the single line-breaks with 2 spaces as marked does not support the GFM line-breaks yet
    // See https://github.com/chjj/marked/issues/51
    var parser = function (source) {
        var src = source
            .replace(/\r\n|\r/g, '\n')
            .replace(/\t/g, '    ')
            .replace(/[\w\\<][^\n]*\n+/g, function (m) {
                return (/\n{2}/).test(m) ? m : m.replace(/\s+$/, '') + '  \n';
            });

        return marked.call(this, src);
    };

    // Fake Showdown for MDMagick but based on marked which supports the GFM syntax
    var converter = function() {};
    converter.prototype.makeHtml = parser;

    window.Attacklab = {
        showdown: {
            converter: converter
        }
    };

    var initialize = function () {
        $('.markdown-input').mdmagick();
    };

    $(initialize).on('pjax:end', initialize);
})(jQuery, window, window.marked);
