<?php
namespace ApiModule;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
use Model\BasicFetchByQuery;
use Model\Queries\AnnotationLastMod;
use Nette\Application\BadRequestException;
use Nette\Http\Response;

/**
 * Description of AnnotationsPresenter
 *
 * @author Honza
 */
class AnnotationsPresenter extends \FrontModule\BasePresenter
{

	/**
	 * @autowire
	 * @var \Nette\Http\Request
	 */
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

	public function actionDefault($cid)
	{
		if ($cid <= 0) {
			throw new BadRequestException('No cid supplied', 404);
		}
		$clientLastMod = new \DateTime($this->httpRequest->getHeader('If-Modified-Since', "Sun, 13 Mar 1988 17:00:00 +0100")); // :-)

		$clientLastMod->setTimezone(new \DateTimeZone(date_default_timezone_get()));

		/** @var $actualLastMod \DateTime */
		$actualLastMod = $this->annotationRepository->fetchOne(new AnnotationLastMod($cid))['timestamp'];
		$totalCount = count($this->annotationRepository->fetch(new BasicFetchByQuery(['event' => $cid, 'deleted' => false])));
		$clientCount = (int) $this->httpRequest->getHeader('X-If-Count-Not-Match', 0);

		$data = null;

		if ($totalCount === $clientCount && $actualLastMod <= $clientLastMod) {
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

		$this->httpResponse->setHeader('Last-Modified', $actualLastMod->format('D, j M Y H:i:s O'));

		if ($clientLastMod < $actualLastMod) {
			$data = $this->annotationRepository->fetch(new BasicFetchByQuery(['event' => $cid, 'deleted' => false, 'timestamp > ?' => ($clientLastMod)]));
			$this->apiLogger->logUpdate();
		} else {

			$data = $this->annotationRepository->fetch(new BasicFetchByQuery(['event' => $cid, 'deleted' => false]));
			$this->httpResponse->setHeader('X-Full-Update', 1);
			$this->apiLogger->logFullDownload();
		}

		$this->template->annotations = $data;
		$this->template->lastMod = $actualLastMod;
		$this->template->count = $totalCount;
	}

}


