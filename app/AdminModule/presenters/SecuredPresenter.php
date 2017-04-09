<?php

namespace AdminModule;

use FrontModule\BasePresenter;
use Nette\Application;

class SecuredPresenter extends BasePresenter
{

	public function checkRequirements($element)
	{
		try {
			parent::checkRequirements($element);
		} catch (Application\ForbiddenRequestException $e) {
			$this->storeRequest();
			$this->redirect('Sign:in');
		}
	}

}
