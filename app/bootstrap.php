<?php

/**
 * My Application bootstrap file.
 */
use Nette\Application\Routers\Route;
use Nette\Configurator;


// Load Nette Framework
require APP_DIR . '/../vendor/autoload.php';


// Configure application
$configurator = new Configurator;

// Enable Nette Debugger for error visualisation & logging
//$configurator->setProductionMode($configurator::AUTO);
$configurator->enableDebugger(__DIR__ . '/../log');

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
	->addDirectory(APP_DIR)
	->addDirectory(LIBS_DIR)
	->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ .'/config/config.local.neon', Configurator::NONE);


$container = $configurator->createContainer();

// Setup router
$container->router[] = new Route('index.php', 'Front:Homepage:default', Route::ONE_WAY);
$container->router[] = new Route('api/2/<presenter>[/<cid>]', array(
   'module'=>'Api',
    'presenter' =>'Default', 
    'action'=> 'default',
    'cid' => null));

$container->router[] = new Route('api/3/<presenter>[/<cid>]', array(
   'module'=>'Api3',
    'presenter' =>'Default',
    'action'=> 'default',
    'cid' => null));

$container->router[] = new Route('<action>/[<id>]', array(
    'module'=>'Front',
    'presenter'=>'Homepage',
    'action'=>'default'));


// Configure and run the application!
$container->application->run();
