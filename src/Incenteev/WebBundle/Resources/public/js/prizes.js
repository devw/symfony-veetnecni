/*global jQuery, window*/
(function ($, translator) {
    'use strict';

    /**
     * @param element
     * @constructor
     */
    var PrizeWidget = function (element) {
        this.$element = $(element);

        this.init();
    };

    PrizeWidget.prototype = {
        constructor: PrizeWidget,

        init: function () {
            this.$element.sortable({
                axis: 'y',
                placeholder: 'control-group highlight',
                stop: this.rankUpdater.bind(this)
            }).on('incenteev_collection_item_add', this.updateRankValue.bind(this))
                .on('incenteev_collection_item_removed', this.rankUpdater.bind(this))
                .on('incenteev_collection_button_added', this.setAddButtonText)
                .on('incenteev_collection_item_remove_button_added', this.createCollapseButton);

            this.rankUpdater();
        },

        createCollapseButton: function (event) {
            var $element = $(event.target);
            var collapseButton;

            if (0 < $element.find('.collection-item-actions [data-toggle]').length) {
                return;
            }
            collapseButton = $('<button type="button" data-toggle="collapse">')
                .addClass('btn btn-mini')
                .attr('data-target', $element.find('[data-target]').attr('data-target'))
                .append('<i class="icon-chevron-down"></i>');

            $element.find('.collection-item-actions').append(collapseButton);
        },

        updateRankValue: function (event, proto) {
            var $proto = $(proto);
            var lastElement = this.$element.find('.prize-item').last();
            var rankValue = lastElement.find('.form-prize-item-rank').val();

            rankValue = parseInt(rankValue, 10) + 1;
            rankValue = isNaN(rankValue) ? '' : rankValue;
            $proto.find('.form-prize-item-rank').val(rankValue);
            $proto.find('.form-prize-item-rank-value').text(rankValue);
        },

        rankUpdater: function () {
            this.$element.find('.form-prize-item-rank').each(function (i) {
                $(this).val(i + 1);
            });
            this.$element.find('.form-prize-item-rank-value').each(function (i) {
                $(this).text(i + 1);
            });
        },

        setAddButtonText: function (event) {
            $(event.target).text(translator.get('messages:prize.action.add_prize_row'));
        }
    };

    var initialize = function () {
        $('[data-tid="prize-widget"]').each(function () {
            var $this = $(this);

            if (!$this.data('prize_widget')) {
                $this.data('prize_widget', new PrizeWidget(this));
            }
        });
    };

    $(initialize).on('pjax:end', initialize);
})(jQuery, window.Translator);
