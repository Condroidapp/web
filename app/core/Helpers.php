<?php declare(strict_types = 1);

namespace App\Tools;

use InvalidArgumentException;
use Nette\InvalidStateException;
use Nette\StaticClassException;

class Helpers
{

	/**
	 * Static class - cannot be instantiated.
	 *
	 * @throws \Nette\StaticClassException
	 */
	final public function __construct()
	{
		throw new StaticClassException();
	}

	/**
	 * Maps collection as associative array using some key inside it
	 *
	 * @param array $collection
	 * @param string $key
	 * @return array collection mapped with key
	 */
	public static function mapAssoc(array $collection, string $key): array
	{
		$arr = [];
		foreach ($collection as $item) {
			if (!isset($item[$key])) {
				throw new InvalidArgumentException('Key ' . $key . ' does not exists in every item.');
			}
			if (array_key_exists($item[$key], $arr)) {
				throw new InvalidStateException('Associative key ' . $key . ' is not unique in collection. Item ' . $item[$key] . ' found again.');
			}
			$arr[$item[$key]] = $item;
		}

		return $arr;
	}

}
