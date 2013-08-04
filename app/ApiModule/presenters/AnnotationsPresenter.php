<?php
namespace ApiModule;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
use Model\BasicFetchByQuery;
use Model\Dao;
use Model\Queries\AnnotationLastMod;
use Nette\Http\Request;
use Nette\Http\Response;

/**
 * Description of AnnotationsPresenter
 *
 * @author Honza
 */
class AnnotationsPresenter extends \FrontModule\BasePresenter  {

    /** @var Request */
    public $httpRequest;
    /** @var  Response */
    public $httpResponse;

    /** @var Dao */
    private $annotationRepository;

    protected function startup() {
        parent::startup();
        $this->annotationRepository = $this->context->annotationRepository;
    }


    public function actionDefault($cid) {
        if($cid <= 0) {
            throw new \Nette\Application\BadRequestException('No cid supplied',404);
        }
        $clientLastMod = new \DateTime($this->httpRequest->getHeader('If-Modified-Since', "Sun, 13 Mar 1988 17:00:00 +0200")); // :-)
        $clientLastMod=new \DateTime("Wed, 17 Apr 2013 21:40:44 +0200");
        $clientLastMod->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        $actualLastMod = $this->annotationRepository->fetchOne(new AnnotationLastMod($cid))['timestamp'];
        $totalCount = count($this->annotationRepository->fetch(new BasicFetchByQuery(['event'=>$cid])));
        $clientCount = (int) $this->httpRequest->getHeader('X-If-Count-Not-Match', 0);

        $data = NULL;

        if($totalCount === $clientCount && $actualLastMod <= $clientLastMod) {
            $this->httpResponse->setCode(\Nette\Http\Response::S304_NOT_MODIFIED);
            $this->terminate();
        }
        if($this->httpRequest->isMethod('HEAD')) {
            $this->getContext()->httpResponse->setCode(\Nette\Http\Response::S200_OK);
            $this->terminate();
        }

        if ($clientLastMod < $actualLastMod) {
               $data = $this->annotationRepository->fetch(new BasicFetchByQuery(['event' => $cid, 'timestamp > ?'=> ($clientLastMod)]));
        }
        else if($totalCount !== $clientCount) {
            $data = $this->annotationRepository->fetch(new BasicFetchByQuery(['event' => $cid]));
            $this->httpResponse->setHeader('X-Full-Update', 1);
        } else {
            throw new \Exception("This should not happen!");
        }


           /* $device = $this->getContext()->httpRequest->getHeader("X-Device-Info", null);
            if($device != null && trim($device) != "") {
                    $parts = explode(";", $device);
                    if(isset($parts[0])) {
                        if(\Nette\Utils\Strings::startsWith($parts[0], ":")) {
                            $parts[0] = trim($parts[0], ": ");
                        }
                    }
                    $model = $this->getContext()->database->quote((isset($parts[0])?$parts[0]:"unknown"));
                    $sn = $this->getContext()->database->quote((isset($parts[1])?$parts[1]:"unknown"));
                    $os = $this->getContext()->database->quote((isset($parts[2])?$parts[2]:"unknown"));
                    $this->getContext()->database->exec("INSERT INTO apicalls (`device`,`serial`,`annotations-check`,`os-version`) VALUES (".$model.",".$sn.",1,".$os.") ON DUPLICATE KEY UPDATE `os-version` = ".$os.", `annotations-check`=`annotations-check`+1");
            }*/
            

           /* $device = $this->getContext()->httpRequest->getHeader("X-Device-Info", null);
            if($device != null && trim($device) != "") {
                    $parts = explode(";", $device);
                    if(isset($parts[0])) {
                        if(\Nette\Utils\Strings::startsWith($parts[0], ":")) {
                            $parts[0] = trim($parts[0], ": ");
                        }
                    }
                    $model = $this->getContext()->database->quote((isset($parts[0])?$parts[0]:"unknown"));
                    $sn = $this->getContext()->database->quote((isset($parts[1])?$parts[1]:"unknown"));
                    $os = $this->getContext()->database->quote((isset($parts[2])?$parts[2]:"unknown"));
                    $this->getContext()->database->exec("INSERT INTO apicalls (`device`,`serial`,`annotations-update`,`os-version`) VALUES (".$model.",".$sn.",1,".$os.") ON DUPLICATE KEY UPDATE `os-version` = ".$os.", `annotations-update`=`annotations-update`+1");
            }*/
             /*$device = $this->getContext()->httpRequest->getHeader("X-Device-Info", null);
            if($device != null && trim($device) != "") {
                    $parts = explode(";", $device);
                    if(isset($parts[0])) {
                        if(\Nette\Utils\Strings::startsWith($parts[0], ":")) {
                            $parts[0] = trim($parts[0], ": ");
                        }
                    }
                    $model = $this->getContext()->database->quote((isset($parts[0])?$parts[0]:"unknown"));
                    $sn = $this->getContext()->database->quote((isset($parts[1])?$parts[1]:"unknown"));
                    $os = $this->getContext()->database->quote((isset($parts[2])?$parts[2]:"unknown"));
                    $this->getContext()->database->exec("INSERT INTO apicalls (`device`,`serial`,`annotations-full-download`,`os-version`) VALUES (".$model.",".$sn.",1,".$os.") ON DUPLICATE KEY UPDATE `os-version` = ".$os.", `annotations-full-download`=`annotations-full-download`+1");
            }*/

        $this->template->annotations = $data;
        $this->template->lastMod = $actualLastMod;
        $this->template->count = $totalCount;
    }

    public function injectHttpRequest(Request $r) {
        $this->httpRequest = $r;
    }

    public function injectHttpResponse(Response $e) {
        $this->httpResponse =$e;
    }
}


