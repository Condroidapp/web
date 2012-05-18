<?php

namespace CliModule;

/**
 * Description of ImportPresenter
 *
 * @author Honza
 */
class ImportPresenter extends CliPresenter {

    public function actionDefault() {
        foreach ($this->getContext()->database->table('cons')->select('id,dataUrl')->where('active = 1') as $con) {
            $this->fetch($con->id, $con->dataUrl);
        }
    }

    private function fetch($cid, $url) {
        //todo logging
        $dom = new \DOMDocument();
        \Nette\Diagnostics\Debugger::tryError();
        $dom->load($url); //fetch document
        if (\Nette\Diagnostics\Debugger::catchError($msg)) {
            throw new \Nette\InvalidStateException('DOM error: ' + $msg);
        }
        $data = array();
        foreach ($dom->documentElement->childNodes as $node) {
            if ($node->nodeType == XML_ELEMENT_NODE) {
                $data[] = $this->parseProgramNode($node);
            }
        } // parse data from xml to array
        $lines = $this->getLines($data); // gets unique lines array

        $db_lines = $this->getContext()->database->table('lines')->where('cid = ' . $cid); //gets lines from db

        $lines = $this->dataIntersect($lines, $db_lines, array('title')); //returns only lines not in db

        foreach ($lines as $item) { //inserts lines to db
            $this->getContext()->database->table('lines')->insert(array('cid' => $cid, 'title' => $item));
        }
        $lines = $this->getContext()->database->table('lines')->where('cid', $cid)->fetchPairs('title', 'id'); //selects lines with theirs db ids

        $oldData = $this->getContext()->database->table('annotations')->where('cid', $cid); //selects annotation data in db
        $data = $this->formatData($data, $lines); //formats XML data to database format
        $data = $this->dataIntersect($data, $oldData, array('author', 'title', 'annotation', 'lid', 'type', 'startTime', 'endTime'));
        //returns only changed or new annotation data
        
        if (count($data)) {
            //creates multiple INSERT query
            $sql = "INSERT INTO `annotations` (`pid`,`author`,`title`,`annotation`,`lid`,`type`,`startTime`,`endTime`, `cid`) VALUES ";
            foreach ($data as $item) {
                $sql .= "(";
                foreach (array('pid', 'author', 'title', 'annotation', 'lid', 'type', 'startTime', 'endTime') as $col) {
                    if (isset($item[$col])) {
                        $sql.= "'" . mysql_real_escape_string($item[$col]) . "', ";
                    } else {
                        $sql .= 'NULL,';
                    }
                }
                $sql.= $cid . "), \n";
            }
            $sql = trim($sql, ", \n");
            
            try {
                //execute multiple INSERT
                $this->getContext()->database->exec($sql);
            } catch (\PDOException $e) {
                //if fails because multiple PID key, we try ON DUPLICATE UPDATE
                if($e->getCode() == '23000') {
                    //you have to update
                    //creates one-by-one INSERT - ON DUPLICATE KEY UPDATE query
                    foreach($data as $item) {
                        $insert = $update = "";
                        foreach(array('pid', 'author', 'title', 'annotation', 'lid', 'type', 'startTime', 'endTime') as $col) {
                            if(isset($item[$col])) {
                            $insert .= "'".mysql_real_escape_string($item[$col])."', ";
                            if($col != 'pid')
                                $update .= "`$col`='".mysql_real_escape_string($item[$col])."', ";
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
                }
            }
        }
        $this->terminate();
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

