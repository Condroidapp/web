<?php

namespace Model\Commands;

use App\InvalidProgramNodeException;
use Nette\Object;
use Nette\Utils\Strings;

class FeedParser extends Object
{

	public $onError = [];

	public $onLog = [];

	private $programIds = [];

	/** @var \GuzzleHttp\Client */
	private $httpClient;

	/** @var \Model\Commands\FileDownloader */
	private $downloader;

	public function __construct(FileDownloader $downloader)
	{
		$this->downloader = $downloader;
	}

	public function parseXML($feedUrl)
	{
		$this->programIds = [];
		$dom = new \DOMDocument();
		$file = $this->downloader->downloadFile($feedUrl);
		$return = $dom->load($file);
		if (!$return) {
			$error = error_get_last();
			$this->log('Feed data source load failed!', ILogger::ERROR);
			$this->onError('DOM Load Failed: ' . $error['message'], 1);

			return;
		}
		$this->log('Feed data source loaded');
		$this->log('Processing XML');
		$feedData = [];
		foreach ($dom->documentElement->childNodes as $node) {
			try {
				if ($node->nodeType == XML_ELEMENT_NODE) {
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

	private function parseProgramNode(\DOMNode $node)
	{
		$data = [];
		foreach ($node->childNodes as $n) {
			if ($n->nodeType == XML_ELEMENT_NODE) {
				$value = $this->sanitizeValue($n->nodeValue, $n->nodeName);
				if ($this->validateValue($n->nodeName, $value, $n)) {
					$data[$n->nodeName] = $value;
				}
			}
		}
		$this->validateNode($data);

		return $data;
	}

	private function validateValue($name, $value, \DOMNode $node)
	{
		switch ($name) {
			case 'pid':
				if (!ctype_digit($value)) {
					$this->onError(sprintf('Line %d - Expected PID value to be numeric, %s given.', $node->getLineNo(), $value), 101);
				}
				if (in_array($value, $this->programIds)) {
					$this->onError(sprintf('Line %d - Program ID is not unique throughout the document.', $node->getLineNo()), 102);
					throw new InvalidProgramNodeException();
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

	private function validateNode($data)
	{
		if ((isset($data['start-time']) && $data['start-time'] != '') && (isset($data['end-time']) && $data['end-time'] !== '')) {

		} else {
			$this->onError('PID ' . $data['pid'] . ' - When you set start or end time, the other one has to be set too.', 107);
		}
	}

	private function log($message, $severity = ILogger::INFO)
	{
		$this->onLog($message, $severity);
	}

	private function sanitizeValue($nodeValue, $name)
	{
		$value = Strings::trim($nodeValue);
		if ($value == '') {
			return null;
		}
		if (in_array($name, ['start-time', 'end-time'])) {
			$value = str_replace([' ', ';', ','], '', $value);
		}

		return $value;
	}

}
