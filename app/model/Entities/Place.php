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
	private $name;

	/**
	 * @ORM\Column(nullable=true)
	 * @var
	 */
	private $description;

	/**
	 * @ORM\Column(type="json_array", nullable=true)
	 * @var string
	 */
	private $hours;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @var int
	 */
	private $sort;

	/**
	 * @ORM\Column(nullable=true, nullable=true)
	 * @var
	 */
	private $category;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @var int
	 */
	private $categorySort;

	/**
	 * @ORM\ManyToOne(targetEntity="Event")
	 * @var Event
	 */
	private $event;

	/**
	 * @ORM\Column(type="simple_array", nullable=true)
	 * @var
	 */
	private $gps;

	/**
	 * @ORM\Column(nullable=true)
	 * @var
	 */
	private $address;

	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime")
	 */
	private $timestamp;

	/**
	 * @ORM\Column(nullable=true)
	 * @var string
	 */
	private $url;

	public function __construct()
	{
		$this->timestamp = new \DateTime();
	}

	public function jsonSerialize()
	{
		$data = [
			'name' => $this->name,
			'description' => $this->description,
			'gps' => $this->gps ? ['lat' => (float) $this->gps[0], 'lon' => (float) $this->gps[1]] : null,
			'sort' => $this->sort,
			'category' => $this->category,
			'categorySort' => $this->categorySort,
			'address' => explode(";", $this->address),
			'hours' => $this->parseHours($this->hours),
			"url" => $this->url,
			'id' => $this->id,
		];

		return ($data);
	}

	private function parseHours($hours)
	{
		if (empty($hours['type'])) {
			return null;
		}

		if ($hours['type'] === 2 && isset($hours['hours'][8])) {
			for ($i = 1; $i < 8; $i++) {
				$hours['hours'][$i] = $hours['hours'][8];
			}
			unset($hours['hours'][8]);
		}
		$hours['hours'] = (object) $hours['hours'];

		return $hours;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getHours()
	{
		return $this->hours;
	}

	/**
	 * @return int
	 */
	public function getSort()
	{
		return $this->sort;
	}

	/**
	 * @return mixed
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * @return int
	 */
	public function getCategorySort()
	{
		return $this->categorySort;
	}

	/**
	 * @return Event
	 */
	public function getEvent()
	{
		return $this->event;
	}

	/**
	 * @return mixed
	 */
	public function getGps()
	{
		return $this->gps;
	}

	/**
	 * @return mixed
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * @return \DateTime
	 */
	public function getTimestamp()
	{
		return $this->timestamp;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @param string $hours
	 */
	public function setHours($hours)
	{
		$this->hours = $hours;
	}

	/**
	 * @param int $sort
	 */
	public function setSort($sort)
	{
		$this->sort = $sort;
	}

	/**
	 * @param mixed $category
	 */
	public function setCategory($category)
	{
		$this->category = $category;
	}

	/**
	 * @param int $categorySort
	 */
	public function setCategorySort($categorySort)
	{
		$this->categorySort = $categorySort;
	}

	/**
	 * @param Event $event
	 */
	public function setEvent($event)
	{
		$this->event = $event;
	}

	/**
	 * @param mixed $gps
	 */
	public function setGps($gps)
	{
		$this->gps = $gps;
	}

	/**
	 * @param mixed $address
	 */
	public function setAddress($address)
	{
		$this->address = $address;
	}

	/**
	 * @param \DateTime $timestamp
	 */
	public function setTimestamp($timestamp)
	{
		$this->timestamp = $timestamp;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

}
