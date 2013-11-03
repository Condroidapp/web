<?php

namespace Model;

use Doctrine\ORM\Mapping as ORM;


/**
 * Base Entity parent class
 * @ORM\MappedSuperclass
 * @author Jan Langer <langeja1@fit.cvut.cz>
 * @property-read int $id
 */
abstract class BaseEntity extends \Nette\Object {

    /**
     * @ORM\Id @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    protected $id;
    
    public function __construct() {
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCacheKeys() {
        if($this->id != NULL) {
            return array(get_class($this) . "#" . $this->id);
        }
        return array();
    }

    public static function getClassName() {
        return get_called_class();
    }

}
