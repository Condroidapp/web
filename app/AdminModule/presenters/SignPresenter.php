<?php

namespace AdminModule;


use FrontModule\BasePresenter;
use App\Components\Forms\Form;
use Nette\Security\AuthenticationException;

class SignPresenter extends BasePresenter {

    public function actionIn($key){
        if($this->getUser()->isLoggedIn()) {
            $this->redirect(':Admin:Dashboard:');
        }
    }


    /**
     * Sign-in form factory.
     * @return Form
     */
    protected function createComponentSignInForm()
    {
        $form = new Form;
        $form->addText('username', 'Login:')
             ->setRequired();

        $form->addPassword('password', 'Heslo:')
             ->setRequired();

        $form->addCheckbox('remember', 'Zapamatovat');

        $form->addSubmit('send', 'Přihlásit');

        // call method signInFormSucceeded() on success
        $form->onSuccess[] = callback($this, 'signInFormSucceeded');
        return $form;
    }



    public function signInFormSucceeded(Form $form)
    {
        $values = $form->getValues();

        if ($values->remember) {
            $this->getUser()->setExpiration('+ 14 days', FALSE);
        } else {
            $this->getUser()->setExpiration('+ 20 minutes', TRUE);
        }

        try {
            $this->getUser()->login($values->username, $values->password);
        } catch (AuthenticationException $e) {
            $this->flashMessage($e->getMessage(), self::FLASH_ERROR);
            return;
        }
        if($this->getParameter('key')) {
            $this->restoreRequest($this->getParameter('key'));
        }
        $this->redirect(':Admin:Dashboard:');

    }



    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('You have been signed out.');
        $this->redirect('in');
    }

} 