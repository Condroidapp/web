<?php

namespace Model\Commands;

use Doctrine\ORM\EntityManager;
use Model\LogRecord;

class DatabaseLogger implements ILogger
{

	private $records = [];

	/** @var \Doctrine\ORM\EntityManager */
	private $entityManager;

	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function start($message)
	{
		$this->addRecord($message, 'START', self::INFO);
	}

	public function end()
	{
		$this->addRecord('End', 'END', self::INFO);
		$this->entityManager->flush($this->records);
	}

	public function log($message, $severity = self::INFO)
	{
		$this->addRecord($message, '', $severity);
	}

	private function addRecord($message, $tag, $severity)
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
