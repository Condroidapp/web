<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 25.8.13
 * Time: 16:53
 */

namespace Model\Commands;

class EchoLogger implements ILogger
{

	private function out(string $m): void
	{
		if ($m === PHP_EOL) {
			echo PHP_EOL;

			return;
		}
		echo '[' . date('Y-m-d H:i:s') . '] ' . $m . PHP_EOL;
	}

	public function start(string $message): void
	{
		$this->out(PHP_EOL);
		$this->out('****** STARTING ******');
		if ($message !== '') {
			$this->out($message);
		}
		$this->out('**********************');
	}

	public function end(): void
	{
		$this->out('****** END ***********');
		$this->out(PHP_EOL);
	}

	public function log(string $message, string $severity = self::INFO): void
	{
		$this->out($severity . ': ' . $message);
	}

}
