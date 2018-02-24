<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 25.8.13
 * Time: 16:53
 */

namespace Model\Commands;

interface ILogger
{

	public const INFO = 'INFO';
	public const WARNING = 'WARNING';
	public const ERROR = 'ERROR';

	public function start(string $message): void;

	public function end(): void;

	public function log(string $message, string $severity = self::INFO): void;

}
