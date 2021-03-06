<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 18.5.14
 * Time: 20:40
 */

namespace App;

class Datagrid extends \Nextras\Datagrid\Datagrid
{

	protected function attached($presenter): void
	{
		parent::attached($presenter);

		$this->addCellsTemplate(__DIR__ . '/../../vendor/nextras/datagrid/bootstrap-style/@bootstrap3.datagrid.latte');
		$this->addCellsTemplate(__DIR__ . '/../../vendor/nextras/datagrid/bootstrap-style/@bootstrap3.extended-pagination.datagrid.latte');
	}

}
