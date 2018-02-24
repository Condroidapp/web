<?php declare(strict_types = 1);

namespace Model;

use Doctrine\ORM\Query;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;
use Nette\Utils\Strings;

/**
 * Basic fetch by params query
 *
 * @package Maps\Model
 * @author Jan Langer <langeja1@fit.cvut.cz>
 */
class BasicFetchByQuery extends QueryObject
{

	/** @var mixed[] conditions */
	private $conditions;

	/**
	 * @param mixed[] $conditions
	 */
	public function __construct(array $conditions)
	{
		parent::__construct();
		$this->conditions = $conditions;
	}

	/**
	 * @param \Kdyby\Doctrine\EntityRepository|\Kdyby\Persistence\Queryable $repository
	 * @return \Doctrine\ORM\Query
	 */
	protected function doCreateQuery(Queryable $repository): Query
	{
		$where = [];
		$params = [];
		$i = 0;
		foreach ($this->conditions as $condition => $value) {
			$separator = '= ?';
			if (Strings::endsWith($condition, '?')) {
				$separator = '';
			}
			$where[] = 'b.' . $condition . $separator . $i++;
			$params[] = $value;
		}
		/** @var \Kdyby\Doctrine\EntityRepository $repository */
		$repository = $repository;
		$className = $repository->getClassName();
		$q = $repository->createQuery('SELECT b FROM ' . $className . ' b ' . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : ''))
			->setParameters($params);

		return $q;
	}    //put your code here

}
