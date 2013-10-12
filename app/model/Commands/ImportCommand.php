<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 5.8.13
 * Time: 22:32
 */

namespace Model\Commands;


use App\Tools\Mixed;
use Kdyby\Doctrine\EntityDao;
use Model\Annotation;
use Model\BasicFetchByQuery;
use Model\Event;
use Model\ProgramLine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command {

    private $eventDao;
    private $feedParser;
    private $annotationDao;

    public function __construct(EntityDao $dao, EntityDao $an, ILogger $logger,  FeedParser $parser) {
        parent::__construct();

        $this->eventDao = $dao;
        $this->annotationDao = $an;

        $this->feedParser = $parser;
        $this->logger = $logger;
        $this->feedParser->onError[] = function($message, $code) {
            $this->logger->log("#$code: $message", ILogger::ERROR);
        };
        $this->feedParser->onLog[] = function($message, $severity) {
            $this->logger->log($message, $severity);
        };
    }

    /** {@inheritdoc} */
    protected function configure() {
        $this->setName("import:parse")
                ->setDescription("Downloads and parses all active data feeds.");
    }


    /** {@inheritdoc} */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $events = $this->eventDao->fetch(new BasicFetchByQuery(['process' => TRUE, 'checkStart < ?' => new \DateTime(), 'checkStop > ?'=>new \DateTime()]));

        /** @var $event Event */
        foreach($events as $event) {
            $this->logger->start($event->name);
            $data = ($this->feedParser->parseXML($event->dataUrl));
            $this->importData($event, $data);
            $this->logger->end();
        }

    }

    private function importData(Event $event, $data) {
        $annotations = $event->getAnnotations();
        $data = Mixed::mapAssoc($data, 'pid');


        $programLinesMap = $this->processProgramLines($data, $event);

        /** @var $annotation Annotation */
        foreach($annotations as $id => $annotation) {
            if(!isset($data[$annotation->pid])) {
                $annotation->setDeleted(TRUE);
                continue;
            }
            $newData = $data[$annotation->pid];

            $this->processAnnotationUpdate($newData, $annotation, $programLinesMap);

            unset($data[$annotation->pid]);
        }

        if(!empty($data)) {
            foreach($data as $pid => $newData) {
                $annotation = new Annotation();
                $annotation->event = $event;
                $this->processAnnotationUpdate($newData, $annotation, $programLinesMap);
                $this->annotationDao->add($annotation);
            }
        }
        $this->annotationDao->save();

    }

    private function processProgramLines($data, Event $event) {
        $programLines = array_column($data, 'program-line');
        $programLines = array_unique($programLines);

        $lines = $event->getProgramLines();
        $programLines = array_flip($programLines);
        $map = [];

        /** @var $line ProgramLine */
        foreach($lines as $line) {
            if(isset($programLines[$line->title])) {
                unset($programLines[$line->title]);
            }
            $map[$line->title] = $line;
        }

        if(!empty($programLines)) {
            foreach($programLines as $line => $foo) {
                $pl = new ProgramLine();
                $pl->title = $line;
                $pl->event = $event;
                $map[$line] = $pl;
            }
        }
        return $map;
    }

    /**
     * @param $newData
     * @param $annotation
     * @param $programLinesMap
     */
    private function processAnnotationUpdate($newData, $annotation, $programLinesMap) {
        $annotation->pid = $newData['pid'];
        $annotation->author = $newData['author'];
        $annotation->title = $newData['title'];
        $annotation->annotation = $newData['annotation'];
        $annotation->startTime = $newData['start-time'];
        $annotation->endTime = $newData['end-time'];
        $annotation->location = $newData['location'];
        $annotation->type = $newData['type'];
        $annotation->programLine = $programLinesMap[$newData['program-line']];
    }


} 