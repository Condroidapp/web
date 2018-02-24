<?php declare(strict_types = 1);

namespace Model\Queries;

use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;
use Model\Annotation;
use Model\Event;

class AnnotationLastMod extends QueryObject
{

	/** @var \Model\Event */
	private $event;

	public function __construct(Event $event)
	{
		parent::__construct();
		$this->event = $event;
	}

	/**
	 * @param \Kdyby\Persistence\Queryable $repository
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
