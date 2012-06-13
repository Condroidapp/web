<?php
namespace ApiModule;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConsPresnter
 *
 * @author Honza
 */
class ConsPresenter extends \FrontModule\BasePresenter {
    public function renderDefault() {
		$this->template->cons = $this->getContext()->database->table('cons')->where("active=1")->limit(20)->order('id DESC');
                $device = $this->getContext()->httpRequest->getHeader("X-Device-Info", null);
                if($device != null && trim($device) != "") {
                    $parts = explode(";", $device);
                    $model = $this->getContext()->database->quote((isset($parts[0])?$parts[0]:"unknown"));
                    $sn = $this->getContext()->database->quote((isset($parts[1])?$parts[1]:"unknown"));
                    $this->getContext()->database->exec("INSERT INTO apicalls (`device`,`serial`,`con-list`) VALUES ('".$model."','".$sn."',1) ON DUPLICATE KEY UPDATE `con-list`=`con-list`+1");
                }
	}
}

?>
