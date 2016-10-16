<?php

namespace Model\Commands;

use Kdyby\Doctrine\EntityDao;
use Model\LogRecord;

class DatabaseLogger implements ILogger
{

	private $logDao;

	private $records = [];

	function __construct(EntityDao $logDao)
	{
		$this->logDao = $logDao;
	}

	public function start($message)
	{
		$this->addRecord($message, "START", self::INFO);
	}

	public function end()
	{
		$this->addRecord('End', "END", self::INFO);
		$this->logDao->save($this->records);
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
		$this->records[] = $record;
	}
}
