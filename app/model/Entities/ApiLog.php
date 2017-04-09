<?php

namespace Model;

use Doctrine\ORM\Mapping as ORM;
use Nette\Object;

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
class ApiLog extends Object
{

	/** @ORM\Id @ORM\Column(type="string") */
	private $device;

	/** @ORM\Id @ORM\Column(type="string") */
	private $serial;

	/** @ORM\Column(type="string", length=100, nullable=true) */
	private $osVersion;

	/** @ORM\Column(type="integer") */
	private $conList = 0;

	/** @ORM\Column(type="integer") */
	private $annotationsFullDownload = 0;

	/** @ORM\Column(type="integer") */
	private $annotationsUpdate = 0;

	/** @ORM\Column(type="integer") */
	private $annotationsCheck = 0;

	/** @ORM\Column(type="datetime") */
	private $lastContact;

	/** @ORM\Column(type="string") */
	private $lastIP;

	public function getDevice()
	{
		return $this->device;
	}

	/**
	 * @param string $device
	 */
	public function setDevice($device)
	{
		$this->device = $device;
	}

	public function getSerial()
	{
		return $this->serial;
	}

	/**
	 * @param string $serial
	 */
	public function setSerial($serial)
	{
		$this->serial = $serial;
	}

	public function getOsVersion()
	{
		return $this->osVersion;
	}

	/**
	 * @param string $osVersion
	 */
	public function setOsVersion($osVersion)
	{
		$this->osVersion = $osVersion;
	}

	public function getConList()
	{
		return $this->conList;
	}

	/**
	 * @param integer $conList
	 */
	public function setConList($conList)
	{
		$this->conList = $conList;
	}

	public function getAnnotationsFullDownload()
	{
		return $this->annotationsFullDownload;
	}

	/**
	 * @param integer $annotationsFullDownload
	 */
	public function setAnnotationsFullDownload($annotationsFullDownload)
	{
		$this->annotationsFullDownload = $annotationsFullDownload;
	}

	public function getAnnotationsUpdate()
	{
		return $this->annotationsUpdate;
	}

	/**
	 * @param integer $annotationsUpdate
	 */
	public function setAnnotationsUpdate($annotationsUpdate)
	{
		$this->annotationsUpdate = $annotationsUpdate;
	}

	public function getAnnotationsCheck()
	{
		return $this->annotationsCheck;
	}

	/**
	 * @param integer $annotationsCheck
	 */
	public function setAnnotationsCheck($annotationsCheck)
	{
		$this->annotationsCheck = $annotationsCheck;
	}

	public function getLastContact()
	{
		return $this->lastContact;
	}

	/**
	 * @param \DateTime $lastContact
	 */
	public function setLastContact($lastContact)
	{
		$this->lastContact = $lastContact;
	}

	public function getLastIP()
	{
		return $this->lastIP;
	}

	/**
	 * @param string $lastIP
	 */
	public function setLastIP($lastIP)
	{
		$this->lastIP = $lastIP;
	}

}
