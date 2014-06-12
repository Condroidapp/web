<?php

namespace Api3Module;


use FrontModule\BasePresenter;
use Model\Event;
use Nette\Application\BadRequestException;

class EventPresenter extends BasePresenter {

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

    public function actionDefault($id = NULL) {
        $condition = ['active'=>1];
        if($id) {
            $condition['id'] = $id;
        }
        $events = $this->eventRepository->findBy($condition);
        $this->apiLogger->logEventList();
        if(!count($events)) {
            throw new BadRequestException("Not found", 404);
        }

        $data = [];
        /** @var $event Event */
        foreach($events as $event) {
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
                'image' => NULL,
                'message' => $event->getMessage(),
            ];
        }

        $this->sendJson($data);
    }

} 