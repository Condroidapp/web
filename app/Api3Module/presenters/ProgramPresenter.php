<?php

namespace Api3Module;


use FrontModule\BasePresenter;
use Model\Annotation;
use Model\BasicFetchByQuery;
use Model\Queries\AnnotationLastMod;
use Nette\Application\BadRequestException;
use Nette\Http\Response;

class ProgramPresenter extends BasePresenter {

    /**
     * @autowire
     * @var \Nette\Http\Request */
    protected $httpRequest;
    /**
     * @autowire
     * @var \Nette\Http\Response
     */
    protected $httpResponse;

    /**
     * @autowire(\Model\Annotation, factory=\Kdyby\Doctrine\EntityDaoFactory)
     * @var \Kdyby\Doctrine\EntityDao
     */
    protected $annotationRepository;
    /**
     * @autowire
     * @var \Model\ApiLogger
     */
    protected $apiLogger;

    public function actionDefault($id) {
        if($id <= 0) {
            throw new BadRequestException('No id supplied',404);
        }

        $clientLastMod = new \DateTime($this->httpRequest->getHeader('If-Modified-Since', "Sun, 13 Mar 1988 17:00:00 +0100")); // :-)

        /** @var $actualLastMod \DateTime */
        $actualLastMod = $this->annotationRepository->fetchOne(new AnnotationLastMod($id))['timestamp'];

        $data = NULL;

        if($actualLastMod <= $clientLastMod) {
            $this->httpResponse->setCode(Response::S304_NOT_MODIFIED);
            $this->apiLogger->logCheck();
            $this->terminate();
        }
        else {
            $this->httpResponse->setCode(Response::S200_OK);
            if($this->httpRequest->isMethod('HEAD')) {
                $this->apiLogger->logCheck();
                $this->terminate();
            }
        }

        $this->httpResponse->setHeader('Last-Modified', $actualLastMod->format('D, j M Y H:i:s O'));
        $query = [];
        if ($clientLastMod < $actualLastMod) {
            $query = ['event' => $id, 'timestamp > ?'=> $clientLastMod];
            $this->apiLogger->logUpdate();
        }
        else {
            $query = ['event' => $id];
            $this->apiLogger->logFullDownload();
        }

        $data = [
            'add' => $this->getJsonData($this->annotationRepository->fetch(new BasicFetchByQuery(array_merge($query, ['createdAt > ?' => $clientLastMod])))),
            'change' => $this->getJsonData($this->annotationRepository->fetch(new BasicFetchByQuery(array_merge($query, ['createdAt <= ?' => $clientLastMod])))),
            'delete' => $this->getJsonData($this->annotationRepository->fetch(new BasicFetchByQuery(array_merge($query, ['deleted' => TRUE, 'deletedAt > ?' => $clientLastMod])))),
        ];
        $this->sendJson($data);
    }

    private function getJsonData($annotations) {
        $data = [];

        /** @var $annotation Annotation */
        foreach($annotations as $annotation) {
            $data[] = [
                'pid' => $annotation->pid,
                'author' => $annotation->author,
                'title' => $annotation->title,
                'imdb' => $annotation->imdb,
                'type' => $annotation->type,
                'location' => $annotation->location,
                'programLine' => $annotation->programLine ? $annotation->programLine->title : NULL,
                'start' => $annotation->startTime ? $annotation->startTime->format('c') : NULL,
                'end' => $annotation->endTime ? $annotation->endTime->format('c') : NULL,
                'annotation' => $annotation->annotation,
            ];
        }
        return $data;
    }

} 