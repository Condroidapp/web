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
use Nette\Diagnostics\Debugger;
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
        try {
            $annotations = $event->getAnnotations();
            $data = Mixed::mapAssoc($data, 'pid');

            $additions = 0;
            $changes = 0;
            $deletions = 0;
            $totalCount = count($data);


            $programLinesMap = $this->processProgramLines($data, $event);

            /** @var $annotation Annotation */
            foreach($annotations as $id => $annotation) {
                if(!isset($data[$annotation->pid])) {
                    $annotation->setDeleted(TRUE);
                    $deletions++;
                    continue;
                }
                $newData = $data[$annotation->pid];
                if($this->annotationChanged($newData, $annotation)) {
                    $changes++;
                    $this->processAnnotationUpdate($newData, $annotation, $programLinesMap);
                }

                unset($data[$annotation->pid]);
            }

            if(!empty($data)) {
                foreach($data as $newData) {
                    $additions++;
                    $annotation = new Annotation();
                    $annotation->event = $event;
                    $this->processAnnotationUpdate($newData, $annotation, $programLinesMap);
                    $this->annotationDao->add($annotation);
                }
            }
            $this->logger->log("Found total of $totalCount records, discovered $additions new, $changes changed, ".
                "$deletions deleted, ".($totalCount-$additions-$deletions-$changes)." unchanged.");
            $this->annotationDao->save();

            $this->logger->log('Successfully processed the feed.');
        } catch (\Exception $e) {
            $this->logger->log('Error during data processing. '.$e->getMessage());
            Debugger::log($e);
            return 1;
        }

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
        if(isset($newData['start-time']) && $newData['start-time']) {
            $annotation->startTime = new \DateTime($newData['start-time']);
        }
        if(isset($newData['end-time']) && $newData['end-time']) {
            $annotation->endTime = new \DateTime($newData['end-time']);
        }
        $annotation->location = $newData['location'];
        $annotation->type = $newData['type'];
        $annotation->programLine = $programLinesMap[$newData['program-line']];
    }

    private function annotationChanged($newData, $annotation) {

        foreach($newData as $key => $item) {
            if($key === 'pid') {
                continue;
            }
            if(!isset($annotation->$key)) {
                continue;
            }
            if($key === 'start-time') {
                $key = 'startTime';
            }
            if($key === 'end-time') {
                $key = 'endTime';
            }
            if($key === 'program-line') {
                $key = 'programLine';
            }
            $previousValue = $annotation->$key;
            if($previousValue instanceof Event) {
                continue;
            }
            if($previousValue instanceof ProgramLine) {
                $previousValue = $previousValue->title;
            }
            if($previousValue instanceof \DateTime) {
                $previousValue = $previousValue->format('c');
            }
            if($item !== $previousValue) {
                return TRUE;
            }
        }
        return FALSE;
    }


} 