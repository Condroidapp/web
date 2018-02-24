<?php declare(strict_types = 1);

namespace AdminModule;

use FrontModule\BasePresenter;
use Nette\Application;

class SecuredPresenter extends BasePresenter
{

	public function checkRequirements($element): void
	{
		try {
			parent::checkRequirements($element);
		} catch (Application\ForbiddenRequestException $e) {
			$this->storeRequest();
			$this->redirect('Sign:in');
		}
	}

}
