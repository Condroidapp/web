<?php declare(strict_types = 1);

namespace Model;

use Doctrine\ORM\Mapping as ORM;
use Nette\SmartObject;

/**
 * Base Entity parent class
 * @ORM\MappedSuperclass
 * @author Jan Langer <langeja1@fit.cvut.cz>
 * @property-read int $id
 */
abstract class BaseEntity
{

	use SmartObject;

	/**
	 * @ORM\Id @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 * @var int
	 */
	private $id;

	public function __construct()
	{
	}

	final public function getId(): int
	{
		return $this->id;
	}

}
