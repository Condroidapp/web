<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 19.6.14
 * Time: 22:20
 */

namespace Model;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * Class Place
 * @package Model
 *
 * @ORM\Entity
 * @ORM\Table
 */
class Place extends BaseEntity implements JsonSerializable
{

	/**
	 * @ORM\Column
	 * @var string
	 */
	private $name;

	/**
	 * @ORM\Column(nullable=true)
	 * @var string|null
	 */
	private $description;

	/**
	 * @ORM\Column(type="json_array", nullable=true)
	 * @var string[]|null
	 */
	private $hours;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @var int|null
	 */
	private $sort;

	/**
	 * @ORM\Column(nullable=true, nullable=true)
	 * @var string|null
	 */
	private $category;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 * @var int|null
	 */
	private $categorySort;

	/**
	 * @ORM\ManyToOne(targetEntity="Event")
	 * @var \Model\Event
	 */
	private $event;

	/**
	 * @ORM\Column(type="simple_array", nullable=true)
	 * @var float[]|null
	 */
	private $gps;

	/**
	 * @ORM\Column(nullable=true)
	 * @var string|null
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
		parent::__construct();
		$this->timestamp = new \DateTime();
	}

	/**
	 * @return mixed[]
	 */
	public function jsonSerialize(): array
	{
		$data = [
			'name' => $this->name,
			'description' => $this->description,
			'gps' => $this->gps ? ['lat' => $this->gps[0], 'lon' => $this->gps[1]] : null,
			'sort' => $this->sort,
			'category' => $this->category,
			'categorySort' => $this->categorySort,
			'address' => explode(';', $this->address),
			'hours' => $this->parseHours($this->hours),
			'url' => $this->url,
			'id' => $this->id,
		];

		return $data;
	}

	/**
	 * @param string[]|null $hours
	 * @return mixed[]|null
	 */
	private function parseHours(?array $hours): ?array
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

	public function getName(): string
	{
		return $this->name;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return string[]|null
	 */
	public function getHours(): ?array
	{
		return $this->hours;
	}

	public function getSort(): ?int
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

	public function getCategorySort(): ?int
	{
		return $this->categorySort;
	}

	public function getEvent(): Event
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

	public function getTimestamp(): \DateTime
	{
		return $this->timestamp;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription($description): void
	{
		$this->description = $description;
	}

	/**
	 * @param string[]|null $hours
	 */
	public function setHours(?array $hours): void
	{
		$this->hours = $hours;
	}

	public function setSort(int $sort): void
	{
		$this->sort = $sort;
	}

	/**
	 * @param mixed $category
	 */
	public function setCategory($category): void
	{
		$this->category = $category;
	}

	public function setCategorySort(int $categorySort): void
	{
		$this->categorySort = $categorySort;
	}

	public function setEvent(Event $event): void
	{
		$this->event = $event;
	}

	/**
	 * @param mixed $gps
	 */
	public function setGps($gps): void
	{
		$this->gps = $gps;
	}

	/**
	 * @param mixed $address
	 */
	public function setAddress($address): void
	{
		$this->address = $address;
	}

	public function setTimestamp(\DateTime $timestamp): void
	{
		$this->timestamp = $timestamp;
	}

	public function setUrl(string $url): void
	{
		$this->url = $url;
	}

}
