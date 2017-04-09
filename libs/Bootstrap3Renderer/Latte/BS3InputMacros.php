<?php

namespace BS3FormRenderer\Latte\Macros;

use Nette\Forms\Controls;
use Nette\Utils\Html;
use Nette\Forms\Controls\BaseControl;
use Nextras;


class BS3InputMacros extends BaseInputMacros
{
    /**
     * @param BaseControl $control
     * @return Nextras\Forms\Rendering\Bs3FormRenderer
     */
    public static function getRenderer(BaseControl $control)
    {
        return $control->getForm()->getRenderer();
    }

	public static function label(Html $label, BaseControl $control)
	{
        return $label;
	}


	public static function input(Html $input, BaseControl $control)
	{
		$name = $input->getName();
		if ($name === 'select' || $name === 'textarea' || ($name === 'input' && !in_array($input->type, array('radio', 'checkbox', 'hidden', 'range', 'image', 'submit', 'reset')))) {
			$input->addClass('form-control');

		} elseif ($name === 'input' && ($input->type === 'submit' || $input->type === 'reset')) {
			$input->setName('button');
			$input->add($input->value);
			$input->addClass('btn');
		}

		return $input;
	}

    public static function pair(Html $input, BaseControl $control)
    {

        $label = $control->getLabel();
        $inp = $control->getControl();


        if ($control instanceof Controls\Checkbox || $control instanceof Controls\CheckboxList || $control instanceof Controls\RadioList) {
            $control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
            $inp = $control->getControl();
        }
        $renderer = self::getRenderer($control);

        $html = Html::el($renderer->wrappers['pair']['container']);
        $controlContainer = Html::el($renderer->wrappers['control']['container'])->add(self::input($inp, $control));
        if($label) {
            $html->add(Html::el($renderer->wrappers['label']['container'], ['class' => ($control->isRequired()?'required':'')])
                ->add(self::label($label, $control)));
        } else {
            $controlContainer->addClass('col-sm-offset-2');
        }
        $html->add($controlContainer);

        return $html;
    }

}
