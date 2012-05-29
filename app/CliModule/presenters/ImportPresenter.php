<?php

namespace CliModule;

/**
 * Description of ImportPresenter
 *
 * @author Honza
 */
class ImportPresenter extends CliPresenter {
    
    const TAG = 'Import';

    public function actionDefault() {
        foreach ($this->getContext()->database->table('cons')->select('id,dataUrl')->where('active = 1')->where("checkStart<NOW()")->where('checkStop>NOW()') as $con) {
            try {
                $this->fetch($con->id, $con->dataUrl);
            } catch (\Exception $e) {
                throw $e;
                $this->getContext()->logger->log(self::TAG, \Logger::LOG_ERROR, ' #'.$con->id.' exception during import of - '.$e->getMessage());
            }
        }
        $this->getContext()->logger->flush();
        $this->terminate();
    }

    private function fetch($cid, $url) {
        
        $dom = new \DOMDocument();
        \Nette\Diagnostics\Debugger::tryError();
        $dom->load($url); //fetch document
        if (\Nette\Diagnostics\Debugger::catchError($msg)) {
            throw new \Nette\InvalidStateException('DOM error: ' + $msg);
        }
        $this->getContext()->logger->log(self::TAG, \Logger::LOG_INFO, '#'.$cid.' data download completed.');
        $newData = array();
        foreach ($dom->documentElement->childNodes as $node) {
            if ($node->nodeType == XML_ELEMENT_NODE) {
                $newData[] = $this->parseProgramNode($node);
            }
        } // parse data from xml to array
        $lines = $this->getLines($newData); // gets unique lines array
        
        $db_lines = $this->getContext()->database->table('lines')->where('cid = ' . $cid); //gets lines from db

        $lines = $this->dataIntersect($lines, $db_lines, array('title')); //returns only lines not in db

        foreach ($lines as $item) { //inserts lines to db
            $this->getContext()->database->table('lines')->insert(array('cid' => $cid, 'title' => $item));
        }
        $this->getContext()->logger->log(self::TAG, \Logger::LOG_INFO, '#'.$cid.' lines sucesfully processed');
        $lines = $this->getContext()->database->table('lines')->where('cid', $cid)->fetchPairs('title', 'id'); //selects lines with theirs db ids

        $oldData = $this->getContext()->database->table('annotations')->where('cid', $cid); //selects annotation data in db
        $newData = $this->formatData($newData, $lines); //formats XML data to database format
        $data = $this->dataIntersect($newData, $oldData, array('author', 'title', 'annotation', 'lid', 'type', 'startTime', 'endTime'));
        $this->getContext()->logger->log(self::TAG, \Logger::LOG_INFO, '#'.$cid.' annotation intersected.');
        //returns only changed or new annotation data
        
        if (count($data)) {
            //creates multiple INSERT query
            
            $sqlParts = array();
            foreach ($data as $item) {
                $part = "(";
                foreach (array('pid', 'author', 'title', 'annotation', 'lid', 'type', 'startTime', 'endTime') as $col) {
                    if (isset($item[$col])) {
                        $part.= $this->getContext()->database->quote($item[$col]) . ", ";
                    } else {
                        $part .= 'NULL,';
                    }
                }
                $part.= $cid . ")";
                $sqlParts[] = $part;
            }
            //$sql = trim($sql, ", \n");
            $sqls = array_chunk($sqlParts, 50);
            $this->getContext()->logger->log(self::TAG, \Logger::LOG_INFO, '#'.$cid.' begining insert of annotations. '.count($sqls).' chunks.');
            $this->getContext()->database->beginTransaction();
            try {
            foreach($sqls as $key => $items) {
                $sql = "INSERT INTO `annotations` (`pid`,`author`,`title`,`annotation`,`lid`,`type`,`startTime`,`endTime`, `cid`) VALUES ";
                $sql.= implode(", \n", $items);
            try {
                //execute multiple INSERT
                $this->getContext()->database->exec($sql);
                $this->getContext()->logger->log(self::TAG, \Logger::LOG_INFO, '#'.$cid.' annotations inserted, chuck '.$key.', no updates.');
            } catch (\PDOException $e) {
                //if fails because multiple PID key, we try ON DUPLICATE UPDATE
                
                if($e->getCode() == '23000') {
                    $this->getContext()->logger->log(self::TAG, \Logger::LOG_INFO, '#'.$cid.' there are some duplicities, going to INSERT ON DUPLICATE KEY.');
                    //you have to update
                    //creates one-by-one INSERT - ON DUPLICATE KEY UPDATE query
                    foreach($data as $item) {
                        $insert = $update = "";
                        foreach(array('pid', 'author', 'title', 'annotation', 'lid', 'type', 'startTime', 'endTime') as $col) {
                            if(isset($item[$col])) {
                            $insert .= $this->getContext()->database->quote($item[$col]).", ";
                            if($col != 'pid')
                                $update .= "`$col`=".$this->getContext()->database->quote($item[$col]).", ";
                            } else {
                                $insert .= 'NULL,';
                                $update .= "`$col`=NULL, ";
                            }
                            
                        }
                        $insert = trim($insert,", ");
                        $update = trim($update,", ");
                        
                        $this->getContext()->database
                                ->exec("INSERT INTO `annotations` (`pid`,`author`,`title`,`annotation`,`lid`,`type`,`startTime`,`endTime`, `cid`) VALUES "
                                        ."($insert,'$cid') "
                                        ."ON DUPLICATE KEY UPDATE $update");
                        
                    }
                    $this->getContext()->logger->log(self::TAG, \Logger::LOG_INFO, '#'.$cid.' annotations updates completed sucesfully.');
                }
                else {
                    throw $e;
                }
            }}
            $this->getContext()->database->commit();
            } catch(PDOException $e) {
                $this->getContext()->database->rollBack();
                $this->getContext()->logger->log(self::TAG, \Logger::LOG_ERROR, $e->getMessage());
                throw $e;
            }
        } else {
            $this->getContext()->logger->log(self::TAG, \Logger::LOG_INFO, '#'.$cid.' no inserts/updates.');
        }
        
        $toDelete = array();
        $dbData = $this->getContext()->database->table('annotations')->where('cid',$cid)->fetchPairs('pid');
        
        foreach($dbData as $pid=>$item) {
            if(!isset($newData[$pid])) {
                $toDelete[] = $pid;
            }
        }
        if(count($toDelete)) {
            $this->getContext()->logger->log(self::TAG, \Logger::LOG_INFO, '#'.$cid.' performing '.count($toDelete).' delete of old data.');
            $this->getContext()->database->table('annotations')->where(array(
                'cid'=>$cid,
                'pid'=>$toDelete,
                ))
                ->delete();
        }
        $this->getContext()->logger->log(self::TAG, \Logger::LOG_INFO, '#'.$cid.' processing completed sucesfully, no errors.');
    }

