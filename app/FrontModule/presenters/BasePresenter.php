<?php
namespace FrontModule;

use Kdyby\Autowired\AutowireComponentFactories;
use Kdyby\Autowired\AutowireProperties;
use Nette;

/**
 * Base class for all application presenters.
 *
 * @author     John Doe
 * @package    MyApplication
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter {
    use AutowireProperties;
    use AutowireComponentFactories;


    const FLASH_ERROR = 'error';
    const FLASH_INFO = 'info';
}
