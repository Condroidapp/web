<?php

namespace Model\Commands;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class FileDownloader
{

	/** @var string */
	private $tmpDir;

	public function __construct($tmpDir)
	{
		$this->tmpDir = $tmpDir;
	}

	public function downloadFile($url)
	{
		$client = new Client([
			RequestOptions::VERIFY => CaBundle::getBundledCaBundlePath(),
		]);

		$filename = $this->tmpDir . DIRECTORY_SEPARATOR . md5($url);
		$resource = fopen($filename, 'w+');

		$client->get($url, [
			RequestOptions::SINK => $resource,
		]);

		return $filename;
	}

}
