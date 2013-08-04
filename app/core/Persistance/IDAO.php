<?php

namespace Model\Persistence;

use Doctrine\Common\Collections\Collection;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
interface IDao
{

	const FLUSH = FALSE;
	const NO_FLUSH = TRUE;


	/**
	 * Persists given entities, but does not flush.
	 *
	 * @param object|array|Collection
	 */
	function add($entity);


	/**
	 * Persists given entities and flushes them down to the storage.
	 *
	 * @param object|array|Collection|NULL
	 */
	function save($entity = NULL);


	/**
	 * @param object|array|Collection
	 * @param boolean $withoutFlush
	 */
	function delete($entity, $withoutFlush = self::FLUSH);

}