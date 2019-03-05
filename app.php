<?php

require 'vendor/autoload.php';
require 'autoload.php';

$app = app\App::init(false);

$app->runConsole($argv);
