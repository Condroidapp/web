<?php declare(strict_types = 1);

namespace Model\Commands;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class FileDownloader
{

	/** @var string */
	private $tmpDir;

	public function __construct(string $tmpDir)
	{
		$this->tmpDir = $tmpDir;
	}

	public function downloadFile(string $url): string
	{
		$client = new Client([
			RequestOptions::VERIFY => CaBundle::getBundledCaBundlePath(),
		]);

		$filename = $this->tmpDir . \DIRECTORY_SEPARATOR . md5($url);
		$resource = fopen($filename, 'wb');

		$client->request('GET', $url, [
			RequestOptions::SINK => $resource,
		]);

		return $filename;
	}

}
