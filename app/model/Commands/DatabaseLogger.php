<?php declare(strict_types = 1);

namespace Model\Commands;

use Doctrine\ORM\EntityManager;
use Model\LogRecord;

class DatabaseLogger implements ILogger
{

	/** @var mixed[] */
	private $records = [];

	/** @var \Doctrine\ORM\EntityManager */
	private $entityManager;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function start(string $message): void
	{
		$this->addRecord($message, 'START', self::INFO);
	}

	public function end(): void
	{
		$this->addRecord('End', 'END', self::INFO);
		$this->entityManager->flush($this->records);
	}

	public function log(string $message, string $severity = self::INFO): void
	{
		$this->addRecord($message, '', $severity);
	}

	private function addRecord(string $message, string $tag, string $severity): void
	{
		$record = new LogRecord();

		$record->setMessage($message);
		$record->setSeverity($severity);
		$record->setTag($tag);
		$record->setTime(microtime(true));
		$this->entityManager->persist($record);
		$this->records[] = $record;
	}

}
