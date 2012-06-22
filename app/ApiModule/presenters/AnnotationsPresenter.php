<?php
namespace ApiModule;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AnnotationsPresenter
 *
 * @author Honza
 */
class AnnotationsPresenter extends \FrontModule\BasePresenter  {
    public function actionDefault($cid) {
        if($cid <= 0) {
            throw new \Nette\Application\BadRequestException('No cid supplied',404);
        }
        if($this->getContext()->httpRequest->isMethod('HEAD')) {
            $actualCount = $this->getContext()->database->table('annotations')->where('cid',$cid)->count();
            $lastMod = $this->getContext()->httpRequest->getHeader('If-Modified-Since', date("r"));
            $itemsCount = $this->getContext()->httpRequest->getHeader('X-If-Count-Not-Match',$actualCount);
            $lastMod = new \DateTime($lastMod);
            $lastMod = $lastMod->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            
            $newOnes = $this->getContext()->database->table('annotations')->where('timestamp > ?',$lastMod)->where('cid',$cid)->count();
            if($newOnes > 0 || $itemsCount != $actualCount ) {
                $this->getContext()->httpResponse->setCode(\Nette\Http\Response::S200_OK);
            } else {
                $this->getContext()->httpResponse->setCode(\Nette\Http\Response::S304_NOT_MODIFIED);
             //   dump($lastMod);
             //   dump($newOnes);
            }
            $device = $this->getContext()->httpRequest->getHeader("X-Device-Info", null);
            if($device != null && trim($device) != "") {
                    $parts = explode(";", $device);
                    $model = $this->getContext()->database->quote((isset($parts[0])?$parts[0]:"unknown"));
                    $sn = $this->getContext()->database->quote((isset($parts[1])?$parts[1]:"unknown"));
                    $os = $this->getContext()->database->quote((isset($parts[2])?$parts[2]:"unknown"));
                    $this->getContext()->database->exec("INSERT INTO apicalls (`device`,`serial`,`annotations-check`,`os-version`) VALUES (".$model.",".$sn.",1,".$os.") ON DUPLICATE KEY UPDATE `os-version` = ".$os.", `annotations-check`=`annotations-check`+1");
            }
            
            $this->terminate();
            
        }
        $data = $this->getContext()->database->table('annotations')->where("cid = ".$cid);
        if($this->getContext()->httpRequest->getHeader('If-Modified-Since',null) != null) {
            $date = new \Nette\DateTime($this->getContext()->httpRequest->getHeader('If-Modified-Since'));
            
            $actualCount = $this->getContext()->database->table('annotations')->where('cid',$cid)->count();
            $itemsCount = $this->getContext()->httpRequest->getHeader('X-If-Count-Not-Match',$actualCount);
            $newOnes = $this->getContext()->database->table('annotations')->where('timestamp > ?',$date)->where('cid',$cid)->count();
            
            $device = $this->getContext()->httpRequest->getHeader("X-Device-Info", null);
            if($device != null && trim($device) != "") {
                    $parts = explode(";", $device);
                    $model = $this->getContext()->database->quote((isset($parts[0])?$parts[0]:"unknown"));
                    $sn = $this->getContext()->database->quote((isset($parts[1])?$parts[1]:"unknown"));
                    $os = $this->getContext()->database->quote((isset($parts[2])?$parts[2]:"unknown"));
                    $this->getContext()->database->exec("INSERT INTO apicalls (`device`,`serial`,`annotations-update`,`os-version`) VALUES (".$model.",".$sn.",1,".$os.") ON DUPLICATE KEY UPDATE `os-version` = ".$os.", `annotations-update`=`annotations-update`+1");
            }
            
            if($newOnes > 0) {
                $data->where('timestamp > ?',$date);
            } else if($actualCount == $itemsCount) {
                $this->terminate();
            } else {
                $this->getContext()->httpResponse->setHeader("X-Full-Update", "1");
            }
        }
        else {
            $device = $this->getContext()->httpRequest->getHeader("X-Device-Info", null);
            if($device != null && trim($device) != "") {
                    $parts = explode(";", $device);
                    $model = $this->getContext()->database->quote((isset($parts[0])?$parts[0]:"unknown"));
                    $sn = $this->getContext()->database->quote((isset($parts[1])?$parts[1]:"unknown"));
                    $os = $this->getContext()->database->quote((isset($parts[2])?$parts[2]:"unknown"));
                    $this->getContext()->database->exec("INSERT INTO apicalls (`device`,`serial`,`annotations-full-download`,`os-version`) VALUES (".$model.",".$sn.",1,".$os.") ON DUPLICATE KEY UPDATE `os-version` = ".$os.", `annotations-full-download`=`annotations-full-download`+1");
            }
        }
        
        $this->template->annotations = $data;
        $this->template->lastMod = $this->getContext()->database->table('annotations')->select('timestamp')->where('cid',$cid)->order('timestamp DESC')->limit(1)->fetch()->timestamp;
        $this->template->lines = $this->getContext()->database->table('lines')->where("cid = ".$cid);
    }
}


