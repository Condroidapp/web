<?php
namespace FrontModule;
use Nette;
/**
 * Base class for all application presenters.
 *
 * @author     John Doe
 * @package    MyApplication
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    
    protected function startup() {
        parent::startup();
       
    }
    

}
