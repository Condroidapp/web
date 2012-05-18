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
            $this->terminate();
        }
        $data = $this->getContext()->database->table('annotations')->where("cid = ".$cid);
        if($this->getContext()->httpRequest->getHeader('If-Modified-Since',null) != null) {
            $data = $this->getContext()->httpRequest->getHeaders('If-Modified-Since');
            $data->where('timestamp > '.$date);
        }
        
        $this->template->annotations = $data;
        $this->template->lines = $this->getContext()->database->table('lines')->where("cid = ".$cid);
    }
}

?>
