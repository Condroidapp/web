<?php

/**
 * Homepage presenter.
 *
 * @author     John Doe
 * @package    MyApplication
 */
class HomepagePresenter extends BasePresenter
{

	public function createComponentTwitter($name) {
            $c = new \Smasty\Components\Twitter\Control(array(
                'screenName' => 'Condroid_CZ',
                'tweetCount' => 3,
            ));
            return $c;
        }

}
