<?php
namespace FrontModule;

/**
 * Homepage presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class HomepagePresenter extends BasePresenter
{

	public function createComponentTwitter($name)
	{
		$c = new \Smasty\Components\Twitter\Control([
			'screen_name' => 'Condroid_CZ',
			'count' => 4,
		]);
		$c->setLoader(new \OauthLoader($this->getContext()->parameters['twitter']));

		return $c;
	}

	public function actionLog()
	{
		$this->template->data = $this->getContext()->database->table("logger")->order("time DESC")->limit(200);
	}

}
