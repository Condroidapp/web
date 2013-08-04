<?php
namespace Model;

use Doctrine\ORM\Mapping as ORM;


/**
 * Annotations
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
 */
class Annotation extends BaseEntity {

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, length=10)
     */
    private $pid;

    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="Event")
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
    private $starttime;

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
     * @ORM\ManyToOne(targetEntity="ProgramLine")
     */
    private $programLine;

}
