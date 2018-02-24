<?php declare(strict_types = 1);

namespace App\Components\Forms;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Kdyby\Doctrine\Entities\IdentifiedEntity;
use Model\BaseEntity;
use Nette\Application\UI\Form;

/**
 * Description of EntityForm
 *
 * @author Jan -Quinix- Langer
 */
class EntityForm extends BaseForm
{

	/** @var \Model\BaseEntity */
	private $entity;

	/** @var string */
	private $successFlashMessage = 'Data byla úspěšně uložena.';

	/** @var mixed[] */
	private $redirect;

	/** @var \Closure[]|callable */
	public $onBind = [];

	/** @var \Closure[]|callable */
	public $onHandle = [];

	/** @var \Closure[]|callable */
	public $onComplete = [];

	public function __construct()
	{
		parent::__construct();
		$this->onSuccess[] = function (Form $form): void {
			$this->handler($form);
		};
	}

	public function bindEntity(BaseEntity $entity): void
	{
		$this->entity = $entity;

		foreach ($this->getComponents() as $name => $input) {
			$value = null;
			if ($this->hasProperty($entity, $name)) {
				$value = $entity->$name;
			} elseif (substr_count($name, '__') + 1 > 1) {
				$parts = explode('__', $name);
				if ($this->hasProperty($entity, $parts[0])) {
					$call = $parts[0];
					$object = $entity->$call();
					if ($this->hasProperty($object, $parts[1])) {
						$value = $object->{$parts[1]};
					}
				}
			} else {
				continue;
			}

			if ($value instanceof BaseEntity || $value instanceof IdentifiedEntity) {
				$value = $value->getId();
			} elseif ($value instanceof ArrayCollection || $value instanceof PersistentCollection) {
				$value = array_map(function ($entity) {
					return $entity->getId();
				}, $value->toArray());
			}

			$input->setDefaultValue($value);
		}
		if (!$this->onBind) {
			return;
		}

		$this->onBind($entity);
	}

	public function getEntity(): BaseEntity
	{
		return $this->entity;
	}

	public function handler(Form $form): void
	{
		if (!$form->isValid()) {
			return;
		}
		try {
			$values = $form->getValues();
			$this->onHandle($this->getEntity(), $values);
			$this->processForm($this->getEntity(), $values);

			/** @var \Nette\Application\UI\Presenter $presenter */
			$presenter = $this->getPresenter();
			if ($this->successFlashMessage) {
				$presenter->flashMessage($this->successFlashMessage, 'success');
			}
			$this->onComplete($this->entity);
			if ($this->redirect) {
				call_user_func_array([$presenter, 'redirect'], $this->redirect);
			}
		} catch (\InvalidArgumentException $e) {
			$this->addError($e->getMessage());
		}
	}

	public function setSuccessFlashMessage(string $successFlashMessage): void
	{
		$this->successFlashMessage = $successFlashMessage;
	}

	public function setRedirect(): void
	{
		$this->redirect = func_get_args();
	}

	/**
	 * @param \Model\BaseEntity $entity
	 * @param iterable $values
	 */
	public function processForm(BaseEntity $entity, iterable $values): void
	{
		foreach ($values as $key => $value) {
			$method = 'set' . ucfirst($key);
			$entity->$method($value);
		}
	}

	private function hasProperty(BaseEntity $entity, string $name): bool
	{
		return isset($entity->$name);
	}

}
