<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 5.8.13
 * Time: 22:43
 */

namespace Model\Commands;


use App\InvalidProgramNodeException;
use Model\Dao;
use Nette\Diagnostics\Debugger;
use Nette\Object;
use Nette\Utils\Strings;

class FeedParser extends Object {

    private $annotations;
    private $programLines;

    public $onError = [];
    public $onLog = [];
    private $programIds = [];


    public function __construct(/*Dao $annotation, Dao $programLines*/) {
        /*$this->annotations = $annotation;
        $this->programLines = $annotation;*/
    }

    public function parseXML($feedUrl) {
        $this->programIds = [];
        $dom = new \DOMDocument();
        $return = @$dom->load($feedUrl);
        if(!$return) {
            $error = error_get_last();
            $this->log("Feed data source load failed!", ILogger::ERROR);
            $this->onError("DOM Load Failed: ".$error['message'], 1);
            return;
        }
        $this->log("Feed data source loaded");
        $this->log("Processing XML");
        $feedData = [];
        foreach ($dom->documentElement->childNodes as $node) {
            try {
                if ($node->nodeType == XML_ELEMENT_NODE) {
                    $feedData[] = $this->parseProgramNode($node);
                }
            } catch (InvalidProgramNodeException $e) {
                //skip node
            }
        }
        $this->log("XML sucesfully processed, found ".count($feedData)." entries.");
        return $feedData;
    }

    private function parseProgramNode(\DOMNode $node) {
        $data = array();
        foreach ($node->childNodes as $n) {
            if ($n->nodeType == XML_ELEMENT_NODE) {
                $value = $this->sanitizeValue($n->nodeValue, $n->nodeName);
                if($this->validateValue($n->nodeName, $value)) {
                    $data[$n->nodeName] = $value;
                }
            }
        }
        $this->validateNode($data);
        return $data;
    }

    private function validateValue($name, $value) {
        switch($name) {
            case 'pid':
                if(!ctype_digit($value)) {
                    $this->onError("Expected PID value to be numeric, ".$value.' given.', 101);
                }
                if(in_array($value, $this->programIds)) {
                    $this->onError("Program ID is not unique throughout the document.", 102);
                    throw new InvalidProgramNodeException();
                }
                break;
            case 'author':
                if($value === "") {
                    $this->onError("Author has to be filled.", 103);
                }
                break;
            case 'title':
                if($value === '') {
                    $this->onError("Title has to be filled.", 104);
                }
                break;
            case 'program-line':
                if($value === '') {
                    $this->onError("Program line has to be filled.", 105);
                }
                break;
            case 'start-time':
            case 'end-time':
                if($value !== NULL && !strtotime($value)) {
                    $this->onError('Invalid datetime value '.$value, 106);
                }
                break;
            case 'type':
            case 'length':
            case 'location':
            case 'annotation':
                break;
            default:
                $this->onError('Unknown node '.$name, 110);
        }
        return true;
    }

    private function validateNode($data) {
        if((isset($data['start-time']) && $data['start-time'] != "") && (isset($data['end-time']) && $data['end-time'] !== "")) {

        } else {
            $this->onError('PID '.$data['pid'].' - When you set start or end time, the other one has to be set too.', 107);
        }
    }

    private function log($message, $severity = ILogger::INFO) {
        $this->onLog($message, $severity);
    }

    private function sanitizeValue($nodeValue, $name) {
        $value = Strings::trim($nodeValue);
        if($value == "") {
            return NULL;
        }
        if(in_array($name, ['start-time', 'end-time'])) {
            $value = str_replace([' ', ';', ','], '', $value);
        }
        return $value;
    }

} 