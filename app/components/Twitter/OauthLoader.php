<?php declare(strict_types = 1);

namespace App\Components\Twitter;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Smasty\Components\Twitter\ILoader;
use Smasty\Components\Twitter\TwitterException;
use Twitter;

class OauthLoader implements ILoader
{

	/** @var string */
	private $consumerKey;

	/** @var string */
	private $consumerSecret;

	/** @var string */
	private $accessToken;

	/** @var string */
	private $accessTokenSecret;

	/** @var array */
	private $config = [];

	/** @var array */
	private $tweetCache = [];

	/** @var \Nette\Caching\Cache */
	private $cache;

	public function __construct(array $tokens, IStorage $storage)
	{
		$this->accessToken = $tokens['accessToken'];
		$this->accessTokenSecret = $tokens['accessTokenSecret'];
		$this->consumerKey = $tokens['consumerKey'];
		$this->consumerSecret = $tokens['consumerSecret'];
		$this->cache = new Cache($storage, __CLASS__);
	}

	/**
	 * Get the loaded tweets, formatted according to Twitter REST API JSON format.
	 *
	 * @param array $config Configuration options
	 * @return array|null
	 */
	public function getTweets(array $config): ?array
	{
		$data = $this->cache->load('statuses');
		if ($data === null) {
			$this->config = $config;

			$path = md5('statuses' . json_encode($this->config));
			if (isset($this->tweetCache[$path])) {
				return $this->tweetCache[$path];
			}

			set_error_handler(function ($s, $m): void {
				restore_error_handler();
				throw new TwitterException($m);
			});
			$content = $this->getStatuses();
			restore_error_handler();

			$this->cache->save('statuses', $content, [
				Cache::EXPIRATION => '+1h',
			]);

			return $this->tweetCache[$path] = $content;
		}

		return $data;
	}

	/**
	 * @return \stdClass[]
	 */
	protected function getStatuses()
	{
		$twitter = new Twitter($this->consumerKey, $this->consumerSecret, $this->accessToken, $this->accessTokenSecret);

		return $twitter->load($twitter::ME, 5, $this->config);
	}

}
