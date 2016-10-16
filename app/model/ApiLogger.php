<?php

namespace Model;

use Kdyby\Doctrine\EntityDao;
use Nette\Http\Request;
use Nette\Object;
use Nette\Utils\Strings;

class ApiLogger extends Object
{

	/**
	 * @var \Kdyby\Doctrine\EntityDao
	 */
	private $repository;

	/**
	 * @var \Nette\Http\Request
	 */
	private $httpRequest;

	private $os, $device, $serial;

	public function __construct(EntityDao $repository, Request $httpRequest)
	{
		$this->repository = $repository;
		$this->httpRequest = $httpRequest;

		$header = trim($httpRequest->getHeader('X-Device-Info', ""));
		if ($header) {
			$identifiers = explode(";", $header);
			if (isset($identifiers[0])) {
				if (Strings::startsWith($identifiers[0], ":")) {
					$identifiers[0] = trim($identifiers[0], ": ");
				}
			}

			$this->device = isset($identifiers[0]) ? $identifiers[0] : 'unknown';
			$this->serial = isset($identifiers[1]) ? $identifiers[1] : 'unknown';
			$this->os = isset($identifiers[2]) ? $identifiers[2] : 'unknown';
		}
	}

	public function logFullDownload()
	{
		$entity = $this->getEntity();
		if (!$entity) {
			return;
		}
		$entity->annotationsFullDownload++;
		$this->repository->save($entity);
	}

	public function logUpdate()
	{
		$entity = $this->getEntity();
		if (!$entity) {
			return;
		}
		$entity->annotationsUpdate++;
		$this->repository->save($entity);
	}

	public function logCheck()
	{
		$entity = $this->getEntity();
		if (!$entity) {
			return;
		}
		$entity->annotationsCheck++;
		$this->repository->save($entity);
	}

	public function logEventList()
	{
		$entity = $this->getEntity();
		if (!$entity) {
			return;
		}
		$entity->conList++;
		$this->repository->save($entity);
	}

	private function getEntity()
	{
		if (!$this->device || !$this->serial) {
			return null;
		}
		$entity = $this->repository->findOneBy(['serial' => $this->serial, 'device' => $this->device]);
		if (!$entity) {
			$entity = new ApiLog();
			$entity->device = $this->device;
			$entity->serial = $this->serial;
			$entity->osVersion = $this->os;
		}
		$entity->lastContact = new \DateTime();
		$entity->lastIP = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

		return $entity;
	}

}
