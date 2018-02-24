<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 18.5.14
 * Time: 21:56
 */

namespace AdminModule;

use App\Components\Forms\EventFormFactory;
use Model\Event;

/** @User(loggedIn) */
class EventPresenter extends SecuredPresenter
{

	/** @var  \Kdyby\Doctrine\EntityManager @autowire */
	protected $em;

	public function actionEdit($id): void
	{
		$this['editForm']->bindEntity($this->em->find(Event::class, $id));
		$this['editForm']->onComplete[] = function ($entity): void {
			$this->em->flush($entity);
		};
	}

	public function actionAdd($id): void
	{
		$this['editForm']->bindEntity(new Event());
		$this['editForm']->onComplete[] = function ($entity): void {
			$this->em->persist($entity);
			$this->em->flush($entity);
		};
		$this['editForm']->setRedirect('Dashboard:');
	}

	public function actionDelete($id): void
	{
		$entity = $this->em->getReference(Event::class, $id);
		$this->em->remove($entity);
		$this->em->flush($entity);
		$this->redirect('Dashboard:');
	}

	public function createComponentEditForm(EventFormFactory $factory)
	{
		return $factory->create();
	}

}
