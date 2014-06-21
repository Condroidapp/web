<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 19.6.14
 * Time: 22:20
 */

namespace Model;


use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\IdentifiedEntity;

/**
 * Class Place
 * @package Model
 *
 * @ORM\Entity
 * @ORM\Table
 */
class Place extends IdentifiedEntity implements \JsonSerializable
{

    /**
     * @ORM\Column
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(nullable=true)
     * @var
     */
    protected $description;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     * @var string
     */
    protected $hours;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    protected $sort;

    /**
     * @ORM\Column(nullable=true, nullable=true)
     * @var
     */
    protected $category;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    protected $categorySort;

    /**
     * @ORM\ManyToOne(targetEntity="Event")
     * @var Event
     */
    protected $event;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     * @var
     */
    protected $gps;

    /**
     * @ORM\Column(nullable=true)
     * @var
     */
    protected $address;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $timestamp;


    /**
     * @ORM\Column(nullable=true)
     * @var string
     */
    protected $url;

    function __construct()
    {
        $this->timestamp = new \DateTime();
    }

    public function jsonSerialize()
    {
        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'gps' => $this->gps ? ['lat' => (float)$this->gps[0], 'lon' => (float)$this->gps[1]] : NULL,
            'sort' => $this->sort,
            'category' => $this->category,
            'categorySort' => $this->categorySort,
            'address' => explode(";", $this->address),
            'hours' => $this->parseHours($this->hours),
            "url" => $this->url,
        ];

        return $data;
    }

    private function parseHours($hours)
    {
        if(empty($hours['type'])) {
            return NULL;
        }

        if(isset($hours['hours'][8])) {
            for ($i = 1; $i < 8; $i++) {
                $hours['hours'][$i] = $hours['hours'][8];
            }
            unset($hours['hours'][8]);
        }
        return $hours;
    }
}