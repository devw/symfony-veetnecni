## Installation

Install the dependencies with [composer](http://getcomposer.org/) (assuming
you have composer available globally and named `composer`):

    composer install

Create your database (through PhpMyAdmin).
Then create the tables in the database:

    php app/console doctrine:migrations:migrate

For the prod environment, you also need to dump the assetic files and warmup
the cache:

    php app/console --env=prod --no-debug assetic:dump
    php app/console --env=prod --no-debug cache:clear --no-warmup
    php app/console --env=prod --no-debug cache:warmup

## Loading fixtures

The projects provides some fixtures to allow having data for the development.
It will provide 2 known users: ``admin@example.org``/``test`` and
``user@example.org``/``logsafe``.
Loading the fixtures is done through the console:

    php app/console doctrine:fixtures:load

## Running the testsuite

### PHP

The tests are written using [PHPUnit](http://phpunit.de) so you need to install
PHPUnit 3.6. Then, you can run the tests by running `phpunit` at the root
of the project.

## Vendor asset versions

This section allows to track the version of the vendor assets we use, to
know if we need to update them. It should be a git reference (tag name or
commit hash) whenever the project is hosted with git.

 - [bootstrap](http://twitter.github.com/bootstrap/): 2.2.1
 - [bootstrap-datepicker](https://github.com/eternicode/bootstrap-datepicker): ad7c00eb0dc2baa4672165303c3ee738b287d79b
 - [bootstrap-colorpicker](https://github.com/xaguilars/bootstrap-colorpicker): b9e7a64c371c25565128148ce71be562d8d62aaf
 - [jquery countdown](http://keith-wood.name/countdown.html): 1.6.0
 - [moment.js](http://momentjs.com/): 1.7.2
 - [timeago](http://timeago.yarp.com/): v0.11.4
 - [chosen](https://github.com/harvesthq/chosen): v0.9.11
 - [humane-js](https://github.com/wavded/humane-js): 3.0.5
 - [jQuery](http://jquery.com/): 1.8.3
 - [jQuery UI](http://jqueryui.com/): 1.9.2 (including `sortable`)
 - [jQuery autosize](https://github.com/jackmoore/autosize): 593b3de0867771f7e8ae6877b3e970653bd410a0
 - [jQuery Expander](https://github.com/kswedberg/jquery-expander): 1.4.5
 - [jQuery handsontable](https://github.com/warpech/jquery-handsontable): 0.7.5
 - [jQuery UI Touch Punch](https://github.com/furf/jquery-ui-touch-punch): 72d67b63c98a6d9c324881343fcd423b88939ccf
 - [jQuery pjax](https://github.com/defunkt/jquery-pjax): 4bb8554240e13ee10888ccb23faa3c7dc36d7ff6
 - jQuery a-tools: 1.5.1 (taken from the MDMagick repo as the jQuery plugin website lost the downloads)
 - [less.js](http://lesscss.org/): 1.3.1
 - [Marked](https://github.com/chjj/marked): 0.2.6
 - [MDMagick](https://github.com/fguillen/MDMagick): bf755e6e8c83d38a651ab1fb6a51a26415d0c843
 - [Modernizr](http://modernizr.com/): 2.6.2 ([custom build](http://modernizr.com/download/#-rgba-input-inputtypes-shiv-mq-cssclasses-teststyles-load))
 - [Placeholder polyfill](https://github.com/jamesallardice/Placeholders.js): 1.3
 - [underscore.js](http://underscorejs.org/): 1.4.2
