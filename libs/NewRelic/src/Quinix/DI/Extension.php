<?php

namespace Quinix\NewRelic\DI;

use Kdyby;
use Nette;


class Extension extends Nette\DI\CompilerExtension {


    public function loadConfiguration() {
        if (!extension_loaded('newrelic') || !ini_get('newrelic.enabled')) {
            return;
        }
        $builder = $this->getContainerBuilder();

        $config = $this->getConfig(array(
            'enabled' => !$builder->expand('%debugMode%')
        ));

        Nette\Utils\Validators::assertField($config, 'enabled');


        if ($builder->expand($config['enabled'])) {

            if(isset($config['appName'])) {
                $this->setupAppName($config['appName'], isset($config['licence'])?$config['licence']:NULL);
            }

            $builder->addDefinition($this->prefix('listener'))
                    ->setClass('Quinix\\NewRelic\\ProfilingListener')
                    ->addTag(Kdyby\Events\DI\EventsExtension::SUBSCRIBER_TAG);
        }
    }


    public static function register(Nette\Configurator $configurator) {
        $configurator->onCompile[] = function ($config, Nette\DI\Compiler $compiler) {
            $compiler->addExtension('newRelic', new Extension);
        };
    }

    private function setupAppName($appName, $licence) {
        if ($licence === NULL) {
			newrelic_set_appname($appName);
		} else {
			newrelic_set_appname($appName, $licence);
		}
    }

}