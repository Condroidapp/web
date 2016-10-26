<?php
namespace Quinix\NewRelic;
use Kdyby;
use Nette;
use Nette\Application\Application;
use Nette\Application\Request;
use Tracy\Debugger;

class ProfilingListener extends Nette\Object implements Kdyby\Events\Subscriber {
    public function getSubscribedEvents() {
        return array(
            'Nette\\Application\\Application::onStartup',
            'Nette\\Application\\Application::onRequest',
            'Nette\\Application\\Application::onError'
        );
    }

    public function onStartup(Application $app) {
        if (!extension_loaded('newrelic')) {
            return;
        }
    }

    public function onRequest(Application $app, Request $request) {
        if (!extension_loaded('newrelic')) {
            return;
        }
        if (PHP_SAPI === 'cli') {
            // uložit v čitelném formátu
            newrelic_name_transaction('$ ' . basename($_SERVER['argv'][0]) . ' ' . implode(' ', array_slice($_SERVER['argv'], 1)));
            // označit jako proces na pozadí
            newrelic_background_job(TRUE);
            return;
        }
        // pojmenování požadavku podle presenteru a akce
        $params = $request->getParameters();
        newrelic_name_transaction($request->getPresenterName() . (isset($params['action']) ? ':' . $params['action'] : ''));
    }

    public function onError(Application $app, \Exception $e) {
        if (!extension_loaded('newrelic')) {
            return;
        }
        if ($e instanceof Nette\Application\BadRequestException) {
            return; // skip
        }
        // logovat pouze výjimky, které se dostanou až k uživateli jako chyba 500
        newrelic_notice_error($e->getMessage(), $e);
    }
}
