<?php
namespace ApiModule;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
use Model\Dao;
use Model\Persistence\IDao;

/**
 * Description of ConsPresnter
 *
 * @author Honza
 */
class ConsPresenter extends \FrontModule\BasePresenter {



    public function renderDefault() {
		$this->template->cons = $this->getContext()->eventRepository->findBy(['active'=>1]);
     /*   $device = $this->getContext()->httpRequest->getHeader("X-Device-Info", null);
        if($device != null && trim($device) != "") {
            $parts = explode(";", $device);
            $model = $this->getContext()->database->quote((isset($parts[0])?$parts[0]:"unknown"));
            $sn = $this->getContext()->database->quote((isset($parts[1])?$parts[1]:"unknown"));
            $os = $this->getContext()->database->quote((isset($parts[2])?$parts[2]:"unknown"));
                    
            $this->getContext()->database->exec("INSERT INTO apicalls (`device`,`serial`,`con-list`,`os-version`) VALUES (".$model.",".$sn.",1,".$os.") ON DUPLICATE KEY UPDATE `os-version` = ".$os.", `con-list`=`con-list`+1");
            }*/ //TODO
    }

}

?>
