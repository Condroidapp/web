<?php declare(strict_types = 1);

namespace Model;

use Doctrine\ORM\EntityManager;
use Nette\Http\Request;
use Nette\Utils\Strings;

class ApiLogger
{

	/** @var string */
	private $os;

	/** @var string */
	private $device;

	/** @var string */
	private $serial;

	/** @var \Doctrine\ORM\EntityManager */
	private $entityManager;

	public function __construct(EntityManager $entityManager, Request $httpRequest)
	{
		$this->entityManager = $entityManager;

		$header = trim($httpRequest->getHeader('X-Device-Info', null));
		if ($header === null) {
			return;
		}

		$identifiers = explode(';', $header);
		if (isset($identifiers[0])) {
			if (Strings::startsWith($identifiers[0], ':')) {
				$identifiers[0] = trim($identifiers[0], ': ');
			}
		}

		$this->device = $identifiers[0] ?? 'unknown';
		$this->serial = $identifiers[1] ?? 'unknown';
		$this->os = $identifiers[2] ?? 'unknown';
	}

	public function logFullDownload(): void
	{
		$entity = $this->getEntity();
		if (!$entity) {
			return;
		}
		$entity->annotationsFullDownload++;
		$this->entityManager->flush($entity);
	}

	public function logUpdate(): void
	{
		$entity = $this->getEntity();
		if (!$entity) {
			return;
		}
		$entity->annotationsUpdate++;
		$this->entityManager->flush($entity);
	}

	public function logCheck(): void
	{
		$entity = $this->getEntity();
		if (!$entity) {
			return;
		}
		$entity->annotationsCheck++;
		$this->entityManager->flush($entity);
	}

	public function logEventList(): void
	{
		$entity = $this->getEntity();
		if (!$entity) {
			return;
		}
		$entity->conList++;
		$this->entityManager->flush($entity);
	}

	private function getEntity(): ?ApiLog
	{
		if (!$this->device || !$this->serial) {
			return null;
		}
		/** @var \Model\ApiLog $entity */
		$entity = $this->entityManager->getRepository(ApiLog::class)->findOneBy(['serial' => $this->serial, 'device' => $this->device]);
		if (!$entity) {
			$entity = new ApiLog();
			$entity->setDevice($this->device);
			$entity->setSerial($this->serial);
			$entity->setOsVersion($this->os);
			$this->entityManager->persist($entity);
		}
		$entity->setLastContact(new \DateTime());
		$entity->setLastIP($_SERVER['REMOTE_ADDR'] ?? null);

		return $entity;
	}

}
