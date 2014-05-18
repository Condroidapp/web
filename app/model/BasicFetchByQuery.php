<?php
namespace Model;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;
use Nette\Utils\Strings;

/**
 * Basic fetch by params query
 *
 * @package Maps\Model
 * @author Jan Langer <langeja1@fit.cvut.cz>
 */
class BasicFetchByQuery extends QueryObject {
    /** @var array conditions */
    private $conditions = [];

    /**
     * @param $conditions
     */
    function __construct($conditions) {
        $this->conditions = $conditions;
    }

    /** {@inheritdoc} */
    protected function doCreateQuery(Queryable $repository) {
        
        $where = [];
        $params = [];
        $i = 0;
        foreach($this->conditions as $condition=>$value) {
            $separator = '= ?';
            if(Strings::endsWith($condition, '?')) {
                $separator = '';
            }
            $where[] = "b.".$condition.$separator.$i++;
            $params[] = $value;
        }
        $q = $repository->createQuery("SELECT b FROM ".$repository->getClassName()." b ".(!empty($where) ? " WHERE ".  implode(" AND ", $where) : ""))
                ->setParameters($params);
        return $q;
    }    //put your code here
}

?>
