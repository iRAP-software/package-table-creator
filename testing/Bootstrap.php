<?php


namespace iRAP\TableCreator;

require_once(__DIR__ . '/Settings.php');
require_once(__DIR__ . '/vendor/autoload.php');


$dirs = array(
    __DIR__ . '/../',
    __DIR__,
    __DIR__ . '/tests'
);

$autoloader = new \iRAP\Autoloader\Autoloader($dirs);