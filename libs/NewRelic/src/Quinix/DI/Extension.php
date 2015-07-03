<?php

namespace Quinix\NewRelic\DI;

use Kdyby;
use Nette;
use Nette\PhpGenerator\Helpers;

class Extension extends Nette\DI\CompilerExtension {


    public function loadConfiguration() {
        if (!extension_loaded('newrelic') || !ini_get('newrelic.enabled')) {
      //      return;
        }
        $builder = $this->getContainerBuilder();

        $config = $this->getParsedConfig();

        Nette\Utils\Validators::assertField($config, 'enabled');


        if ($builder->expand($config['enabled'])) {


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

    private function getAppNameString($config) {
        $licence = isset($config['licence']) ? $config['licence'] : null;
        $appName = $config['appName'];
        if ($licence === NULL) {
			return "newrelic_set_appname('$appName');";
		} else {
			return "newrelic_set_appname('$appName', '$licence');";
		}
    }

    public function afterCompile(Nette\PhpGenerator\ClassType $class)
    {
        $init = $class->methods['initialize'];

        $init->addBody(Helpers::format(
            'if (extension_loaded(\'newrelic\')) {'.$this->getAppNameString($this->getParsedConfig()) . '}'
        ));
    }

    private function getParsedConfig()
    {
        $builder = $this->getContainerBuilder();
        $config = parent::getConfig(array(
            'enabled' => !$builder->expand('%debugMode%')
        ));

        return $config;
    }

}
