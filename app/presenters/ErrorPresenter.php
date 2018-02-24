<?php declare(strict_types = 1);

namespace App;

use FrontModule\BasePresenter;
use Nette\Application\BadRequestException;
use Tracy\Debugger;

class ErrorPresenter extends BasePresenter
{

	/**
	 * @param mixed $exception
	 */
	public function renderDefault($exception): void
	{
		if ($this->isAjax()) { // AJAX request? Just note this error in payload.
			$this->payload->error = true;
			$this->terminate();

		} elseif ($exception instanceof BadRequestException) {
			$code = $exception->getCode();
			// load template 403.latte or 404.latte or ... 4xx.latte
			$this->setView(in_array($code, [403, 404, 405, 410, 500]) ? $code : '4xx');
			// log to access.log
			Debugger::log('HTTP code ' . $code . ': ' . $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine(), 'access');

		} else {
			$this->setView('500'); // load template 500.latte
			Debugger::log($exception, Debugger::ERROR); // and log exception
		}
	}

}
