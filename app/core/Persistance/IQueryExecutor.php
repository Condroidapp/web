<?php


namespace Model\Persistence;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
interface IQueryExecutor
{

	/**
	 * @param IQueryObject $queryObject
	 * @return integer
	 */
	function count(IQueryObject $queryObject);


	/**
	 * @param IQueryObject $queryObject
	 * @return array
	 */
	function fetch(IQueryObject $queryObject);


	/**
	 * @param IQueryObject $queryObject
	 * @return object
	 */
	function fetchOne(IQueryObject $queryObject);

}