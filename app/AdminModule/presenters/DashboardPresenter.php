<?php declare(strict_types = 1);

namespace AdminModule;

use App\Components\Grids\EventGridFactory;
use App\Components\grids\LogGridFactory;
use Nextras\Datagrid\Datagrid;

/**
 * Class DashboardPresenter
 * @User(loggedIn)
 */
class DashboardPresenter extends SecuredPresenter
{

	public function createComponentEventGrid(EventGridFactory $factory): Datagrid
	{
		return $factory->create();
	}

	public function createComponentLogGrid(LogGridFactory $factory): Datagrid
	{
		return $factory->create();
	}

}
