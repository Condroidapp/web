<?php

namespace FrontModule;

use Nette;

/**
 * @property Nette\Bridges\ApplicationLatte\Template $template
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	const FLASH_ERROR = 'error';
	const FLASH_INFO = 'info';

	protected function beforeRender()
	{
		$this->template->useFullAssets = $this->context->parameters['useFullAssets'];
		parent::beforeRender();
	}

}
