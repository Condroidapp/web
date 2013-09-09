<?php
/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 5.8.13
 * Time: 22:32
 */

namespace Model\Commands;


use Kdyby\Doctrine\EntityDao;
use Model\BasicFetchByQuery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command {

    private $eventDao;
    private $feedParser;

    public function __construct(EntityDao $dao, ILogger $logger,  FeedParser $parser) {
        parent::__construct();

        $this->eventDao = $dao;
        $this->feedParser = $parser;
        $this->logger = $logger;
        $this->feedParser->onError[] = function($message, $code) {
            $this->logger->log("#$code: $message", ILogger::ERROR);
            echo "#$code: $message";
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

        foreach($events as $event) {
            $this->logger->start($event->name);
            ($this->feedParser->parseXML($event->dataUrl));
            $this->logger->end();
        }
    }


} 