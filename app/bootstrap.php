<?php

/**
 * My Application bootstrap file.
 */
use Nette\Application\Routers\Route;


// Load Nette Framework
require LIBS_DIR . '/Nette/loader.php';


// Configure application
$configurator = new Nette\Config\Configurator;

// Enable Nette Debugger for error visualisation & logging
//$configurator->setProductionMode($configurator::AUTO);
$configurator->enableDebugger(__DIR__ . '/../log');

// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
	->addDirectory(APP_DIR)
	->addDirectory(LIBS_DIR)
	->register();
if(PHP_SAPI == 'cli') {
    $configurator->productionMode = FALSE;
}
// Create Dependency Injection container from config.neon file
$configurator->addConfig(__DIR__ . '/config/config.neon');
$container = $configurator->createContainer();
if(PHP_SAPI == 'cli') {
    $container->application->allowedMethods = FALSE;
    Nette\Diagnostics\Debugger::$productionMode = false;
    $container->router[] = new FixedCliRouter(array(
        'module' => 'Cli',
        'presenter' => 'Import',
        'action' => 'default',
    ));
}
// Setup router
$container->router[] = new Route('index.php', 'Front:Homepage:default', Route::ONE_WAY);
$container->router[] = new Route('api/2/<presenter>[/<cid>]', array(
   'module'=>'Api',
    'presenter' =>'Default', 
    'action'=> 'default',
    'cid' => null));
$container->router[] = new Route('cli/<presenter>/<action>/[<id>]', array(
    'module'=>'Cli',
    'presenter'=>'Import',
    'action'=>'default'));

$container->router[] = new Route('<action>/[<id>]', array(
    'module'=>'Front',
    'presenter'=>'Homepage',
    'action'=>'default'));


// Configure and run the application!
$container->application->run();
