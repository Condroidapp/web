<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 25.8.13
 * Time: 16:53
 */

namespace Model\Commands;

interface ILogger
{

	const INFO = 'INFO';
	const WARNING = 'WARNING';
	const ERROR = 'ERROR';

	public function start($message);

	public function end();

	public function log($message, $severity = self::INFO);

}
