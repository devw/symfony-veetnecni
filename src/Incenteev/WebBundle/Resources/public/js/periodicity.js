/*global jQuery, window, document */
(function ($, _, window, document, moment) {
    'use strict';

    var translator = window.Translator;

    /**
     * @param element
     * @constructor
     */
    var PeriodicityWidget = function (element) {
        var $element = $(element);
        this.$element = $element;
        if (0 === this.$element.find('.periodicity-summary').length) {
            $element.prepend(this.getSummaryTemplate());
        }
        this.outputElement = $element.find('.periodicity-summary output');
        this.weekContainer = $element.find('.periodicity-weeks');
        this.dayContainer = $element.find('.periodicity-days');
        this.hourSelectBox = $element.find('.periodicity-hours');

        $element.on('click.periodicity', '.periodicity-summary button', this.toggleDetails.bind(this));
        $element.on('change.periodicity', this.updateOutput.bind(this));

        this.initPresets();
        this.updateOutput();

        $element.addClass('periodicity-short');
    };

    PeriodicityWidget.prototype = {
        constructor: PeriodicityWidget,

        outputElement: [],
        weekContainer: [],
        dayContainer: [],
        hourSelectBox: [],

        weekPresets: {},
        dayPresets: {},

        updateOutput: function () {
            this.outputElement.text(this.getSummary());
        },

        /**
         * Gets the summary string for the periodicity.
         *
         * @return {String}
         */
        getSummary: function () {
            var selectedDays = _.filter(_.map(this.dayContainer.find('input'), function (input) {
                if (!input.checked) {
                    return null;
                }

                return moment().day(Math.log(input.value) / Math.LN2).format('dddd');
            }), _.isString);

            if (!selectedDays.length) {
                return translator.get('messages:periodicity.summary.never_days');
            }

            var selectedHours = _.map(this.hourSelectBox.find('option:selected'), function (option) {
                return $(option).text();
            });

            if (!selectedHours.length) {
                return translator.get('messages:periodicity.summary.never_hours');
            }

            var selectedWeeks = _.filter(_.map(this.weekContainer.find('input'), function (input) {
                if (!input.checked) {
                    return null;
                }

                var week = Math.log(input.value) / Math.LN2 + 1;

                return translator.get('messages:periodicity.summary.week.w' + week);
            }), _.isString);

            if (!selectedWeeks.length) {
                return translator.get('messages:periodicity.summary.never_weeks');
            }

            var message;
            var pluralization;
            if (5 !== selectedWeeks.length) {
                message = 'messages:periodicity.summary.week_days_and_hours';
                pluralization = selectedWeeks.length * selectedHours.length;
            } else {
                message = 7 === selectedDays.length ?
                    'messages:periodicity.summary.every_days' :
                    'messages:periodicity.summary.day_and_hours';
            }

            return translator.get(message, {
                weeks: this.getSummaryPart(selectedWeeks),
                days: this.getSummaryPart(selectedDays),
                hours: this.getSummaryPart(selectedHours)
            }, pluralization);
        },

        /**
         * Gets a part of the summary representing a list of elements ("a, b and c")
         *
         * @param {Array<string>} elements
         * @return {String}
         */
        getSummaryPart: function (elements) {
            if (1 === elements.length) {
                return elements[0];
            }

            var last = elements.pop();

            return translator.get('messages:periodicity.summary.list_join', {
                first: elements.join(', '),
                last: last
            });
        },

        toggleDetails: function (event) {
            event.preventDefault();
            this.$element.toggleClass('periodicity-short');
        },

        selectWeekPreset: function (event) {
            var $select = $(event.target);
            var presetName = $select.val();
            var preset = this.weekPresets[presetName];

            this.selectPreset(preset, this.weekContainer);
        },

        selectDayPreset: function (event) {
            var $select = $(event.target);
            var presetName = $select.val();
            var preset = this.dayPresets[presetName];

            this.selectPreset(preset, this.dayContainer);
        },

        selectPreset: function (preset, container) {
            if (_.isFinite(preset.match)) {
                var inputs = container.find('input');

                _.forEach(inputs, function (input) {
                    var $input = $(input);
                    $input.prop('checked', (preset.match & ~~$input.val()) > 0);
                });
            }

            if (true === preset.showDetails) {
                container.removeClass('periodicity-preset');
            } else {
                container.addClass('periodicity-preset');
            }
        },

        initPresets: function () {
            this.weekPresets = this.getWeekPresets();
            this.dayPresets = this.getDayPresets();

            if (0 === this.weekContainer.find('select').length) {
                this.createPresetSelectBox(this.weekContainer, this.weekPresets, this.selectWeekPreset.bind(this));
            }
            if (0 === this.dayContainer.find('select').length) {
                this.createPresetSelectBox(this.dayContainer, this.dayPresets, this.selectDayPreset.bind(this));
            }
            this.hourSelectBox.chosen({
                placeholder_text: translator.get('messages:periodicity.placeholder.hours'),
                no_results_text: translator.get('messages:periodicity.info.no_hours_found'),
                search_contains: true
            });
        },

        /**
         * Creates a preset selector
         *
         * @param container
         * @param {Object} presets
         * @param {Function} changeCallback
         */
        createPresetSelectBox: function (container, presets, changeCallback) {
            var selectElement = $('<select>');
            var inputs = container.find('input');
            var valueFound = false;

            var value = _.reduce(inputs, function (memo, input) {
                var $input = $(input);
                if (!$input.is(':checked')) {
                    return memo;
                }

                return memo + ~~$input.val();
            }, 0);

            // inherit the disabled state from the choice element.
            selectElement.prop('disabled', inputs.prop('disabled'));

            _.forEach(presets, function (preset, key) {
                var optionElement = $('<option>').val(key).text(preset.title);
                selectElement.append(optionElement);

                if (valueFound) {
                    return;
                }

                if (preset.match === value || true === preset.match) {
                    selectElement.val(key);
                    valueFound = true;
                    if (true !== preset.showDetails) {
                        container.addClass('periodicity-preset');
                    }
                }
            });

            container.prepend(selectElement);
            selectElement.on('change.periodicity', changeCallback);
        },

        getWeekPresets: function () {
            return {
                every_week: {
                    title: translator.get('messages:periodicity.presets.week.every_week'),
                    match: 31
                },
                first_week: {
                    title: translator.get('messages:periodicity.presets.week.first_week'),
                    match: 1
                },
                last_week: {
                    title: translator.get('messages:periodicity.presets.week.last_week'),
                    match: 16
                },
                custom: {
                    title: translator.get('messages:periodicity.presets.week.custom'),
                    showDetails: true,
                    match: true
                }
            };
        },
        getDayPresets: function () {
            return {
                daily: {
                    title: translator.get('messages:periodicity.presets.day.daily'),
                    match: 127
                },
                weekday: {
                    title: translator.get('messages:periodicity.presets.day.weekday'),
                    match: 62
                },
                every_mon_wed_fri: {
                    title: translator.get('messages:periodicity.presets.day.every_mon_wed_fri'),
                    match: 42
                },
                every_tue_thu: {
                    title: translator.get('messages:periodicity.presets.day.every_tue_thu'),
                    match: 20
                },
                custom: {
                    title: translator.get('messages:periodicity.presets.day.custom'),
                    showDetails: true,
                    match: true
                }
            };
        },

        getSummaryTemplate: function () {
            return '<div class="periodicity-summary"><output></output><button class="btn btn-mini" data-toggle="button">%s</button></div>'.replace('%s', translator.get('messages:periodicity.action.customize'));
        }
    };

    var initialize = function () {
        $('.periodicity').each(function () {
            var $this = $(this);

            if (!$this.data('periodicity')) {
                $this.data('periodicity', new PeriodicityWidget(this));
            }
        });
    };

    $(function() {
        // TODO find a better way to handle the moment language to do it in a single place
        moment.lang($(document.documentElement).attr('lang'));

        initialize();
    }).on('pjax:end', initialize);
}(jQuery, window._, window, document, window.moment));
