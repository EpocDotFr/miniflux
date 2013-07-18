<?php

namespace Plugin\Example;

use \Plugin;
use \PicoFarad\Router;
use \PicoFarad\Response;

Plugin::setup('example', function() {

});

Plugin::addMenu('test', 'test', 45);

Router\get_action('test', function() {

    Response\html('boo');
});