/*global jQuery, window, document */
(function ($, window, document, _, moment, humane, translator) {
    'use strict';

    var initGrid = function initGrid($element) {
        $.getJSON($element.attr('data-source-url')).done(function(response) {
            $element.handsontable(getGridOptions(response, $element.attr('data-save-url'), $element.attr('data-token')));
        });
    };

    var getGridOptions = function getGridOptions(response, saveUrl, token) {
        return {
            data: buildData(response),
            startRows: response.participations.length,
            startCols: response.periods.length,
            rowHeaders: _.map(response.participations, function (participation) {
                return participation.username;
            }),
            colHeaders: _.map(response.periods, function (period) {
                if (period.start === period.end) {
                    return moment.unix(period.start).format('L');
                }

                return moment.unix(period.start).format('L') + ' - ' + moment.unix(period.end).format('L');
            }),
            columns: _.map(response.periods, function (period) {
                return {data: period.timestamp};
            }),
            onChange: function (changes, source) {
                if ('loadData' === source) {
                    return;
                }

                var data = _.map(changes, function (change) {
                    return {
                        participation: response.participations[change[0]].id,
                        date: change[1],
                        value: change[3]
                    };
                });

                humane.warning(translator.get('messages:contest.info.grid_saving'));

                var xhr = $.ajax({
                    url: saveUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: token,
                        data: data
                    }
                });
                xhr.done(function (saveResponse) {
                    if ('partial' === saveResponse.status) {
                        humane.error(translator.get('messages:contest.flash.data_grid.partially_saved'));
                    }
                });
                xhr.fail(function (jqXhr) {
                    if (400 === jqXhr.status) {
                        humane.error(translator.get('messages:contest.flash.data_grid.invalid_data'));
                        return;
                    }

                    humane.error(translator.get('messages:contest.flash.data_grid.saving_failed'));
                });
            }
        };
    };

    var buildData = function buildData(response) {
        var data = {};

        _.map(response.participations, function(participation) {
            data[participation.id] = getEmptyObject(response.periods);
        });

        _.map(response.entries, function (entry) {
            data[entry.participation][entry.date] = entry.value;
        });

        var finalData = [];

        _.map(response.participations, function(participation) {
            finalData.push(data[participation.id]);
        });

        return finalData;
    };

    var getEmptyObject = function getEmptyObject(periods) {
        var data = {};

        _.map(periods, function (period) {
            data[period.timestamp] = null;
        });

        return data;
    };

    $(function () {
        var $dataGrid = $('.data-grid.data-grid-results');

        // Return early if we don't have a grid
        if (!$dataGrid.length) {
            return;
        }

        moment.lang($(document.documentElement).attr('lang'));

        initGrid($dataGrid);
    });
}(jQuery, window, document, window._, window.moment, window.humane, window.Translator));
