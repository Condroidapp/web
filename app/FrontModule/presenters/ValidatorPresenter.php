<?php declare(strict_types = 1);

namespace FrontModule;

use App\Components\Forms\BaseForm;
use Model\Commands\FeedParser;
use Nette\Forms\Form;
use Nette\Forms\Rendering\DefaultFormRenderer;
use Nette\Utils\ArrayHash;

class ValidatorPresenter extends BasePresenter
{

	/** @var \Model\Commands\FeedParser */
	private $parser;

	public function __construct(FeedParser $parser)
	{
		parent::__construct();
		$this->parser = $parser;
	}

	protected function createComponentValidatorForm(): BaseForm
	{
		$form = new BaseForm();
		$form->setRenderer(new DefaultFormRenderer());

		$form->addUpload('xml', 'XML')
			->setRequired('Vyberte prosím soubor.')
			->addRule(Form::MIME_TYPE, 'Vložte prosím soubor typu XML.', ['text/xml', 'application/xml', 'text/html']);

		$form->addSubmit('send', 'Odeslat');

		$form->onSuccess[] = function (BaseForm $form, ArrayHash $values): void {
			/** @var \Nette\Http\FileUpload $file */
			$file = $values->xml;
			if (!$file->isOk()) {
				return;
			}

			$errors = [];
			$this->parser->onError[] = function (string $message) use (&$errors): void {
				$errors[] = $message;
			};

			$this->parser->onLog[] = function (string $message) use (&$errors): void {
				$errors[] = $message;
			};
			$this->parser->onCriticalError[] = function (string $message) use (&$criticalErrors): void {
				$criticalErrors[] = $message;
			};

			try {
				$result = $this->parser->parseXmlFile($file->getTemporaryFile());
				$this->template->resultCount = count($result);
			} catch (\Throwable $e) {
				$criticalErrors[] = sprintf('Parsing failed witch error ' . get_class($e) . ' - ' . $e->getMessage());
			}

			if ($errors !== []) {
				$this->template->errors = $errors;
			}
			if ($criticalErrors !== []) {
				$this->template->criticalErrors = $criticalErrors;
			}
		};

		return $form;
	}

}
