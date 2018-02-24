<?php declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

$configurator = new \Nette\Configurator();

if (\PHP_SAPI === 'cli' && in_array('--debug', $_SERVER['argv'], true)) {
	unset($_SERVER['argv'][array_search('--debug', $_SERVER['argv'], true)]);
	$configurator->setDebugMode(true);
}
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->addDirectory(__DIR__ . '/../libs')
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

return $configurator->createContainer();
