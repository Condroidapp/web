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
                    $this->getContext()->database->exec("INSERT INTO apicalls (`device`,`serial`,`annotations-check`) VALUES (".$model.",".$sn.",1) ON DUPLICATE KEY UPDATE `annotations-check`=`annotations-check`+1");
            }
            
            $this->terminate();
            
        }
        $data = $this->getContext()->database->table('annotations')->where("cid = ".$cid);
        if($this->getContext()->httpRequest->getHeader('If-Modified-Since',null) != null) {
            $date = new \Nette\DateTime($this->getContext()->httpRequest->getHeader('If-Modified-Since'));
            
            $actualCount = $this->getContext()->database->table('annotations')->where('cid',$cid)->count();
            $itemsCount = $this->getContext()->httpRequest->getHeader('X-If-Count-Not-Match',$actualCount);
            $newOnes = $this->getContext()->database->table('annotations')->where('timestamp > ?',$lastMod)->where('cid',$cid)->count();
            
            $device = $this->getContext()->httpRequest->getHeader("X-Device-Info", null);
            if($device != null && trim($device) != "") {
                    $parts = explode(";", $device);
                    $model = $this->getContext()->database->quote((isset($parts[0])?$parts[0]:"unknown"));
                    $sn = $this->getContext()->database->quote((isset($parts[1])?$parts[1]:"unknown"));
                    $this->getContext()->database->exec("INSERT INTO apicalls (`device`,`serial`,`annotations-update`) VALUES (".$model.",".$sn.",1) ON DUPLICATE KEY UPDATE `annotations-update`=`annotations-update`+1");
            }
            
            if($newOnes > 0) {
                $data->where('timestamp > ?',$date);
            } elseif($actualCount == $itemsCount) {
                $this->terminate();
            }
        }
        else {
            $device = $this->getContext()->httpRequest->getHeader("X-Device-Info", null);
            if($device != null && trim($device) != "") {
                    $parts = explode(";", $device);
                    $model = $this->getContext()->database->quote((isset($parts[0])?$parts[0]:"unknown"));
                    $sn = $this->getContext()->database->quote((isset($parts[1])?$parts[1]:"unknown"));
                    $this->getContext()->database->exec("INSERT INTO apicalls (`device`,`serial`,`annotations-full-download`) VALUES (".$model.",".$sn.",1) ON DUPLICATE KEY UPDATE `annotations-full-download`=`annotations-full-download`+1");
            }
        }
        
        $this->template->annotations = $data;
        $this->template->lastMod = $this->getContext()->database->table('annotations')->select('timestamp')->where('cid',$cid)->order('timestamp DESC')->limit(1)->fetch()->timestamp;
        $this->template->lines = $this->getContext()->database->table('lines')->where("cid = ".$cid);
    }
}


