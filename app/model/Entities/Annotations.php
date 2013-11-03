<?php
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
 * @property ProgramLine $location
 * @property string $programLine
 * @property boolean deleted
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
class Annotation extends BaseEntity {

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, length=10)
     */
    private $pid;

    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="annotations")
     */
    private $event;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255, nullable=false)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
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
     * @var \DateTime
     *
     * @ORM\Column(name="startTime", type="datetime", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime
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
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;



    /**
     * @var ProgramLine
     *
     * @ORM\ManyToOne(targetEntity="ProgramLine", fetch="EAGER", cascade={"persist"})
     */
    private $programLine;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $deleted = FALSE;

    /**
     * @param string $annotation
     */
    public function setAnnotation($annotation) {
        $this->annotation = $annotation;
    }

    /**
     * @return string
     */
    public function getAnnotation() {
        return $this->annotation;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author) {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * @param \DateTime $endTime
     */
    public function setEndTime($endTime) {
        $this->endTime = $endTime;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime() {
        return $this->endTime;
    }

    /**
     * @param \Model\Event $event
     */
    public function setEvent($event) {
        $this->event = $event;
    }

    /**
     * @return \Model\Event
     */
    public function getEvent() {
        return $this->event;
    }

    /**
     * @param string $location
     */
    public function setLocation($location) {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @param string $pid
     */
    public function setPid($pid) {
        $this->pid = $pid;
    }

    /**
     * @return string
     */
    public function getPid() {
        return $this->pid;
    }

    /**
     * @param \Model\ProgramLine $programLine
     */
    public function setProgramLine($programLine) {
        $this->programLine = $programLine;
    }

    /**
     * @return \Model\ProgramLine
     */
    public function getProgramLine() {
        return $this->programLine;
    }

    /**
     * @param \DateTime $starttime
     */
    public function setStartTime($starttime) {
        $this->startTime = $starttime;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime() {
        return $this->startTime;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp() {
        return $this->timestamp;
    }

    /**
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted) {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function getDeleted() {
        return $this->deleted;
    }


    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamp() {
        $this->timestamp = new \DateTime();
    }



}
