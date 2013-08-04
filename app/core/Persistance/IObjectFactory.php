<?php


namespace Model\Persistence;



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
interface IObjectFactory
{

    /**
     * @param array $arguments
     * @return object
     */
	function createNew($arguments = array());

}