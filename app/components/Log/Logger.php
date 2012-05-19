<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Logger
 *
 * @author Honza
 */
class Logger extends \Nette\Object {
    private $database;
    private $msgs = array();
    
    const LOG_ERROR = 'error';
    const LOG_INFO = 'info';
    
    function __construct(Nette\Database\Connection $database) {
        $this->database = $database;
    }
    
    public function log($tag, $severity, $msg) {
        $this->msgs[] = array(
            'message' => $msg,
            'tag' => $tag,
            'severity' =>$severity,
            'time' => microtime(TRUE),
        );
    }
    
    public function flush() {
        $this->database->table('logger')->insert($this->msgs);
    }

    
    
}

?>
