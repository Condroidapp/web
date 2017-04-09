<?php

namespace BS3FormRenderer\Latte\Macros;



use Latte\CompileException;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Nette\Forms\Controls\BaseControl;
use Nette\Utils\Html;

if (!class_exists('Latte\CompileException')) {
    class_alias('Nette\Latte\CompileException', 'Latte\CompileException');
    class_alias('Nette\Latte\Compiler', 'Latte\Compiler');
    class_alias('Nette\Latte\MacroNode', 'Latte\MacroNode');
    class_alias('Nette\Latte\Macros\MacroSet', 'Latte\Macros\MacroSet');
    class_alias('Nette\Latte\PhpWriter', 'Latte\PhpWriter');
}

abstract class BaseInputMacros extends MacroSet
{

	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);
		$me->addMacro('input', array($me, 'macroInput'));
		$me->addMacro('label', array($me, 'macroLabel'), array($me, 'macroLabelEnd'));
        $me->addMacro('pair', array($me, 'macroPair'));
		return $me;
	}


	/**
	 * {label ...}
	 */
	public function macroLabel(MacroNode $node, PhpWriter $writer)
	{
		$class = get_class($this);
		$words = $node->tokenizer->fetchWords();
		if (!$words) {
			throw new CompileException("Missing name in {{$node->name}}.");
		}
		$name = array_shift($words);
		return $writer->write(
			($name[0] === '$'
				? '$_input = is_object(%0.word) ? %0.word : $_form[%0.word];'
				: '$_input = $_form[%0.word];'
			) . 'if ($_label = $_input->getLabel(%1.raw)) echo ' . $class . '::label($_label->addAttributes(%node.array), $_input)',
			$name,
			($words ? 'NULL, ' : '') . implode(', ', array_map(array($writer, 'formatWord'), $words))
		);
	}

	/**
	 * {/label}
	 */
	public function macroLabelEnd(MacroNode $node, PhpWriter $writer)
	{
		if ($node->content != NULL) {
			$node->openingCode = substr_replace($node->openingCode, '->startTag()', strrpos($node->openingCode, ')') + 1, 0);
			return $writer->write('?></label><?php');
		}
	}


	/**
	 * {input ...}
	 */
	public function macroInput(MacroNode $node, PhpWriter $writer)
	{
		$class = get_class($this);
		$words = $node->tokenizer->fetchWords();
		if (!$words) {
			throw new CompileException("Missing name in {{$node->name}}.");
		}
		$name = array_shift($words);
		return $writer->write(
			($name[0] === '$'
				? '$_input = is_object(%0.word) ? %0.word : $_form[%0.word];'
				: '$_input = $_form[%0.word];'
			) . 'echo ' . $class . '::input($_input->getControl(%1.raw)->addAttributes(%node.array), $_input)',
			$name,
			implode(', ', array_map(array($writer, 'formatWord'), $words))
		);
	}


	public static function label(Html $label, BaseControl $control)
	{
		return $label;
	}


	public static function input(Html $input, BaseControl $control)
	{
		return $input;
	}

    public function macroPair(MacroNode $node, PhpWriter $writer) {
        $class = get_class($this);
        $words = $node->tokenizer->fetchWords();
        if (!$words) {
            throw new CompileException("Missing name in {{$node->name}}.");
        }
        $name = array_shift($words);

        return $writer->write(
                      ($name[0] === '$'
                          ? '$_input = is_object(%0.word) ? %0.word : $_form[%0.word];'
                          : '$_input = $_form[%0.word];'
                      ) . 'echo ' . $class . '::pair($_input->getControl(%1.raw)->addAttributes(%node.array), $_input)',
                          $name,
                          implode(', ', array_map(array($writer, 'formatWord'), $words))
        );
    }

}
