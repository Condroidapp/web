<?php

namespace Api3Module;

use FrontModule\BasePresenter;
use Kdyby\Doctrine\EntityManager;
use Model\Annotation;
use Model\ApiLogger;
use Model\BasicFetchByQuery;
use Model\Queries\AnnotationLastMod;
use Nette\Application\BadRequestException;
use Nette\Http\Request;
use Nette\Http\Response;

class ProgramPresenter extends BasePresenter
{

	/** @var \Nette\Http\Request */
	private $httpRequest;

	/** @var \Nette\Http\Response */
	private $httpResponse;

	/** @var \Model\ApiLogger */
	private $apiLogger;

	/** @var \Kdyby\Doctrine\EntityManager */
	private $entityManager;

	public function __construct(Request $httpRequest, Response $httpResponse, ApiLogger $apiLogger, EntityManager $entityManager)
	{
		parent::__construct();
		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;
		$this->apiLogger = $apiLogger;
		$this->entityManager = $entityManager;
	}

	public function actionDefault($id)
	{
		if ($id <= 0) {
			throw new BadRequestException('No id supplied', 404);
		}

		$clientLastMod = new \DateTime($this->httpRequest->getHeader('If-Modified-Since', 'Sun, 13 Mar 1988 17:00:00 +0100')); // :-)

		/** @var $actualLastMod \DateTime */
		$actualLastMod = $this->entityManager->getRepository(Annotation::class)->fetchOne(new AnnotationLastMod($id))['timestamp'];

		$data = null;

		if ($actualLastMod <= $clientLastMod) {
			$this->httpResponse->setCode(Response::S304_NOT_MODIFIED);
			$this->apiLogger->logCheck();
			$this->terminate();
		} else {
			$this->httpResponse->setCode(Response::S200_OK);
			if ($this->httpRequest->isMethod('HEAD')) {
				$this->apiLogger->logCheck();
				$this->terminate();
			}
		}

		$actualClientLastMod = $this->httpRequest->getHeader('If-Modified-Since', null);
		$this->httpResponse->setHeader('Last-Modified', $actualLastMod->format('D, j M Y H:i:s O'));
		if ($actualClientLastMod !== null && $clientLastMod < $actualLastMod) {
			$query = ['event' => $id, 'timestamp > ?' => $clientLastMod];
			$this->apiLogger->logUpdate();
		} else {
			$query = ['event' => $id];
			$this->apiLogger->logFullDownload();
		}

		$annotationRepository = $this->entityManager->getRepository(Annotation::class);
		$data = [
			'add' => $this->getJsonData($annotationRepository->fetch(new BasicFetchByQuery(array_merge($query, ['createdAt > ?' => $clientLastMod])))),
			'change' => $this->getJsonData($annotationRepository->fetch(new BasicFetchByQuery(array_merge($query, ['createdAt <= ?' => $clientLastMod])))),
			'delete' => $this->getJsonData($annotationRepository->fetch(new BasicFetchByQuery(array_merge($query, ['deleted' => true, 'deletedAt > ?' => $clientLastMod])))),
		];
		$this->sendJson($data);
	}

	/**
	 * @param Annotation[]|\Traversable $annotations
	 * @return array
	 */
	private function getJsonData(\Traversable $annotations)
	{
		$data = [];

		foreach ($annotations as $annotation) {
			$startTime = $annotation->getStartTime();
			$endTime = $annotation->getEndTime();

			if ($startTime === null || $endTime === null) {
				$startTime = null;
				$endTime = null;
			}

			if ($endTime !== null && $endTime < $startTime) {
				$endTime = $endTime->modify('+1 day');
			}
			$data[] = [
				'pid' => (int) $annotation->getPid(),
				'author' => $annotation->getAuthor(),
				'title' => $annotation->getTitle(),
				'imdb' => $annotation->getImdb(),
				'type' => $annotation->getType(),
				'location' => $annotation->getLocation(),
				'programLine' => $annotation->getProgramLine() ? $annotation->getProgramLine()->getTitle() : null,
				'start' => $startTime !== null ? $startTime->format('c') : null,
				'end' => $endTime !== null ? $endTime->format('c') : null,
				'annotation' => $annotation->getAnnotation(),
			];
		}

		return $data;
	}

}
