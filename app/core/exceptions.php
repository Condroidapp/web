<?php
namespace App;
/**
 * The exception that is thrown when a value (typically returned by function) does not match with the expected value.
 */
class UnexpectedValueException extends \UnexpectedValueException
{


	/**
	 * @param mixed $list
	 * @param string|object $class
	 * @param string $property
	 *
	 * @return \Maps\UnexpectedValueException
	 */
	public static function invalidEventValue($list, $class, $property)
	{
		$class = is_object($class) ? get_class($class) : $class;
		return new static("Property $class::$$property must be array or NULL, " . gettype($list) . " given.");
	}



	/**
	 * @param string|object $class
	 * @param string $property
	 *
	 * @return \Maps\UnexpectedValueException
	 */
	public static function notACollection($class, $property)
	{
		$class = is_object($class) ? get_class($class) : $class;
		return new static("Class property $class::\$$property is not an instance of Doctrine\\Common\\Collections\\Collection.");
	}



	/**
	 * @param string|object $class
	 * @param string $property
	 *
	 * @return \Maps\UnexpectedValueException
	 */
	public static function collectionCannotBeReplaced($class, $property)
	{
		$class = is_object($class) ? get_class($class) : $class;
		return new static("Class property $class::\$$property is an instance of Doctrine\\Common\\Collections\\Collection. Use add<property>() and remove<property>() methods to manipulate it or declare your own.");
	}

}

use Doctrine\ORM\Query;
/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class QueryException extends \Exception
{

	/** @var \Doctrine\ORM\Query */
	private $query;



	/**
	 * @param \Exception $previous
	 * @param \Doctrine\ORM\Query $query
	 * @param string $message
	 */
	public function __construct(\Exception $previous, Query $query = NULL, $message = "")
	{
		parent::__construct($message ? : $previous->getMessage(), 0, $previous);
		$this->query = $query;
	}



	/**
	 * @return \Doctrine\ORM\Query
	 */
	public function getQuery()
	{
		return $this->query;
	}

}

/**
 * Class InvalidArgumentException
 *
 * @package Maps
 * @author Jan Langer <langeja1@fit.cvut.cz>
 */
class InvalidArgumentException extends \InvalidArgumentException {
}

/**
 * Class InvalidStateException
 *
 * @package Maps
 * @author Jan Langer <langeja1@fit.cvut.cz>
 */
class InvalidStateException extends \RuntimeException {
}

/**
 * Class ShellCommandException
 *
 * @package Maps
 * @author Jan Langer <langeja1@fit.cvut.cz>
 */
class ShellCommandException extends \RuntimeException {
}

/**
 * Class StaticClassException
 *
 * @package Maps
 * @author Jan Langer <langeja1@fit.cvut.cz>
 */
class StaticClassException extends \LogicException {
}