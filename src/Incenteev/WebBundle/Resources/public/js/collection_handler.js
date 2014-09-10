/*global jQuery, window*/
(function ($, _) {
    'use strict';

    /**
     * @param element
     * @constructor
     */
    var CollectionWidget = function (element) {
        var $element = $(element);
        this.$element = $element;

        $element.on('click.collection', '> .control-group button.collection-item-remove', this.removeItem.bind(this));
        $element.on('click.collection', '> .collection-item-add', this.addItem.bind(this));
        _.forEach($element.find('> .control-group'), this.createRemoveButton.bind(this));
        if (0 === this.$element.find('.collection-item-add').length) {
            this.createAddItemButton();
        }
        this.$addItemButton = this.$element.find('.collection-item-add');
    };

    CollectionWidget.prototype = {
        constructor: CollectionWidget,

        createAddItemButton: function () {
            var $addItemButton = $('<button type="button" class="btn collection-item-add"><i class="icon-plus-sign"></i></button>');

            this.$element.append($addItemButton);
            _.defer(this.triggerButtonAdded.bind(this));
        },

        triggerButtonAdded: function () {
            this.$addItemButton.trigger('incenteev_collection_button_added');
        },

        removeItem: function (event) {
            event.preventDefault();
            var $element = $(event.currentTarget).closest('.control-group');
            $element.trigger('incenteev_collection_item_remove');
            $element.remove();
            // Triggered on the container as the element is not in the DOM anymore
            this.$element.trigger($.Event('incenteev_collection_item_removed', {
                relatedTarget: $element[0]
            }));
        },

        createRemoveButton: function (element) {
            var $element = $(element);
            var $button;
            var $wrapper;

            if (0 < $element.find('.collection-item-remove').length) {
                return;
            }
            $button = $('<button type="button" class="btn btn-mini collection-item-remove"><i class="icon-remove"></i></button>');
            $wrapper = $('<div class="collection-item-actions"></div>');
            $wrapper.append($button);
            $element.prepend($wrapper);
            _.defer(function () {
                $element.trigger('incenteev_collection_item_remove_button_added');
            });
        },

        addItem: function (event) {
            event.preventDefault();
            var $container = this.$element;
            var count = $container.children('.control-group').length;
            var proto = $container.attr('data-prototype').replace(/__name__label__/g, count).replace(/__name__/g, count);
            var $proto = $(proto);

            $container.trigger('incenteev_collection_item_add', $proto);
            this.$addItemButton.before($proto);
            this.createRemoveButton($proto);
            $proto.trigger('incenteev_collection_item_added');
        }
    };

    var initialize = function () {
        $('.collection').each(function () {
            var $this = $(this);

            if (!$this.data('collection')) {
                $this.data('collection', new CollectionWidget(this));
            }
        });
    };

    $(initialize).on('pjax:end', initialize);
})(jQuery, window._);
