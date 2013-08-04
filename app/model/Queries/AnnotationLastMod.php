<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 4.8.13
 * Time: 20:22
 */

namespace Model\Queries;

use Model\Annotation;
use Model\Persistence\IQueryable;
use Model\Persistence\QueryObjectBase;

class AnnotationLastMod extends QueryObjectBase {


    private $event;

    function __construct($event) {
        $this->event = $event;
    }


    /**
     * @param IQueryable $repository
     * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
     */
    protected function doCreateQuery(IQueryable $repository) {
        return $repository->createQuery("SELECT a.timestamp FROM ".Annotation::getClassName()." a WHERE a.event=:event ORDER by a.timestamp DESC")
                ->setParameter('event', $this->event)
                ->setMaxResults(1);
    }
}