<?php

namespace App;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Utils\Random;

class VersionMacro extends MacroSet
{

	public static function install(Compiler $compiler)
	{
		$me = new self($compiler);
		$me->addMacro('version', [$me, 'macroVersion']);

		return $me;
	}

	public function macroVersion(MacroNode $node, PhpWriter $writer)
	{
		$length = 10;
		$word = $node->tokenizer->fetchWord();
		if (is_numeric($word)) {
			$length = (int) $word;
		}

		return $writer->write(' ?>?' . Random::generate($length) . '<?php ');
	}

}
