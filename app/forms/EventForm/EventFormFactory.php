<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 18.5.14
 * Time: 22:03
 */

namespace App\Components\Forms;


use Doctrine\ORM\EntityManager;

class EventFormFactory
{
    private $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function create()
    {
        $form = new EntityForm();

        $form->addText('name', 'Name');
        $form->addText('date', 'Date');
        $form->addTextArea('message', 'Message');
        $form->addCheckbox('active', 'Active');
        $form->addCheckbox('process', 'Process');

        $form->addDatetime('checkStart', 'Check start');
        $form->addDatetime('checkStop', 'Check stop');

        $form->addSubmit('ok', 'Send');

        return $form;
    }
} 