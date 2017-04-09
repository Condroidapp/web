<?php

namespace Api3Module;

use FrontModule\BasePresenter;
use Kdyby\Doctrine\EntityManager;
use Model\ApiLogger;
use Model\Event;

class EventPresenter extends BasePresenter
{

	/** @var \Model\ApiLogger */
	private $apiLogger;

	/** @var \Kdyby\Doctrine\EntityManager */
	private $entityManager;

	public function __construct(EntityManager $entityManager, ApiLogger $apiLogger)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->apiLogger = $apiLogger;
	}

	public function actionDefault($id = null)
	{
		$condition = ['active' => 1];
		if ($id) {
			$condition['id'] = $id;
		}
		$events = $this->entityManager->getRepository(Event::class)->findBy($condition);
		$this->apiLogger->logEventList();
		if (!count($events)) {
			$this->sendJson([]);
		}

		$data = [];
		/** @var $event Event */
		foreach ($events as $event) {
			$gps = $event->getGps();
			$data[] = [
				'id' => $event->id,
				'name' => $event->getName(),
				'timetable' => $event->isHasTimetable(),
				'date' => $event->getDate(),
				'url' => $event->getUrl(),
				'annotations' => $event->isHasAnnotations(),
				'datasource' => $this->link('//:Api3:Program:default', ['id' => $event->getId()]),
				'start' => $event->getCheckStart()->format('c'),
				'end' => $event->getCheckStop()->format('c'),
				'image' => null,
				'message' => $event->getMessage(),
				'places' => $id !== null ? $event->getPlaces()->toArray() : null,
				'gps' => $id !== null && count($gps) === 2 ? ['lat' => $event->getGps()[0], 'lon' => $event->getGps()[1]] : null,
			];
		}

		$this->sendJson($id ? array_shift($data) : $data);
	}

}
