<?php


namespace Model\Persistence;

use Doctrine;
use Doctrine\ORM\AbstractQuery;
use Nette;
use Nette\Utils\Paginator;


/**
 * Base class for all queries in application
 *
 * @author Filip Procházka <filip@prochazka.su>
 */
abstract class QueryObjectBase extends Nette\Object implements IQueryObject {

    /**
     * @var \Doctrine\ORM\Query
     */
    private $lastQuery;

    /**
     * @var ResultSet
     */
    private $lastResult;


    /**
     */
    public function __construct() {

    }


    /**
     * @param IQueryable $repository
     * @return \Doctrine\ORM\Query|\Doctrine\ORM\QueryBuilder
     */
    protected abstract function doCreateQuery(IQueryable $repository);


    /**
     * @param IQueryable $repository
     *
     * @throws \Maps\UnexpectedValueException
     * @return \Doctrine\ORM\Query
     */
    public function getQuery(IQueryable $repository) {
        $query = $this->doCreateQuery($repository);
        if ($query instanceof Doctrine\ORM\QueryBuilder) {
            $query = $query->getQuery();
        }

        if (!$query instanceof Doctrine\ORM\Query) {
            $class = $this->getReflection()->getMethod('doCreateQuery')->getDeclaringClass();
            throw new \Maps\UnexpectedValueException("Method " . $class . "::doCreateQuery() must return" .
                    " instanceof Doctrine\\ORM\\Query or instanceof Doctrine\\ORM\\QueryBuilder, " .
                    \Maps\Tools\Mixed::getType($query) . " given.");
        }

        if ($this->lastQuery && $this->lastQuery->getDQL() === $query->getDQL()) {
            $query = $this->lastQuery;
        }

        if ($this->lastQuery !== $query) {
            $this->lastResult = new ResultSet($query);
        }

        return $this->lastQuery = $query;
    }


    /**
     * @param IQueryable $repository
     *
     * @return integer
     */
    public function count(IQueryable $repository) {
        return $this->fetch($repository)
                ->getTotalCount();
    }


    /**
     * @param IQueryable $repository
     * @param int $hydrationMode
     *
     * @return ResultSet|array
     */
    public function fetch(IQueryable $repository, $hydrationMode = AbstractQuery::HYDRATE_OBJECT) {
        $query = $this->getQuery($repository)
                ->setFirstResult(NULL)
                ->setMaxResults(NULL);
        if ($hydrationMode != AbstractQuery::HYDRATE_OBJECT) {
            $query->setHydrationMode($hydrationMode);
        }
        return $hydrationMode !== AbstractQuery::HYDRATE_OBJECT
                ? $query->execute()
                : $this->lastResult;
    }


    /**
     * @param IQueryable $repository
     * @return object
     */
    public function fetchOne(IQueryable $repository) {
        $query = $this->getQuery($repository)
                ->setFirstResult(NULL)
                ->setMaxResults(1);

        return $query->getSingleResult();
    }


    /**
     * @internal For Debugging purposes only!
     * @return \Doctrine\ORM\Query
     */
    public function getLastQuery() {
        return $this->lastQuery;
    }

    /**
     * @param IQueryable $repository
     * @return Doctrine\ORM\QueryBuilder
     * @throws \Maps\UnexpectedValueException
     */
    public function getQueryBuilder(IQueryable $repository) {
        $query = $this->doCreateQuery($repository);
        if ($query instanceof Doctrine\ORM\QueryBuilder) {
            return $query;
        }

        throw new \Maps\UnexpectedValueException("Query returned " . \Maps\Tools\Mixed::getType($query) . " but Doctrine\\ORM\\QueryBuilder expected.");
    }

}