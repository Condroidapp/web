<?php


namespace Model\Persistence;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
interface IQueryObject
{

	/**
	 * @param IQueryable $repository
	 * @return integer
	 */
	function count(IQueryable $repository);


	/**
	 * @param IQueryable $repository
	 * @return mixed|ResultSet
	 */
	function fetch(IQueryable $repository);


	/**
	 * @param IQueryable $repository
	 * @return object
	 */
	function fetchOne(IQueryable $repository);

}