<?php

namespace Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\IdentifiedEntity;

/**
 * Cons
 *
 * @ORM\Table(name="events",
 *      indexes={
 *          @ORM\Index (name="active", columns={"active"})
 * })
 * @ORM\Entity
 */
class Event extends IdentifiedEntity
{

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="active", type="boolean", nullable=false)
	 */
	private $active;

	/**
	 * @var boolean
	 * @ORM\Column(name="process", type="boolean", nullable=false)
	 */
	private $process = false;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="year", type="integer", nullable=true)
	 */
	private $year;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255, nullable=false)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="date", type="string", length=255, nullable=false)
	 */
	private $date;

	/**
	 * @ORM\Column(nullable=true)
	 * @var string
	 */
	private $url;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="icon", type="string", length=255, nullable=true)
	 */
	private $icon;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="dataUrl", type="string", length=255, nullable=true)
	 */
	private $dataUrl;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="message", type="text", nullable=true)
	 */
	private $message;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="hasTimetable", type="boolean", nullable=true)
	 */
	private $hasTimetable;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="hasAnnotations", type="boolean", nullable=true)
	 */
	private $hasAnnotations;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="locationsFile", type="text", nullable=true)
	 */
	private $locationsFile;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="checkStart", type="datetime", nullable=true)
	 */
	private $checkStart;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="checkStop", type="datetime", nullable=true)
	 */
	private $checkStop;

	/**
	 * @ORM\OneToMany(targetEntity="Annotation", mappedBy="event")
	 */
	private $annotations;

	/**
	 * @ORM\OneToMany(targetEntity="ProgramLine", mappedBy="event")
	 */
	private $programLines;

	/**
	 * @ORM\OneToMany(targetEntity="Place", mappedBy="event")
	 * @ORM\OrderBy({"categorySort":"ASC", "sort":"ASC", "name":"ASC"})
	 */
	private $places;

	/**
	 * @ORM\Column(type="simple_array", nullable=true)
	 * @var
	 */
	private $gps;

	public function __construct()
	{
		$this->annotations = new ArrayCollection();
		$this->programLines = new ArrayCollection();
	}

	/**
	 * @return boolean
	 */
	public function isActive()
	{
		return $this->active;
	}

	/**
	 * @param boolean $active
	 */
	public function setActive($active)
	{
		$this->active = $active;
	}

	/**
	 * @return boolean
	 */
	public function isProcess()
	{
		return $this->process;
	}

	/**
	 * @param boolean $process
	 */
	public function setProcess($process)
	{
		$this->process = $process;
	}

	/**
	 * @return int
	 */
	public function getYear()
	{
		return $this->year;
	}

	/**
	 * @param int $year
	 */
	public function setYear($year)
	{
		$this->year = $year;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getDate()
	{
		return $this->date;
	}

	/**
	 * @param string $date
	 */
	public function setDate($date)
	{
		$this->date = $date;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->icon;
	}

	/**
	 * @param string $icon
	 */
	public function setIcon($icon)
	{
		$this->icon = $icon;
	}

	/**
	 * @return string
	 */
	public function getDataUrl()
	{
		return $this->dataUrl;
	}

	/**
	 * @param string $dataUrl
	 */
	public function setDataUrl($dataUrl)
	{
		$this->dataUrl = $dataUrl;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message)
	{
		$this->message = $message;
	}

	/**
	 * @return boolean
	 */
	public function isHasTimetable()
	{
		return $this->hasTimetable;
	}

	/**
	 * @param boolean $hasTimetable
	 */
	public function setHasTimetable($hasTimetable)
	{
		$this->hasTimetable = $hasTimetable;
	}

	/**
	 * @return boolean
	 */
	public function isHasAnnotations()
	{
		return $this->hasAnnotations;
	}

	/**
	 * @param boolean $hasAnnotations
	 */
	public function setHasAnnotations($hasAnnotations)
	{
		$this->hasAnnotations = $hasAnnotations;
	}

	/**
	 * @return string
	 */
	public function getLocationsFile()
	{
		return $this->locationsFile;
	}

	/**
	 * @param string $locationsFile
	 */
	public function setLocationsFile($locationsFile)
	{
		$this->locationsFile = $locationsFile;
	}

	/**
	 * @return \DateTime
	 */
	public function getCheckStart()
	{
		return $this->checkStart;
	}

	/**
	 * @param \DateTime $checkStart
	 */
	public function setCheckStart($checkStart)
	{
		$this->checkStart = $checkStart;
	}

	/**
	 * @return \DateTime
	 */
	public function getCheckStop()
	{
		return $this->checkStop;
	}

	/**
	 * @param \DateTime $checkStop
	 */
	public function setCheckStop($checkStop)
	{
		$this->checkStop = $checkStop;
	}

	/**
	 * @return mixed
	 */
	public function getAnnotations()
	{
		return $this->annotations;
	}

	/**
	 * @param mixed $annotations
	 */
	public function setAnnotations($annotations)
	{
		$this->annotations = $annotations;
	}

	/**
	 * @return mixed
	 */
	public function getProgramLines()
	{
		return $this->programLines;
	}

	/**
	 * @param mixed $programLines
	 */
	public function setProgramLines($programLines)
	{
		$this->programLines = $programLines;
	}

	/**
	 * @return mixed
	 */
	public function getPlaces()
	{
		return $this->places;
	}

	/**
	 * @param mixed $places
	 */
	public function setPlaces($places)
	{
		$this->places = $places;
	}

	/**
	 * @return mixed
	 */
	public function getGps()
	{
		return $this->gps;
	}

	/**
	 * @param mixed $gps
	 */
	public function setGps($gps)
	{
		$this->gps = $gps;
	}

}
