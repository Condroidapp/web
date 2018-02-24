<?php declare(strict_types = 1);

namespace Model\Commands;

use App\InvalidProgramNodeException;
use Nette\SmartObject;
use Nette\Utils\Strings;

class FeedParser
{

	use SmartObject;

	/** @var \Closure[] */
	public $onError = [];

	/** @var \Closure[] */
	public $onCriticalError = [];

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
	public function downloadAndParseXml(string $feedUrl): array
	{
		$file = $this->downloader->downloadFile($feedUrl);
		$feedData = $this->parseXmlFile($file);

		@unlink($file);

		return $feedData;
	}

	/**
	 * @param string $fileName
	 * @return mixed[]
	 */
	public function parseXmlFile(string $fileName): array
	{
		$dom = new \DOMDocument();
		$return = $dom->load($fileName);
		if (!$return) {
			$error = error_get_last();
			$this->log('Feed data source load failed!', ILogger::ERROR);
			$this->onCriticalError('XML DOM Load Failed: ' . $error['message'], 1);

			return [];
		}
		$this->log('Processing XML');
		$feedData = [];
		$pids = [];
		if ($dom->documentElement->nodeName !== 'annotations') {
			$this->onCriticalError(sprintf('Unknown root element %s on line %d. Import aborted.', $dom->documentElement->nodeName, $dom->documentElement->getLineNo()));
			return [];
		}
		/** @var \DOMNode $node */
		foreach ($dom->documentElement->childNodes as $node) {
			try {
				if ($node->nodeType === XML_ELEMENT_NODE) {
					if ($node->nodeName !== 'programme') {
						$this->onCriticalError(sprintf('Unknown element %s on line %d. Ignored.', $node->nodeName, $node->getLineNo()));
						continue;
					}
					$programNode = $this->parseProgramNode($node, $pids);
					$feedData[] = $programNode;
					$pids[] = $programNode['pid'];
				}
			} catch (InvalidProgramNodeException $e) {
				//skip node
			}
		}
		$this->log('XML successfully processed, found ' . count($feedData) . ' entries.');

		return $feedData;
	}

	/**
	 * @param \DOMNode $node
	 * @param string[] $pids
	 * @return mixed[]
	 */
	private function parseProgramNode(\DOMNode $node, array $pids): array
	{
		$data = [];
		foreach ($node->childNodes as $n) {
			if ($n->nodeType !== XML_ELEMENT_NODE) {
				continue;
			}

			$value = $this->sanitizeValue($n->nodeValue, $n->nodeName);
			if (!$this->validateValue($n->nodeName, $value, $n, $pids)) {
				continue;
			}

			$data[$n->nodeName] = $value;
		}
		$this->validateNode($data, $node);

		return $data;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @param \DOMNode $node
	 * @param string[] $pids
	 * @return bool
	 */
	private function validateValue(string $name, $value, \DOMNode $node, array $pids): bool
	{
		switch ($name) {
			case 'pid':
				if ($value === '' || $value === null) {
					$this->onCriticalError(sprintf('Line %d - PID is required. Entry ignored.', $node->getLineNo()), 111);
					throw new InvalidProgramNodeException();
				}
				if (!ctype_digit($value)) {
					$this->onCriticalError(sprintf('Line %d - Expected PID value to be numeric, %s given.', $node->getLineNo(), $value), 101);
				}
				if (in_array($value, $pids, true)) {
					$this->onCriticalError(sprintf('Line %d - PID has to be unique throughout the source data. Program ID %s was repeated. Later entry is ignored.', $node->getLineNo(), $value), 112);
					throw new InvalidProgramNodeException();
				}
				break;
			case 'author':
				if ($value === '' || $value === null) {
					$this->onError(sprintf('Line %d - Author should filled.', $node->getLineNo()), 103);
				}
				break;
			case 'title':
				if ($value === '' || $value === null) {
					$this->onCriticalError(sprintf('Line %d - Title has to be filled. Entry ignored.', $node->getLineNo()), 104);
					throw new InvalidProgramNodeException();
				}
				break;
			case 'program-line':
				if ($value === '' || $value === null) {
					$this->onError(sprintf('Line %d - Program line should be filled.', $node->getLineNo()), 105);
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
				$this->onCriticalError('Unknown node ' . $name, 110);
		}

		return true;
	}

	/**
	 * @param mixed[] $data
	 * @param \DOMNode $node
	 */
	private function validateNode(array $data, \DOMNode $node): void
	{
		if (!isset($data['title'], $data['pid'])) {
			$this->onCriticalError(sprintf('Line %d - PID and title is required. Entry ignored.', $node->getLineNo()), 111);
			throw new InvalidProgramNodeException();
		}
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
