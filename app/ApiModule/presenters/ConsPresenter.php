<?php
namespace ApiModule;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
use Model\Dao;
use Model\Persistence\IDao;

/**
 * Description of ConsPresnter
 *
 * @author Honza
 */
class ConsPresenter extends \FrontModule\BasePresenter
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

	public function renderDefault()
	{
		$this->template->cons = $this->eventRepository->findBy(['active' => 1]);
		$this->apiLogger->logEventList();

	}

}

?>
