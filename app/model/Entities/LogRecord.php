<?php

namespace Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Logger
 *
 * @ORM\Table(name="logger")
 * @ORM\Entity
 */
class LogRecord extends BaseEntity {


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

    /**
     * @param string $message
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @param string $severity
     */
    public function setSeverity($severity) {
        $this->severity = $severity;
    }

    /**
     * @return string
     */
    public function getSeverity() {
        return $this->severity;
    }

    /**
     * @param string $tag
     */
    public function setTag($tag) {
        $this->tag = $tag;
    }

    /**
     * @return string
     */
    public function getTag() {
        return $this->tag;
    }

    /**
     * @param float $time
     */
    public function setTime($time) {
        $this->time = $time;
    }

    /**
     * @return float
     */
    public function getTime() {
        return $this->time;
    }




}
