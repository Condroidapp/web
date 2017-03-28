<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 5.8.13
 * Time: 22:32
 */

namespace Model\Commands;

use App\Tools\Helpers;
use Doctrine\ORM\EntityManager;
use Model\Annotation;
use Model\Event;
use Model\ProgramLine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;

class ImportCommand extends Command
{

	/** @var \Model\Commands\FeedParser */
	private $feedParser;

	/** @var \Model\Commands\ILogger */
	private $logger;

	/** @var \Doctrine\ORM\EntityManager */
	private $entityManager;

	public function __construct(EntityManager $entityManager, ILogger $logger, FeedParser $parser)
	{
		parent::__construct();

		$this->feedParser = $parser;
		$this->logger = $logger;
		$this->feedParser->onError[] = function ($message, $code) {
			$this->logger->log("#$code: $message", ILogger::ERROR);
		};
		$this->feedParser->onLog[] = function ($message, $severity) {
			$this->logger->log($message, $severity);
		};
		$this->entityManager = $entityManager;
	}

	/** {@inheritdoc} */
	protected function configure()
	{
		$this->setName('import:parse')
			->setDescription('Downloads and parses all active data feeds.');
	}

	/** {@inheritdoc} */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$events = $this->entityManager->createQueryBuilder()
			->select('e')
			->from(Event::class, 'e')
			->andWhere('e.process = :process')
			->andWhere('e.checkStart < :start')
			->andWhere('e.checkStop > :stop')
			->setParameters([
				'process' => true,
				'start' => new \DateTime(),
				'stop' => new \DateTime(),
			])
			->getQuery()
			->getResult();

		/** @var $event Event */
		foreach ($events as $event) {
			$this->logger->start($event->getName());
			$data = $this->feedParser->parseXML($event->getDataUrl());
			$this->importData($event, $data);
			$this->logger->end();
		}

	}

	private function importData(Event $event, array $data)
	{
		try {
			$annotations = $event->getAnnotations();
			$data = Helpers::mapAssoc($data, 'pid');

			$additions = 0;
			$changes = 0;
			$deletions = 0;
			$totalCount = count($data);

			$programLinesMap = $this->processProgramLines($data, $event);

			/** @var $annotation Annotation */
			foreach ($annotations as $id => $annotation) {
				if ($annotation->getDeleted() && !isset($data[$annotation->pid])) {
					continue;
				}
				if (!isset($data[$annotation->pid])) {
					$annotation->setDeleted(true);
					$annotation->setDeletedAt(new \DateTime());
					$deletions++;
					continue;
				}
				$newData = $data[$annotation->pid];
				if ($this->isAnnotationChanged($newData, $annotation)) {
					$changes++;
					$this->hydrateAnnotation($newData, $annotation, $programLinesMap);
				}

				unset($data[$annotation->pid]);
			}

			if ($data !== []) {
				foreach ($data as $newData) {
					$additions++;
					$annotation = new Annotation();
					$annotation->setEvent($event);
					$this->hydrateAnnotation($newData, $annotation, $programLinesMap);
					$this->entityManager->persist($annotation);
				}
			}
			$this->logger->log("Found total of $totalCount records, discovered $additions new, $changes changed, " .
				"$deletions deleted, " . ($totalCount - $additions - $deletions - $changes) . ' unchanged.');
			$this->entityManager->flush();

			$this->logger->log('Successfully processed the feed.');
		} catch (\Exception $e) {
			Debugger::log($e);
			$this->logger->log('Error during data processing. ' . $e->getMessage());
		}

	}

	/**
	 * @param array $data
	 * @param \Model\Event $event
	 * @return array
	 */
	private function processProgramLines(array $data, Event $event)
	{
		$programLines = array_column($data, 'program-line');
		$programLines = array_unique($programLines);

		$lines = $event->getProgramLines();
		$programLines = array_flip($programLines);
		$map = [];

		/** @var $line ProgramLine */
		foreach ($lines as $line) {
			if (isset($programLines[$line->getTitle()])) {
				unset($programLines[$line->getTitle()]);
			}
			$map[$line->title] = $line;
		}

		if ($programLines !== []) {
			foreach ($programLines as $name => $foo) {
				$entity = new ProgramLine($name, $event);
				$map[$name] = $entity;
			}
		}

		return $map;
	}

	/**
	 * @param $newData
	 * @param $annotation
	 * @param $programLinesMap
	 */
	private function hydrateAnnotation(array $newData, Annotation $annotation, array $programLinesMap)
	{
		$annotation->setPid($newData['pid']);
		if (isset($newData['author']) && $newData['author']) {
			$annotation->setAuthor($newData['author']);
		}
		$annotation->setTitle($newData['title']);
		$annotation->setAnnotation($newData['annotation']);
		if (isset($newData['start-time']) && $newData['start-time']) {
			$annotation->setStartTime(new \DateTime($newData['start-time']));
		}
		if (isset($newData['end-time']) && $newData['end-time']) {
			$annotation->setEndTime(new \DateTime($newData['end-time']));
		}
		if (isset($newData['location']) && $newData['location']) {
			$annotation->setLocation($newData['location']);
		}
		$annotation->setType(isset($newData['type']) ? $newData['type'] : 'P');
		$annotation->setProgramLine($programLinesMap[$newData['program-line']]);
		$annotation->setDeleted(false);
		$annotation->setDeletedAt(null);
	}

	private function isAnnotationChanged($newData, $annotation)
	{

		foreach ($newData as $key => $item) {
			if ($key === 'pid') {
				continue;
			}
			if ($key === 'start-time') {
				$key = 'startTime';
			}
			if ($key === 'end-time') {
				$key = 'endTime';
			}
			if ($key === 'program-line') {
				$key = 'programLine';
			}
			if (!isset($annotation->$key)) {
				continue;
			}
			$previousValue = $annotation->$key;
			if ($previousValue instanceof Event) {
				continue;
			}
			if ($previousValue instanceof ProgramLine) {
				$previousValue = $previousValue->title;
			}
			if ($previousValue instanceof \DateTime) {
				$item = (new \DateTime($item))->format('c');
				$previousValue = $previousValue->format('c');
			}
			if ($item !== $previousValue) {
				return true;
			}
		}

		return false;
	}

}
