<?php
namespace ApiModule;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
use Kdyby\Doctrine\EntityDao;
use Model\BasicFetchByQuery;
use Model\Queries\AnnotationLastMod;
use Nette\Application\BadRequestException;
use Nette\Http\Request;
use Nette\Http\Response;

/**
 * Description of AnnotationsPresenter
 *
 * @author Honza
 */
class AnnotationsPresenter extends \FrontModule\BasePresenter  {

    /**
     * @autowire
     * @var \Nette\Http\Request */
    protected $httpRequest;
    /**
     * @autowire
     * @var \Nette\Http\Response
     */
    protected $httpResponse;

    /**
     * @autowire(\Model\Annotation, factory=\Kdyby\Doctrine\EntityDaoFactory)
     * @var \Kdyby\Doctrine\EntityDao
     */
    protected $annotationRepository;
    /**
     * @autowire
     * @var \Model\ApiLogger
     */
    //protected $apiLogger;


    public function actionDefault($cid) {
        if($cid <= 0) {
            throw new BadRequestException('No cid supplied',404);
        }
        $clientLastMod = new \DateTime($this->httpRequest->getHeader('If-Modified-Since', "Sun, 13 Mar 1988 17:00:00 +0200")); // :-)

        $clientLastMod->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        /** @var $actualLastMod \DateTime */
        $actualLastMod = $this->annotationRepository->fetchOne(new AnnotationLastMod($cid))['timestamp'];
        $totalCount = count($this->annotationRepository->fetch(new BasicFetchByQuery(['event'=>$cid])));
        $clientCount = (int) $this->httpRequest->getHeader('X-If-Count-Not-Match', 0);

        $data = NULL;

        if($totalCount === $clientCount && $actualLastMod <= $clientLastMod) {
            $this->httpResponse->setCode(Response::S304_NOT_MODIFIED);
         //   $this->apiLogger->logCheck();
            $this->terminate();
        }
        else {
            $this->httpResponse->setCode(Response::S200_OK);
            if($this->httpRequest->isMethod('HEAD')) {
              //  $this->apiLogger->logCheck();
                $this->terminate();
            }
        }

        $this->httpResponse->setHeader('Last-Modified', $actualLastMod->format('D, j M Y H:i:s O'));

        if ($clientLastMod < $actualLastMod) {
            $data = $this->annotationRepository->fetch(new BasicFetchByQuery(['event' => $cid, 'timestamp > ?'=> ($clientLastMod)]));
          //  $this->apiLogger->logUpdate();
        }
        else {

            $data = $this->annotationRepository->fetch(new BasicFetchByQuery(['event' => $cid]));
            $this->httpResponse->setHeader('X-Full-Update', 1);
           // $this->apiLogger->logFullUpdate();
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

}


