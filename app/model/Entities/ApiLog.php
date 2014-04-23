<?php

namespace Model;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\BaseEntity as KBaseEntity;
use Nette\Object;


/**
 * Class ApiLog
 * @ORM\Table(name="apicalls")
 * @ORM\Entity
 *
 * @property string $device
 * @property string $serial
 * @property string $osVersion
 * @property int $conList
 * @property int $annotationsFullDownload
 * @property int $annotationsUpdate
 * @property int $annotationsCheck
 * @property \DateTime $lastContact
 * @property string $lastIP
 *
 *
 */
class ApiLog extends KBaseEntity {

    /** @ORM\Id @ORM\Column(type="string") */
    protected $device;
    /** @ORM\Id @ORM\Column(type="string") */
    protected $serial;
    /** @ORM\Column(type="string", length=100, nullable=true) */
    protected $osVersion;
    /** @ORM\Column(type="integer") */
    protected $conList = 0;
    /** @ORM\Column(type="integer") */
    protected $annotationsFullDownload = 0;
    /** @ORM\Column(type="integer") */
    protected $annotationsUpdate = 0;
    /** @ORM\Column(type="integer") */
    protected $annotationsCheck = 0;
    /** @ORM\Column(type="datetime") */
    protected $lastContact;
    /** @ORM\Column(type="string") */
    protected $lastIP;

} 