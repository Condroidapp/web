<?php declare(strict_types = 1);

namespace App;

/**
 * Class InvalidArgumentException
 *
 * @package Maps
 * @author Jan Langer <langeja1@fit.cvut.cz>
 */
class InvalidArgumentException extends \InvalidArgumentException
{

}

/**
 * Class InvalidStateException
 *
 * @package Maps
 * @author Jan Langer <langeja1@fit.cvut.cz>
 */
class InvalidStateException extends \RuntimeException
{

}

/**
 * Class ShellCommandException
 *
 * @package Maps
 * @author Jan Langer <langeja1@fit.cvut.cz>
 */
class ShellCommandException extends \RuntimeException
{

}

/**
 * Class StaticClassException
 *
 * @package Maps
 * @author Jan Langer <langeja1@fit.cvut.cz>
 */
class StaticClassException extends \LogicException
{

}

class InvalidProgramNodeException extends InvalidArgumentException
{

}
