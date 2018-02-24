<?php declare(strict_types = 1);

namespace App;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

class RouterFactory
{

	public function create(): \Nette\Application\Routers\RouteList
	{
		$router = new RouteList();
		$router[] = new Route('index.php', 'Front:Homepage:default', Route::ONE_WAY);
		$router[] = new Route('api/3/<presenter>[/<id>]', [
			'module' => 'Api3',
			'presenter' => 'Default',
			'action' => 'default',
			'cid' => null]);
		$router[] = new Route('admin/<presenter>/<action>[/<id>]', [
			'module' => 'Admin',
			'presenter' => 'Dashboard',
			'action' => 'default',
			'id' => null]);

		$router[] = new Route('<action>/[<id>]', [
			'module' => 'Front',
			'presenter' => 'Homepage',
			'action' => 'default']);

		return $router;
	}

}
