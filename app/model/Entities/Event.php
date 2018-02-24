<?php declare(strict_types = 1);

namespace Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cons
 *
 * @ORM\Table(name="events",
 *      indexes={
 *          @ORM\Index (name="active", columns={"active"})
 * })
 * @ORM\Entity
 */
class Event extends BaseEntity
{

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="active", type="boolean", nullable=false)
	 */
	private $active;

	/**
	 * @var bool
	 * @ORM\Column(name="process", type="boolean", nullable=false)
	 */
	private $process = false;

	/**
	 * @var int|null
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
	 * @var string|null
	 */
	private $url;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(name="icon", type="string", length=255, nullable=true)
	 */
	private $icon;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(name="dataUrl", type="string", length=255, nullable=true)
	 */
	private $dataUrl;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(name="message", type="text", nullable=true)
	 */
	private $message;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="hasTimetable", type="boolean")
	 */
	private $hasTimetable = true;

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="hasAnnotations", type="boolean")
	 */
	private $hasAnnotations = true;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(name="locationsFile", type="text", nullable=true)
	 */
	private $locationsFile;

	/**
	 * @var \DateTime|null
	 *
	 * @ORM\Column(name="checkStart", type="datetime", nullable=true)
	 */
	private $checkStart;

	/**
	 * @var \DateTime|null
	 *
	 * @ORM\Column(name="checkStop", type="datetime", nullable=true)
	 */
	private $checkStop;

	/**
	 * @ORM\OneToMany(targetEntity="Annotation", mappedBy="event")
	 * @var \Model\Annotation[]|\Doctrine\Common\Collections\Collection
	 */
	private $annotations;

	/**
	 * @ORM\OneToMany(targetEntity="ProgramLine", mappedBy="event")
	 * @var \Model\ProgramLine[]|\Doctrine\Common\Collections\Collection
	 */
	private $programLines;

	/**
	 * @ORM\OneToMany(targetEntity="Place", mappedBy="event")
	 * @ORM\OrderBy({"categorySort":"ASC", "sort":"ASC", "name":"ASC"})
	 * @var \Model\Place[]|\Doctrine\Common\Collections\Collection
	 */
	private $places;

	/**
	 * @ORM\Column(type="simple_array", nullable=true)
	 * @var float[]|null
	 */
	private $gps;

	public function __construct()
	{
		parent::__construct();
		$this->annotations = new ArrayCollection();
		$this->programLines = new ArrayCollection();
	}

	public function isActive(): bool
	{
		return $this->active;
	}

	public function setActive(bool $active): void
	{
		$this->active = $active;
	}

	public function isProcess(): bool
	{
		return $this->process;
	}

	public function setProcess(bool $process): void
	{
		$this->process = $process;
	}

	public function getYear(): int
	{
		return $this->year;
	}

	public function setYear(int $year): void
	{
		$this->year = $year;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getDate(): string
	{
		return $this->date;
	}

	public function setDate(string $date): void
	{
		$this->date = $date;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function setUrl(string $url): void
	{
		$this->url = $url;
	}

	public function getIcon(): string
	{
		return $this->icon;
	}

	public function setIcon(string $icon): void
	{
		$this->icon = $icon;
	}

	public function getDataUrl(): string
	{
		return $this->dataUrl;
	}

	public function setDataUrl(string $dataUrl): void
	{
		$this->dataUrl = $dataUrl;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	public function setMessage(string $message): void
	{
		$this->message = $message;
	}

	public function isHasTimetable(): bool
	{
		return $this->hasTimetable;
	}

	public function setHasTimetable(bool $hasTimetable): void
	{
		$this->hasTimetable = $hasTimetable;
	}

	public function isHasAnnotations(): bool
	{
		return $this->hasAnnotations;
	}

	public function setHasAnnotations(bool $hasAnnotations): void
	{
		$this->hasAnnotations = $hasAnnotations;
	}

	public function getLocationsFile(): string
	{
		return $this->locationsFile;
	}

	public function setLocationsFile(string $locationsFile): void
	{
		$this->locationsFile = $locationsFile;
	}

	public function getCheckStart(): \DateTime
	{
		return $this->checkStart;
	}

	public function setCheckStart(\DateTime $checkStart): void
	{
		$this->checkStart = $checkStart;
	}

	public function getCheckStop(): \DateTime
	{
		return $this->checkStop;
	}

	public function setCheckStop(\DateTime $checkStop): void
	{
		$this->checkStop = $checkStop;
	}

	/**
	 * @return \Model\Annotation[]
	 */
	public function getAnnotations(): array
	{
		return $this->annotations->toArray();
	}

	/**
	 * @param mixed $annotations
	 */
	public function setAnnotations($annotations): void
	{
		$this->annotations = $annotations;
	}

	/**
	 * @return \Model\ProgramLine[]
	 */
	public function getProgramLines(): array
	{
		return $this->programLines->toArray();
	}

	/**
	 * @param mixed $programLines
	 */
	public function setProgramLines($programLines): void
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
	public function setPlaces($places): void
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
	public function setGps($gps): void
	{
		$this->gps = $gps;
	}

}
