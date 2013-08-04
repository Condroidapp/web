<?php
namespace Model;
use Doctrine\ORM\Mapping as ORM;


/**
 * Lines
 *
 * @ORM\Table(name="program_lines",
 *      indexes={
 *          @ORM\Index (name="cid", columns={"event_id"})
 * })
 * @ORM\Entity
 */
class ProgramLine extends BaseEntity {

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var Event
     *
     * @ORM\ManyToOne(targetEntity="Event")
     * 
     */
    private $event;


}
