<?php

namespace Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity;
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
class Event extends IdentifiedEntity {

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    protected $active;

    /**
     * @var boolean
     * @ORM\Column(name="process", type="boolean", nullable=false)
     */
    protected $process = FALSE;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    protected $year;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string", length=255, nullable=false)
     */
    protected $date;

    /**
     * @ORM\Column(nullable=true)
     * @var string
     */
    protected $url;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     */
    protected $icon;

    /**
     * @var string
     *
     * @ORM\Column(name="dataUrl", type="string", length=255, nullable=true)
     */
    protected $dataUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    protected $message;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasTimetable", type="boolean", nullable=true)
     */
    protected $hasTimetable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hasAnnotations", type="boolean", nullable=true)
     */
    protected $hasAnnotations;

    /**
     * @var string
     *
     * @ORM\Column(name="locationsFile", type="text", nullable=true)
     */
    protected $locationsFile;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="checkStart", type="datetime", nullable=true)
     */
    protected $checkStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="checkStop", type="datetime", nullable=true)
     */
    protected $checkStop;
    /**
     * @ORM\OneToMany(targetEntity="Annotation", mappedBy="event")
     */
    protected $annotations;
    /**
     * @ORM\OneToMany(targetEntity="ProgramLine", mappedBy="event")
     */
    protected $programLines;

    /**
     * @ORM\OneToMany(targetEntity="Place", mappedBy="event")
     * @ORM\OrderBy({"categorySort":"ASC", "sort":"ASC", "name":"ASC"})
     */
    protected $places;


    public function __construct() {
        $this->annotations = new ArrayCollection();
        $this->programLines = new ArrayCollection();
    }


}
