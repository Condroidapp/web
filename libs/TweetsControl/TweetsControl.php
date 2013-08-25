<?php
use Nette\Diagnostics\Debugger;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Smasty\Components\Twitter\ILoader;

/**
 * Created by JetBrains PhpStorm.
 * User: Jan
 * Date: 13.6.13
 * Time: 11:32
 * To change this template use File | Settings | File Templates.
 */

class OauthLoader implements  ILoader {

    private $consumerKey;
    private $consumerSecret;
    private $accessToken;
    private $accessTokenSecret;
    /** @var array */
    private $config = array();

    /** @var array */
    private $tweetCache = array();

    function __construct($tokens) {
        $this->accessToken = $tokens['accessToken'];
        $this->accessTokenSecret = $tokens['accessTokenSecret'];
        $this->consumerKey = $tokens['consumerKey'];
        $this->consumerSecret = $tokens['consumerSecret'];
    }


    /**
     * Get the loaded tweets, formatted according to Twitter REST API JSON format.
     *
     * @param array $config Configuration options
     * @return array|null
     */
    public function getTweets(array $config) {
        $cache = Nette\Environment::getCache('Tweets');
        if (!$cache['statuses']) {
            $this->config = $config;

            $path = md5("statuses".json_encode($this->config));
            if (isset($this->tweetCache[$path])) {
                return $this->tweetCache[$path];
            }

            set_error_handler(function($s, $m) {
                restore_error_handler();
                throw new TwitterException($m);
            });
            $content = $this->generateRequestUrl();
            restore_error_handler();

            try {
                $cache->save('statuses', $content, array(
                    Nette\Caching\Cache::EXPIRATION => "+1h",
                ));
                return $this->tweetCache[$path] = $cache['statuses'];
            } catch (JsonException $e) {
                throw new TwitterException($e->getMessage(), $e->getCode(), $e);
            }
        }
        else
            return $cache['statuses'];
    }

    /**
     * Generate URL for Twitter JSON API request.
     *
     * @return Url
     */
    protected function generateRequestUrl() {
        $twitter = new Twitter($this->consumerKey, $this->consumerSecret, $this->accessToken, $this->accessTokenSecret);
        $statuses = $twitter->load($twitter::ME, 5, $this->config);
        return $statuses;
        foreach($statuses as $status) {
            dump($status);
        }
        exit;
        if ($this->config['userId'])
            $url->appendQuery('user_id=' . $this->config['userId']);
        elseif ($this->config['screenName'])
            $url->appendQuery('screen_name=' . $this->config['screenName']);

        if ($this->config['tweetCount'])
            $url->appendQuery('count=' . $this->config['tweetCount']);
        if ($this->config['retweets'])
            $url->appendQuery('include_rts=true');
        if (!$this->config['replies'])
            $url->appendQuery('exclude_replies=true');

        $url->appendQuery('include_entities=true');
        return $url;
    }
}