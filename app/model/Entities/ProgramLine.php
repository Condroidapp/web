<?php declare(strict_types = 1);

namespace Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lines
 * @property string $title
 * @property Event $event
 *
 *
 * @ORM\Table(name="program_lines",
 *      indexes={
 *          @ORM\Index (name="cid", columns={"event_id"})
 * })
 * @ORM\Entity
 */
class ProgramLine extends BaseEntity
{

	/**
	 * @var string
	 *
	 * @ORM\Column(name="title", type="string", length=255, nullable=false)
	 */
	private $title;

	/**
	 * @var \Model\Event
	 *
	 * @ORM\ManyToOne(targetEntity="Event", inversedBy="programLines")
	 *
	 */
	private $event;

	public function __construct(string $title, Event $event)
	{
		parent::__construct();
		$this->title = $title;
		$this->event = $event;
	}

	public function setEvent(Event $event): void
	{
		$this->event = $event;
	}

	public function getEvent(): Event
	{
		return $this->event;
	}

	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

}
