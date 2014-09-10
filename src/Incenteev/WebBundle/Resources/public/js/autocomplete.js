/*global jQuery, window*/
(function ($, _) {
    'use strict';

    var extractor = function (query) {
        var result = /([^\s,;]+)$/.exec($.trim(query));

        if (result && result[1]) {
            return $.trim(result[1]);
        }
        return '';
    };

    var processData = function (data, query, process) {
        var selectedValues = query.split(/[\s,;]+/);

        // Remove the last element, which is the one we are writing
        selectedValues.pop();

        process(_.difference(data, selectedValues));
    };

    $(function () {
        var sourceCache = {};

        $('[data-provide="autocomplete-multiple"]').typeahead({
            source: function (query, process) {
                var sourceUrl = this.$element.attr('data-source-url');

                if (sourceUrl in sourceCache) {
                    processData(sourceCache[sourceUrl], query, process);

                    return;
                }

                $.ajax({
                    url: sourceUrl,
                    dataType: 'json'
                }).done(function (data) {
                    var users = _.map(data.users, function (user) {
                        return user.emailCanonical;
                    });

                    sourceCache[sourceUrl] = users;
                    processData(users, query, process);
                });
            },
            updater: function (item) {
                return $.trim(this.$element.val()).replace(/[^\s,;]*$/, '') + item + ', ';
            },
            matcher: function (item) {
                var tquery = extractor(this.query);

                if (!tquery) {
                    return false;
                }

                return ~item.indexOf(tquery);
            }
        });
    });
})(jQuery, window._);
