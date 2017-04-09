<?php

namespace Model;

use Doctrine\ORM\Mapping as ORM;
use Nette\Object;

/**
 * Base Entity parent class
 * @ORM\MappedSuperclass
 * @author Jan Langer <langeja1@fit.cvut.cz>
 * @property-read int $id
 */
abstract class BaseEntity extends Object
{

	/**
	 * @ORM\Id @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @var int
	 */
	private $id;

	public function __construct()
	{
	}

	/**
	 * @return int
	 */
	final public function getId()
	{
		return $this->id;
	}

}
