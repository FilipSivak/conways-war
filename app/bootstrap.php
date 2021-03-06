<?php

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

// Load Nette Framework or autoloader generated by Composer
require __DIR__ . '/../libs/autoload.php';

$configurator = new Nette\Config\Configurator;

// Enable Nette Debugger for error visualisation & logging
$configurator->enableDebugger(__DIR__ . '/../log');

// Specify folder for cache
$configurator->setTempDirectory(__DIR__ . '/../temp');

// Enable RobotLoader - this will load all classes automatically
$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

// Create Dependency Injection container from config.neon file
$container = $configurator->createContainer();

$router = new Nette\Application\Routers\RouteList();
$router[] = new Nette\Application\Routers\Route('index.php', 'Homepage:default', Route::ONE_WAY);
$router[] = new Nette\Application\Routers\Route('<presenter>/<action>[/<id>]', 'Homepage:default');

// Router
$container->addService("router", $router);

/* DOCTRINE */
$entityManager = require "doctrine_bootstrap.php";

// put entityManager to container as service
$container->addService("EntityManager", $entityManager);

return $container;
