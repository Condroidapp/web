<?php

namespace FrontModule;

use Smasty\Components\Twitter\TwitterControlFactory;

class HomepagePresenter extends BasePresenter
{

	/** @var \Smasty\Components\Twitter\TwitterControlFactory */
	private $twitterControlFactory;

	public function __construct(TwitterControlFactory $twitterControlFactory)
	{
		parent::__construct();
		$this->twitterControlFactory = $twitterControlFactory;
	}

	public function createComponentTwitter()
	{
		return $this->twitterControlFactory->create([
			'screen_name' => 'Condroid_CZ',
			'count' => 4,
		]);
	}

}
