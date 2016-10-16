<?php
namespace AdminModule;

use App\Components\Grids\EventGridFactory;
use App\Components\grids\LogGridFactory;

/**
 * Class DashboardPresenter
 * @User(loggedIn)
 */
class DashboardPresenter extends SecuredPresenter
{

	public function createComponentEventGrid(EventGridFactory $factory)
	{
		return $factory->create();
	}

	public function createComponentLogGrid(LogGridFactory $factory)
	{
		return $factory->create();
	}

}
