<?php declare(strict_types = 1);

namespace AdminModule;

use App\Components\Forms\BaseForm;
use App\Components\Forms\IBaseFormFactory;
use FrontModule\BasePresenter;
use Nette\Security\AuthenticationException;

class SignPresenter extends BasePresenter
{

	public function actionIn($key): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			return;
		}

		$this->redirect(':Admin:Dashboard:');
	}

	protected function createComponentSignInForm(IBaseFormFactory $factory)
	{
		$form = $factory->create();
		$form->addText('username', 'Login:')
			->setRequired();

		$form->addPassword('password', 'Heslo:')
			->setRequired();

		$form->addCheckbox('remember', 'Zapamatovat');

		$form->addSubmit('send', 'Přihlásit');

		// call method signInFormSucceeded() on success
		$form->onSuccess[] = [$this, 'signInFormSucceeded'];

		return $form;
	}

	public function signInFormSucceeded(BaseForm $form): void
	{
		$values = $form->getValues();

		if ($values->remember) {
			$this->getUser()->setExpiration('+ 14 days', false);
		} else {
			$this->getUser()->setExpiration('+ 20 minutes', true);
		}

		try {
			$this->getUser()->login($values->username, $values->password);
		} catch (AuthenticationException $e) {
			$this->flashMessage($e->getMessage(), self::FLASH_ERROR);

			return;
		}
		if ($this->getParameter('key')) {
			$this->restoreRequest($this->getParameter('key'));
		}
		$this->redirect(':Admin:Dashboard:');
	}

	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->flashMessage('You have been signed out.');
		$this->redirect('in');
	}

}
