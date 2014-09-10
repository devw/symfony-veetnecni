/*global jQuery, document */
(function ($, document) {
    'use strict';

    var selectors = {
        container: '#container',
        appearanceForm: '.form-appearance',
        themeList: '.builtin-background',
        themeElements: '.builtin-background label',
        backgroundOverlay: '.form-appearance-overlay label',
        backgroundPosition: '.form-appearance-position label',
        backgroundRepeat: '.form-appearance-repeat',
        backgroundRemoval: '.form-remove-background'
    };

    var themes = [
        {
            background_image: 'background/Snowflakes.jpg',
            title: 'contest.background.snowflakes'
        },
        {
            background_image: 'background/Stars.jpg',
            title: 'contest.background.stars'
        },
        {
            background_image: 'background/Purple-stars.jpg',
            title: 'contest.background.purple_stars'
        },
        {
            background_image: 'background/Circus.jpg',
            background_position: 'center',
            title: 'contest.background.circus',
            overlay: 'dark'
        },
        {
            background_image: 'background/Grass.jpg',
            title: 'contest.background.grass',
            overlay: 'dark'
        },
        {
            background_image: 'background/Color-lines.jpg',
            background_position: 'center',
            title: 'contest.background.color_lines',
            overlay: 'dark'
        },
        {
            background_image: 'background/Christmas.png',
            title: 'contest.background.christmas',
            overlay: 'dark'
        },
        {
            background_image: 'background/Universe.jpg',
            title: 'contest.background.universe'
        },
        {
            background_image: 'background/Tropical-beach.jpg',
            background_position: 'right',
            title: 'contest.background.tropical_beach',
            overlay: 'dark'
        },
        {
            background_image: 'background/Camels-in-desert.jpg',
            title: 'contest.background.camels_in_desert',
            overlay: 'dark'
        },
        {
            background_image: 'background/Ski.jpg',
            title: 'contest.background.ski',
            overlay: 'dark'
        },
        {
            background_image: 'background/Pontoon-blue-sea.jpg',
            title: 'contest.background.pontoon_blue_sea',
            overlay: 'dark'
        },
        {
            background_image: 'background/Golf.jpg',
            background_position: 'right',
            title: 'contest.background.golf',
            overlay: 'dark'
        },
        {
            background_image: 'background/Snowboarder.jpg',
            background_position: 'right',
            title: 'contest.background.snowboarder',
            overlay: 'dark'
        },
        {
            background_image: 'background/Cooking.jpg',
            background_position: 'center',
            title: 'contest.background.cooking'
        },
        {
            background_image: 'background/Spices.jpg',
            title: 'contest.background.spices',
            overlay: 'dark'
        },
        {
            background_image: 'background/Partnership.jpg',
            background_position: 'center',
            title: 'contest.background.partnership',
            overlay: 'dark'
        },
        {
            background_image: 'background/Race.jpg',
            background_position: 'center',
            title: 'contest.background.race',
            overlay: 'dark'
        },
        {
            background_image: 'background/Speedometer.jpg',
            background_color: '#000000',
            title: 'contest.background.speedometer'
        },
        {
            background_image: 'background/Stopwatch.jpg',
            title: 'contest.background.stopwatch',
            overlay: 'dark'
        }
    ];

    var applyOverlay = function (event) {
        switchOverlay($(event.currentTarget).find(':radio').val());
    };

    var switchOverlay = function (overlay) {
        $(selectors.container).removeClass('overlay-dark', 'overlay-light').addClass('overlay-' + overlay);
    };

    var applyPosition = function (event) {
        switchPosition($(event.currentTarget).find(':radio').val());
    };

    var switchPosition = function(position) {
        var $body = $('body');

        switch (position) {
            case 'center':
                $body.addClass('background-center').removeClass('background-left background-right');
                break;
            case 'right':
                $body.addClass('background-right').removeClass('background-left background-center');
                break;
            case 'left':
                // Fallthrough
            default:
                $body.addClass('background-left').removeClass('background-center background-right');
                break;
        }
    };

    var applyRemoveBackground = function (event) {
        var isChecked = $(event.currentTarget).is(':checked');
        var $body = $('body');

        if (isChecked) {
            $body.css('background-image', 'none');
            $(selectors.themeElements + '.selected').removeClass('selected');
        } else {
            $body.css('background-image', 'url(' + $(selectors.appearanceForm).find('.app-background').attr('src') + ')');
        }
    };

    var applyRepeat = function (event) {
        var isChecked = $(event.currentTarget).is(':checked');
        var backgroundRepeatClass = 'background-repeat';
        var $body = $('body');

        if (isChecked) {
            $body.addClass(backgroundRepeatClass);
        } else {
            $body.removeClass(backgroundRepeatClass);
        }
    };

    var applyTheme = function (event) {
        var element = $(event.currentTarget);
        var selectedTheme = themes[$(selectors.themeElements).filter(element).index()];
        var backgroundColor = selectedTheme.background_color || '#ffffff';
        var backgroundColorInput = $('.form-appearance-color');
        var backgroundPosition = selectedTheme.background_position || 'left';
        var backgroundPositionInput = $('.form-appearance-position').find('input[value=' + backgroundPosition + ']:radio');
        var overlay = selectedTheme.overlay || 'light';
        var overlayInput = $('.form-appearance-overlay').find('input[value=' + overlay + ']:radio');
        var picturePath = element.find('img').attr('src');

        // Showing to the user the selected theme...
        $(selectors.themeElements + '.selected').removeClass('selected');
        element.addClass('selected');

        // Adding preview...
        $('body').css({
            'background-image': 'url(' + picturePath + ')',
            'background-color': backgroundColor
        });
        switchPosition(backgroundPosition);
        switchOverlay(overlay);

        // Modifying the thumbnail...
        $(selectors.appearanceForm).find('.app-background').attr('src', picturePath);

        // Filling the form with theme values...
        backgroundColorInput.val(backgroundColor);
        backgroundPositionInput.attr('checked', 'checked');
        overlayInput.attr('checked', 'checked');
    };

    var initialize = function () {
        // Return early if we are not in settings/appearance page
        if (0 === $(selectors.appearanceForm).length) {
            return;
        }
        $(selectors.themeElements).find('input:radio').hide();
    };

    $(function () {
        var $document = $(document);

        initialize();
        $document.on('change', selectors.themeElements, applyTheme);
        $document.on('change', selectors.backgroundOverlay, applyOverlay);
        $document.on('change', selectors.backgroundPosition, applyPosition);
        $document.on('change', selectors.backgroundRemoval, applyRemoveBackground);
        $document.on('change', selectors.backgroundRepeat, applyRepeat);
    }).on('pjax:end', initialize);
})(jQuery, document);
