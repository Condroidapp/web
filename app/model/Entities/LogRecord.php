<?php declare(strict_types = 1);

namespace Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Logger
 *
 * @ORM\Table(name="logger")
 * @ORM\Entity
 */
class LogRecord extends BaseEntity
{

	/**
	 * @var string
	 *
	 * @ORM\Column(name="severity", type="string", length=5, nullable=false)
	 */
	private $severity;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="tag", type="string", length=20, nullable=false)
	 */
	private $tag;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="message", type="string", length=255, nullable=false)
	 */
	private $message;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="time", type="float", nullable=false)
	 */
	private $time;

	public function setMessage(string $message): void
	{
		$this->message = $message;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	public function setSeverity(string $severity): void
	{
		$this->severity = $severity;
	}

	public function getSeverity(): string
	{
		return $this->severity;
	}

	public function setTag(string $tag): void
	{
		$this->tag = $tag;
	}

	public function getTag(): string
	{
		return $this->tag;
	}

	public function setTime(float $time): void
	{
		$this->time = $time;
	}

	public function getTime(): float
	{
		return $this->time;
	}

}
