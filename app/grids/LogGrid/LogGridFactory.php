<?php

namespace App\Components\grids;

use App\Datagrid;
use Doctrine\ORM\EntityManager;
use Model\BasicFetchByQuery;
use Model\LogRecord;
use Nette\Utils\Paginator;

class LogGridFactory
{

	/** @var EntityManager */
	protected $em;

	function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function create()
	{
		$grid = new Datagrid();

		$grid->addColumn('time');
		$grid->addColumn('tag');
		$grid->addColumn('severity');
		$grid->addColumn('message');

		$grid->addCellsTemplate(__DIR__ . '/logGrid.latte');

		$grid->setPagination(30, function ($filter, $sort) {
			return $this->em->getRepository(LogRecord::class)->fetch(new BasicFetchByQuery([]))->getTotalCount();
		});

		$grid->setDataSourceCallback(function ($filter, $sort, Paginator $paginator) {
			$qb = $this->em->createQueryBuilder()
				->select('l')->from(LogRecord::class, 'l')->orderBy('l.time', 'DESC');

			$query = $qb->setFirstResult($paginator->getOffset())->setMaxResults($paginator->getItemsPerPage())->getQuery();

			return $query->execute();
		});

		return $grid;

	}

}
