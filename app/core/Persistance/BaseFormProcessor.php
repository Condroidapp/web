<?php

namespace Model\Persistence;


use App\InvalidStateException;
use Model\BaseEntity;
use Model\Dao;

/**
 * Process values from EntityForm to provided entity
 *
 * @see \Maps\Component\Forms\EntityForm
 * @author Jan Langer <langeja1@fit.cvut.cz>
 */
class BaseFormProcessor extends \Nette\Object {

    /** @var \Maps\Model\Dao  */
    private $dao;

    /**
     * @param \Maps\Model\Dao $dao
     */
    public function __construct(\Maps\Model\Dao $dao) {
        $this->dao = $dao;
    }

    /**
     * Updates the entity with <pre>$values</pre> and persist it
     *
     * @param BaseEntity $entity
     * @param array $values
     * @return BaseEntity
     */
    public function update($entity, $values) {
        $this->setData($entity, $values);
        $this->dao->save($entity);
        return $entity;
    }

    /**
     * Updates entity values with provided data
     * Calls get* methods for all elements inside <pre>$values</pre> array
     *
     * @param BaseEntity $entity
     * @param array $values
     */
    protected function setData($entity, $values) {
        foreach ($values as $key => $value) {
            $method = "set" . ucfirst($key);
            $entity->$method($value);
        }
    }
    
    /**
     * @param string $entityName
     * @return Dao
     */
    protected function getEntityRepository($entityName) {
        return $this->dao->getEntityManager()->getRepository($entityName);
    }
    
    /**
     * @return Dao
     */
    public function getDao() {
        return $this->dao;
    }

    /**
     * @param \Nette\Http\FileUpload $file
     * @param string $dir destination directory
     * @param string $filename final file name without extension
     * @return null|string returns name of the file, or null when no file was uploaded
     * @throws \Maps\InvalidStateException when error occurs
     */
    protected function handleUpload(\Nette\Http\FileUpload $file, $dir, $filename) {
        if($file->isOk()) {
            $ext = pathinfo($file->getName(), PATHINFO_EXTENSION);
            $i=0;
            while(file_exists($dir.'/'.$filename.'-'.$i.'.'.$ext)) {
                $i++;
            }
            $path = $dir.'/'.$filename.'-'.$i.'.'.$ext;
            if($file->move($path)) {
                chmod($path, 0666);
                return basename($path);
            }
        } elseif($file->getError() != UPLOAD_ERR_NO_FILE) {
            throw new InvalidStateException("Unexpected error.");
        }
        return NULL;
    }

}

?>
