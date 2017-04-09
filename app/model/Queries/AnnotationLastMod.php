<?php

namespace Model\Queries;

use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;
use Model\Annotation;

class AnnotationLastMod extends QueryObject
{

	private $event;

	public function __construct($event)
	{
		parent::__construct();
		$this->event = $event;
	}

	/**
	 * @param Queryable $repository
	 * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
	 */
	protected function doCreateQuery(Queryable $repository)
	{
		return $repository->createQueryBuilder()
			->select('a.timestamp')
			->from(Annotation::class, 'a')
			->andWhere('a.deleted = :deleted')
			->andWhere('a.event = :event')
			->addOrderBy('a.timestamp', 'desc')
			->setParameters([
				'deleted' => false,
				'event' => $this->event,
			])
			->getQuery()
			->setMaxResults(1);
	}

}
