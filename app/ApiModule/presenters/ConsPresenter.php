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
	}
}

?>
