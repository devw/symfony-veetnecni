/*global jQuery, window, document*/
(function ($, _, translator, document) {
    'use strict';

    var selector = {
        form: '.form-teams',
        multipleSelect: '.form-teams .controls select',
        sortableList: '.list-team',
        unselectedUsers: '.form-app-unselected-users',
        unselectedUsersList: '.list-unselected-users',
        addTeam: '.form-teams-add',
        appActions: '.form-app-actions'
    };

    // TODO the scope of this widget is wrong as it is currently modifying some elements outside the element
    /**
     * @param element
     * @constructor
     */
    var TeamWidget = function (element) {
        this.$element = $(element);
        this.hasTeam = $(selector.multipleSelect).length > 0;

        this.init();
    };

    TeamWidget.prototype = {
        constructor: TeamWidget,

        init: function () {
            this.$element.attr('data-add-button-text', translator.get('messages:prize.action.contest_team.action.add_team_row'));
            this.patchPHP();
            if (this.hasTeam) {
                this.addSortableLists();
                this.connectList();
            }
            $(document).on('sortupdate', selector.form + ' '  + selector.sortableList, this.onSortupdate.bind(this))
                .on('click', '[data-tid="remove-user"]', this.removeUser.bind(this));

            this.$element.on('incenteev_collection_item_remove', this.removeTeam)
                .on('incenteev_collection_item_added', this.addTeam.bind(this))
                .on('incenteev_collection_button_added', this.setAddButtonText);
        },

        // TODO probably this should be in php code, waiting for another PR
        patchPHP: function () {
            _.forEach(this.$element.children('.control-group'), function (controlGroup) {
                var $controlGroup = $(controlGroup);

                $controlGroup.find('> .controls').addClass('control-group-team');
            });
        },

        addSortableLists: function () {
            var self = this;
            $('.btn' + selector.addTeam).removeClass('no-team');
            this.appendUnselectedUsersElement();
            this.appendConnectedSortableList($(selector.multipleSelect + ':first'), true);
            _.forEach($(selector.multipleSelect), function (multipleSelect) {
                self.appendConnectedSortableList($(multipleSelect), false);
            });
        },

        removeUser: function (event) {
            var $currentTarget = $(event.currentTarget);
            var $listElement = $currentTarget.closest('li');
            var $selectElement = $currentTarget.closest('.controls').find('select');
            var userId = $listElement.attr('data-user-id');

            this.toggleHelp($currentTarget.closest(selector.sortableList));
            $selectElement.find('option[value=' + userId + ']').attr('selected', false);
            $(selector.unselectedUsersList).append($listElement);
        },

        removeTeam: function (event) {
            var $controlGroupElement = $(event.target);
            var $selectElement = $controlGroupElement.find('select');
            var $userSelectedElements = $selectElement.find('option:selected');

            if ($userSelectedElements.length) {
                var $listElements = $controlGroupElement.find('li');
                $(selector.unselectedUsersList).append($listElements);
                _.forEach($userSelectedElements, function (userElements) {
                    var userId = $(userElements).val();
                    $selectElement.find('option[value=' + userId + ']').attr('selected', false);
                });
            }
        },

        addTeam: function () {
            var $multipleSelect;

            if (!this.hasTeam) {
                this.hasTeam = true;
                this.addSortableLists();
            } else {
                $multipleSelect = $(selector.multipleSelect + ':last');
                this.appendConnectedSortableList($multipleSelect, false);
            }
            this.patchPHP();
            this.connectList();
        },

        setAddButtonText: function (event) {
            var $button = $(event.target);

            if (!this.hasTeam) {
                $button.addClass('no-team');
            }
            $button.text(translator.get('messages:contest_team.action.add_team_row'));
        },

        toggleHelp: function ($listElement) {
            _.defer(function () {
                if ($listElement.find('li').length > 1) {
                    $listElement.find('li.ui-state-disabled').hide();
                } else {
                    $listElement.find('li.ui-state-disabled').show();
                }
            });
        },

        onSortupdate: function (event, ui) {
            var userId = $(ui.item).attr('data-user-id');
            var $selectElement = $(event.target).closest('ul').siblings('select');
            var values = $selectElement.val() || [];

            if (ui.sender !== null) {
                values.push(userId);
            } else {
                values = _.without(values, userId);
            }
            $selectElement.val(values.length ? values : null);

            this.toggleHelp($(event.currentTarget));
        },

        appendUnselectedUsersElement: function () {
            if (0 < this.$element.siblings(selector.unselectedUsers).length) {
                return;
            }
            var $unselectedUsersElement = $('<div>').addClass(selector.unselectedUsers.slice(1))
                .append($('<h4>').text(translator.get('messages:organization_team.info.users_without_team')));

            $(selector.appActions).before($unselectedUsersElement);
        },

        // it appends a Connected Sortable List
        appendConnectedSortableList: function ($selectElement, isUnselected) {
            var $users = $selectElement.find('option' + (isUnselected ? ':not(:selected)' : ':selected'));
            var appendListElement = function ($users) {
                var getListElement;
                var $list;
                var $controls = isUnselected ? $(selector.unselectedUsers) : $selectElement.closest('.controls');

                if (0 < $controls.find(selector.sortableList).length) {
                    return;
                }
                getListElement = function () {
                    return $('<ul>')
                        .addClass('list ' + selector.sortableList.slice(1) + ' ' + (isUnselected ? ' ' + selector.unselectedUsersList.slice(1) : ''))
                        .append($('<li>').addClass('ui-state-disabled hide form-help-block').text(translator.get('messages:contest_team.help.empty_team')));
                };
                $list = getListElement();
                if (!$users.length) {
                    $list.find('.ui-state-disabled').show();
                }
                _.forEach($users, function (user) {
                    var $user = $(user);

                    $list.append($('<li>')
                        .attr('data-user-id', $user.val())
                        .text($user.text())
                        .append('<i class="icon-remove" data-tid="remove-user"></i>'));

                    if (!isUnselected) {
                        // remove the user from the unselected-users list
                        $(selector.unselectedUsersList).find('li[data-user-id=' + $user.val() + ']')
                            .remove();
                    }
                });
                $controls.append($list);
            };

            $selectElement.hide();
            appendListElement($users);
        },

        connectList: function () {
            var selectValues;

            $(selector.form + ' ' + selector.sortableList).sortable({
                connectWith: selector.sortableList,
                cancel: '.ui-state-disabled',
                placeholder: 'ui-state-highlight',
                start: function (event, ui) {
                    if (ui.item[0].parentNode === this) {
                        var $selectElement = $(this).closest('ul').siblings('select');
                        selectValues = $selectElement.val();
                    }
                },
                stop: function (event, ui) {
                    if (ui.item[0].parentNode === this) {
                        var $selectElement = $(this).closest('ul').siblings('select');
                        $selectElement.val(selectValues);
                    }
                }
            });
        }
    };

    var initialize = function () {
        $('[data-tid="team-widget"]').each(function () {
            var $this = $(this);

            if (!$this.data('team_widget')) {
                $this.data('team_widget', new TeamWidget(this));
            }
        });
    };

    $(initialize).on('pjax:end', initialize);
})(jQuery, window._, window.Translator, document);
