<?php declare(strict_types = 1);

namespace Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ApiLog
 * @ORM\Table(name="apicalls")
 * @ORM\Entity
 *
 * @property string $device
 * @property string $serial
 * @property string $osVersion
 * @property int $conList
 * @property int $annotationsFullDownload
 * @property int $annotationsUpdate
 * @property int $annotationsCheck
 * @property \DateTime $lastContact
 * @property string $lastIP
 *
 *
 */
class ApiLog
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $device;

	/**
	 * @ORM\Id
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $serial;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $osVersion;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $conList = 0;

	/**
	 * @var int
	 * @ORM\Column(type="integer")
	 */
	private $annotationsFullDownload = 0;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $annotationsUpdate = 0;

	/**
	 * @ORM\Column(type="integer")
	 * @var int
	 */
	private $annotationsCheck = 0;

	/**
	 * @ORM\Column(type="datetime")
	 * @var \DateTime
	 */
	private $lastContact;

	/**
	 * @ORM\Column(type="string")
	 * @var string
	 */
	private $lastIP;

	public function getDevice(): string
	{
		return $this->device;
	}

	public function setDevice(string $device): void
	{
		$this->device = $device;
	}

	public function getSerial(): string
	{
		return $this->serial;
	}

	public function setSerial(string $serial): void
	{
		$this->serial = $serial;
	}

	public function getOsVersion(): ?string
	{
		return $this->osVersion;
	}

	public function setOsVersion(?string $osVersion): void
	{
		$this->osVersion = $osVersion;
	}

	public function getConList(): int
	{
		return $this->conList;
	}

	public function setConList(int $conList): void
	{
		$this->conList = $conList;
	}

	public function getAnnotationsFullDownload(): int
	{
		return $this->annotationsFullDownload;
	}

	public function setAnnotationsFullDownload(int $annotationsFullDownload): void
	{
		$this->annotationsFullDownload = $annotationsFullDownload;
	}

	public function getAnnotationsUpdate(): int
	{
		return $this->annotationsUpdate;
	}

	public function setAnnotationsUpdate(int $annotationsUpdate): void
	{
		$this->annotationsUpdate = $annotationsUpdate;
	}

	public function getAnnotationsCheck(): int
	{
		return $this->annotationsCheck;
	}

	public function setAnnotationsCheck(int $annotationsCheck): void
	{
		$this->annotationsCheck = $annotationsCheck;
	}

	public function getLastContact(): \DateTime
	{
		return $this->lastContact;
	}

	public function setLastContact(\DateTime $lastContact): void
	{
		$this->lastContact = $lastContact;
	}

	public function getLastIP(): string
	{
		return $this->lastIP;
	}

	public function setLastIP(string $lastIP): void
	{
		$this->lastIP = $lastIP;
	}

}
