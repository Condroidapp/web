<?php declare(strict_types = 1);

namespace Model\Commands;

use App\InvalidProgramNodeException;
use Nette\Utils\Strings;

class FeedParser
{

	/** @var \Closure[] */
	public $onError = [];

	/** @var \Closure[] */
	public $onLog = [];

	/** @var \Model\Commands\FileDownloader */
	private $downloader;

	public function __construct(FileDownloader $downloader)
	{
		$this->downloader = $downloader;
	}

	/**
	 * @param string $feedUrl
	 * @return mixed[]
	 */
	public function parseXML(string $feedUrl): array
	{
		$dom = new \DOMDocument();
		$file = $this->downloader->downloadFile($feedUrl);
		$return = $dom->load($file);
		if (!$return) {
			$error = error_get_last();
			$this->log('Feed data source load failed!', ILogger::ERROR);
			$this->onError('DOM Load Failed: ' . $error['message'], 1);

			return [];
		}
		$this->log('Feed data source loaded');
		$this->log('Processing XML');
		$feedData = [];
		foreach ($dom->documentElement->childNodes as $node) {
			try {
				if ($node->nodeType === XML_ELEMENT_NODE) {
					$feedData[] = $this->parseProgramNode($node);
				}
			} catch (InvalidProgramNodeException $e) {
				//skip node
			}
		}
		$this->log('XML sucesfully processed, found ' . count($feedData) . ' entries.');

		@unlink($file);

		return $feedData;
	}

	/**
	 * @param \DOMNode $node
	 * @return mixed[]
	 */
	private function parseProgramNode(\DOMNode $node): array
	{
		$data = [];
		foreach ($node->childNodes as $n) {
			if ($n->nodeType !== XML_ELEMENT_NODE) {
				continue;
			}

			$value = $this->sanitizeValue($n->nodeValue, $n->nodeName);
			if (!$this->validateValue($n->nodeName, $value, $n)) {
				continue;
			}

			$data[$n->nodeName] = $value;
		}
		$this->validateNode($data);

		return $data;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @param \DOMNode $node
	 * @return bool
	 */
	private function validateValue(string $name, $value, \DOMNode $node): bool
	{
		switch ($name) {
			case 'pid':
				if (!ctype_digit($value)) {
					$this->onError(sprintf('Line %d - Expected PID value to be numeric, %s given.', $node->getLineNo(), $value), 101);
				}
				break;
			case 'author':
				if ($value === '' || $value === null) {
					$this->onError(sprintf('Line %d - Author has to be filled.', $node->getLineNo()), 103);
				}
				break;
			case 'title':
				if ($value === '' || $value === null) {
					$this->onError(sprintf('Line %d - Title has to be filled.', $node->getLineNo()), 104);
					throw new InvalidProgramNodeException();
				}
				break;
			case 'program-line':
				if ($value === '' || $value === null) {
					$this->onError(sprintf('Line %d - Program line has to be filled.', $node->getLineNo()), 105);
				}
				break;
			case 'start-time':
			case 'end-time':
				if ($value !== null && !strtotime($value)) {
					$this->onError(sprintf('Line %d - Invalid datetime value %s', $node->getLineNo(), $value), 106);
				}
				break;
			case 'type':
			case 'length':
			case 'location':
			case 'annotation':
				break;
			default:
				$this->onError('Unknown node ' . $name, 110);
		}

		return true;
	}

	/**
	 * @param mixed[] $data
	 */
	private function validateNode(array $data): void
	{
		if (!(isset($data['start-time']) || isset($data['end-time'])) || ($data['start-time'] !== null || $data['end-time'] !== null)) {
			return;
		}

		$this->onError('PID ' . $data['pid'] . ' - When you set start or end time, the other one has to be set too.', 107);
	}

	private function log(string $message, string $severity = ILogger::INFO): void
	{
		$this->onLog($message, $severity);
	}

	private function sanitizeValue(string $nodeValue, string $name): ?string
	{
		$value = Strings::trim($nodeValue);
		if ($value === '') {
			return null;
		}
		if (in_array($name, ['start-time', 'end-time'])) {
			$value = str_replace([' ', ';', ','], '', $value);
		}

		return $value;
	}

}
