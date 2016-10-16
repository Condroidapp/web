<?php

namespace Api3Module;

use FrontModule\BasePresenter;
use Model\Event;
use Nette\Application\BadRequestException;

class EventPresenter extends BasePresenter
{

	/**
	 * @autowire(\Model\Event, factory=\Kdyby\Doctrine\EntityDaoFactory)
	 * @var \Kdyby\Doctrine\EntityDao
	 */
	protected $eventRepository;

	/**
	 * @autowire
	 * @var \Model\ApiLogger
	 */
	protected $apiLogger;

	public function actionDefault($id = null)
	{
		$condition = ['active' => 1];
		if ($id) {
			$condition['id'] = $id;
		}
		$events = $this->eventRepository->findBy($condition);
		$this->apiLogger->logEventList();
		if (!count($events)) {
			throw new BadRequestException("Not found", 404);
		}

		$data = [];
		/** @var $event Event */
		foreach ($events as $event) {
			$data[] = [
				'id' => $event->id,
				'name' => $event->getName(),
				'timetable' => $event->getHasTimetable(),
				'date' => $event->date,
				'url' => $event->url,
				'annotations' => $event->getHasAnnotations(),
				'datasource' => $this->link('//:Api3:Program:default', ['id' => $event->getId()]),
				'start' => $event->getCheckStart()->format('c'),
				'end' => $event->getCheckStop()->format('c'),
				'image' => null,
				'message' => $event->getMessage(),
				'places' => ($id ? $this->getPlaces($event) : null),
				'gps' => ($id && $event->gps ? ['lat' => $event->gps[0], 'lon' => $event->gps[1]] : null),
			];
		}

		$this->sendJson($id ? array_shift($data) : $data);
	}

	private function getPlaces($event)
	{
		return $event->places;
	}

}
