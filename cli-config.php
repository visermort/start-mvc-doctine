<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require 'vendor/autoload.php';
require 'autoload.php';

$app = app\App::init(false);

$entityManager = app\App::getComponent('doctrine')->db;

return ConsoleRunner::createHelperSet($entityManager);
