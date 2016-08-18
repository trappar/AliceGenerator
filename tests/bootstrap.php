<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

if (!is_file($autoloadFile = __DIR__ . '/../vendor/autoload.php')) {
    throw new \RuntimeException('Did not find vendor/autoload.php. Did you run "composer install --dev"?');
}
$loader = require $autoloadFile;
$loader->addPsr4('Trappar\AliceGenerator\Tests\\', __DIR__ . '/Trappar/AliceGenerator');
AnnotationRegistry::registerLoader('class_exists');