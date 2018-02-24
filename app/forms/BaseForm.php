<?php declare(strict_types = 1);

namespace App\Components\Forms;

use BS3FormRenderer\Bs3FormRenderer;
use FrontModule\BasePresenter;
use Nette\Application\UI\Form as AppForm;
use Nette\Forms\Form as NForm;
use Nette\Forms\Rules;
use Nextras\Forms\Controls\DateTimePicker;

//setup of default rule messages

Rules::$defaultMessages += [
	NForm::PROTECTION => 'Došlo k chybě při odesílání formuláře. Zkuste to prosím znovu.',
	NForm::EQUAL => 'Prosím vložte %s.',
	NForm::FILLED => 'Vyplňte prosím pole %label.',
	NForm::MIN_LENGTH => 'Do pole %label vyplňte prosím alespoň %d znaků.',
	NForm::MAX_LENGTH => 'Prosím vyplňte nejvýše %d znaků do pole $label.',
	NForm::LENGTH => 'Do pole %label vyplňte minimálně %d a maximálně %d znaků.',
	NForm::EMAIL => 'Zkontrolujte prosím e-mailovou adresu v poli %label.',
	NForm::URL => 'Do pole %label zadejte prosím URL adresu ve správném formátu.',
	NForm::INTEGER => 'Do pole %label prosím zadejte celočíselnou hodnotu.',
	NForm::FLOAT => 'Do pole %label prosím zadejte číselnou hodnotu.',
	NForm::RANGE => 'Do pole %label prosím vyplňte číslo mezi %d a %d.',
	NForm::MAX_FILE_SIZE => 'Maximální velikost nahraného souboru může být %d bytů.',
	NForm::IMAGE => 'Nahraný soubor musí být obrázek ve formátu JPEG, GIF nebo PNG.',
];

/**
 * Base form for every form in app. Adds extended field type to forms
 *
 * @author Jan Langer <langeja1@fit.cvut.cz>
 * @package Maps\Components\Forms
 */
class BaseForm extends AppForm
{

	public function __construct()
	{
		parent::__construct();
		$this->addProtection();
		$this->setRenderer(new Bs3FormRenderer());
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param string $message adds form error as flash message
	 * @param bool $translate
	 */
	public function addError($message, $translate = true): void
	{
		if (trim($message) === '') {
			return;
		}

		$this->getPresenter()->flashMessage($message, BasePresenter::FLASH_ERROR);
	}

	/**
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param string $name
	 * @param string|null $caption
	 * @return \Nette\Forms\Controls\SubmitButton
	 */
	public function addSubmit($name, $caption = null)
	{
		$component = parent::addSubmit($name, $caption);
		$component->getControlPrototype()->addAttributes(['class' => 'btn-primary']);

		return $component;
	}

	public function addDatetime(string $name, ?string $caption = null): DateTimePicker
	{
		return $this[$name] = new DateTimePicker($caption);
	}

}

interface IBaseFormFactory
{

	public function create(): BaseForm;

}
