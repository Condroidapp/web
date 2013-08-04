<?php

namespace Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Logger
 *
 * @ORM\Table(name="logger")
 * @ORM\Entity
 */
class Logger extends BaseEntity {


    /**
     * @var string
     *
     * @ORM\Column(name="severity", type="string", length=5, nullable=false)
     */
    private $severity;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=20, nullable=false)
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255, nullable=false)
     */
    private $message;

    /**
     * @var float
     *
     * @ORM\Column(name="time", type="float", nullable=false)
     */
    private $time;


}
