<?php declare(strict_types = 1);

namespace Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Annotations
 * @property string $pid
 * @property Event $event
 * @property string $author
 * @property string $title
 * @property string $annotation
 * @property string $type
 * @property \DateTime $startTime
 * @property \DateTime $endTime
 * @property \DateTime $timestamp
 * @property string $location
 * @property ProgramLine|null $programLine
 * @property boolean $deleted
 *
 * @ORM\Table(name="annotations",
 *   indexes={
 *      @ORM\Index(name="cid", columns={"event_id"})
 *      },
 *  uniqueConstraints= {
 *      @ORM\UniqueConstraint(name="pid_uq", columns={"event_id","pid"})
 *      }
 * )
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Annotation extends BaseEntity
{

	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=false, length=10)
	 */
	private $pid;

	/**
	 * @var \Model\Event
	 * @ORM\ManyToOne(targetEntity="Event", inversedBy="annotations")
	 */
	private $event;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(name="author", type="string", length=255, nullable=true)
	 */
	private $author;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="title", type="string", length=255, nullable=false)
	 */
	private $title;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(name="annotation", type="text", nullable=true)
	 */
	private $annotation;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="type", type="string", length=20, nullable=false)
	 */
	private $type;

	/**
	 * @var \DateTime|null
	 *
	 * @ORM\Column(name="startTime", type="datetime", nullable=true)
	 */
	private $startTime;

	/**
	 * @var \DateTime|null
	 *
	 * @ORM\Column(name="endTime", type="datetime", nullable=true)
	 */
	private $endTime;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="timestamp", type="datetime", nullable=false)
	 */
	private $timestamp;

	/**
	 * @var string|null
	 *
	 * @ORM\Column(name="location", type="string", length=255, nullable=true)
	 */
	private $location;

	/**
	 * @var \Model\ProgramLine|null
	 *
	 * @ORM\ManyToOne(targetEntity="ProgramLine", fetch="EAGER", cascade={"persist"})
	 */
	private $programLine;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	private $deleted = false;

	/**
	 * @var \DateTime|null
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $deletedAt;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime")
	 */
	private $createdAt;

	/**
	 * @ORM\Column(nullable=true)
	 * @var string|null
	 */
	protected $imdb;

	public function setImdb(?string $imdb): void
	{
		$this->imdb = $imdb;
	}

	public function getImdb(): ?string
	{
		return $this->imdb;
	}

	public function setAnnotation(?string $annotation): void
	{
		$this->annotation = $annotation;
		$this->createdAt = new \DateTime();
	}

	public function getAnnotation(): ?string
	{
		return $this->annotation;
	}

	public function setAuthor(?string $author): void
	{
		$this->author = $author;
	}

	public function getAuthor(): ?string
	{
		return $this->author;
	}

	public function setEndTime(?\DateTime $endTime): void
	{
		$this->endTime = $endTime;
	}

	public function getEndTime(): ?\DateTime
	{
		return $this->endTime;
	}

	public function setEvent(Event $event): void
	{
		$this->event = $event;
	}

	public function getEvent(): Event
	{
		return $this->event;
	}

	public function setLocation(?string $location): void
	{
		$this->location = $location;
	}

	public function getLocation(): ?string
	{
		return $this->location;
	}

	public function setPid(string $pid): void
	{
		$this->pid = $pid;
	}

	public function getPid(): string
	{
		return $this->pid;
	}

	public function setProgramLine(?ProgramLine $programLine): void
	{
		$this->programLine = $programLine;
	}

	public function getProgramLine(): ?ProgramLine
	{
		return $this->programLine;
	}

	public function setStartTime(?\DateTime $starttime): void
	{
		$this->startTime = $starttime;
	}

	public function getStartTime(): ?\DateTime
	{
		return $this->startTime;
	}

	public function setTimestamp(\DateTime $timestamp): void
	{
		$this->timestamp = $timestamp;
	}

	public function getTimestamp(): \DateTime
	{
		return $this->timestamp;
	}

	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setType(string $type): void
	{
		$this->type = $type;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function setDeleted(bool $deleted): void
	{
		$this->deleted = $deleted;
	}

	public function getDeleted(): bool
	{
		return $this->deleted;
	}

	/**
	 * @ORM\PrePersist
	 * @ORM\PreUpdate
	 */
	public function updateTimestamp(): void
	{
		$this->timestamp = new \DateTime();
	}

	public function getDeletedAt(): ?\DateTime
	{
		return $this->deletedAt;
	}

	public function setDeletedAt(?\DateTime $deletedWhen): void
	{
		$this->deletedAt = $deletedWhen;
	}

	public function getCreatedAt(): \DateTime
	{
		return $this->createdAt;
	}

}
