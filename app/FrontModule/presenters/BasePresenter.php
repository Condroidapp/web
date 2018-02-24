<?php declare(strict_types = 1);

namespace FrontModule;

use Nette;

/**
 * @property Nette\Bridges\ApplicationLatte\Template $template
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

	public const FLASH_ERROR = 'error';
	public const FLASH_INFO = 'info';

	protected function beforeRender(): void
	{
		$this->template->useFullAssets = $this->context->parameters['useFullAssets'];
		parent::beforeRender();
	}

}
