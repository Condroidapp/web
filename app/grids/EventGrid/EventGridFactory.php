<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 18.5.14
 * Time: 20:28
 */

namespace App\Components\Grids;


use Kdyby\Doctrine\EntityManager;
use Model\Event;
use App\Datagrid;

class EventGridFactory
{
    /** @var \Kdyby\Doctrine\EntityManager */
    private $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function create()
    {
        $grid = new Datagrid();

        $grid->addColumn('id', '#ID');
        $grid->addColumn('name', 'Jméno');
        $grid->addColumn('active');
        $grid->addColumn('process');

        $grid->setDataSourceCallback(function ($filter, $sort) {
                $qb = $this->em->createQueryBuilder()
                    ->select('e')
                    ->from(Event::class, 'e')
                    ->orderBy('e.id', 'DESC');

                return $qb->getQuery()->execute();
        });

        $grid->setColumnGetterCallback(function ($row, $column) {
            return $row->$column;
        });

        $grid->setRowPrimaryKey('id');
        $grid->addCellsTemplate(__DIR__.'/eventGrid.latte');

        return $grid;
    }

} 