    private function parseProgramNode(\DOMNode $node) {
        $data = array();
        foreach ($node->childNodes as $n) {
            if ($n->nodeType == XML_ELEMENT_NODE) {
                $data[$n->nodeName] = trim($n->nodeValue);
            }
        }
        return $data;
    }

    private function getLines($data) {
        $lines = array();
        foreach ($data as $item) {
            if (!in_array($item['program-line'], $lines)) {
                $lines[] = $item['program-line'];
            }
        }
        return $lines;
    }

    private function dataIntersect($new, $old, $columns) {
        $return = $new;
        foreach ($old as $item) {
            $hash = $this->countHash($item, $columns);

            foreach ($new as $key => $it) {
                if (is_string($it)) {
                    $it = array('title' => $it);
                }
                if (($this->countHash($it, $columns)) == $hash) {
                    unset($return[$key]);
                    break;
                }
            }
        }
        return $return;
    }

    private function countHash($item, $from) {
        $string = "";
        foreach ($from as $key) {

            if (isset($item[$key])) {
                $string.=$item[$key];
            }
        }
        return sha1($string);
    }

    private function formatData($data, $lines) {
        $return = array();
        foreach ($data as $item) {
            $item['lid'] = $lines[$item['program-line']];
            if ($item['start-time'] != "") {
                $item['startTime'] = new \DateTime($item['start-time']);
                $item['endTime'] = new \DateTime($item['end-time']);
                $item['startTime'] = $item['startTime']->format("Y-m-d H:i:s");
                $item['endTime'] = $item['endTime']->format("Y-m-d H:i:s");
            }
            unset($item['program-line'], $item['start-time'], $item['end-time'], $item['length']);

            $return[$item['pid']] = $item;
        }
        return $return;
    }

}

