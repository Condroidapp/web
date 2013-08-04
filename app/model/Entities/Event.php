<?php

namespace Model;

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
class Event extends BaseEntity {

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active;

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
    private $dataurl;

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


}
