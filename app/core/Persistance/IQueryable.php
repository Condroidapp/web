<?php


namespace Model\Persistence;

use Doctrine;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
interface IQueryable
{

	/**
	 * Create a new QueryBuilder instance that is prepopulated for this entity name
	 *
	 * @param string|NULL $alias
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	function createQueryBuilder($alias = NULL);


	/**
	 * @param string|NULL $dql
	 * @return \Doctrine\ORM\Query
	 */
	function createQuery($dql = NULL);

}