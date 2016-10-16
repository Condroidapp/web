<?php
/**
 * TwitterControl for Nette Framework 2.0, http://github.com/smasty/TwitterControl
 * Copyright 2011 Martin Srank (http://smasty.net)
 * Licensed under terms of the MIT License (http://opensource.org/licenses/mit-license)
 */

namespace Smasty\Components\Twitter;

use Nette;
use Nette\Application\UI\Control as NetteControl;
use Tracy\Debugger;
use Nette\InvalidStateException;

/**
 * TwitterControl renderable component.
 *
 * Available config options:
 * - screenName => Twitter screen name (either screenName or userId is required)
 * - userId => Twitter user ID (takes precedence over screenName, if both specified)
 * - tweetCount => Number of tweets to load (max. 200)
 *
 * - header => Render component header with user info
 * - avatars => Render avatars next to tweets
 * - retweets => Include retweets
 * - replies => Include replies
 * - intents => Render tweet intents (reply, retweet, favorite)
 *
 * @author Martin Srank, http://smasty.net
 */
class Control extends NetteControl
{

	/** @var string */
	public static $templateDirectory = '/templates';

	/** @var string */
	private $templateFile = '/TwitterControl.latte';

	/** @var array */
	private $config;

	/** @var ILoader */
	private $loader;

	/** @var IFormatter */
	private $formatter;

	const VERSION = '2.0';

	public function __construct(array $config, ILoader $loader)
	{
		parent::__construct();

		$defaults = [
			'screenName' => null,
			'userId' => null,
			'tweetCount' => 5,
			'header' => true,
			'avatars' => true,
			'retweets' => true,
			'replies' => true,
			'intents' => true,
		];

		$this->config = $config + $defaults;

		if (!isset($this->config['user_id']) && !isset($this->config['screen_name'])) {
			throw new InvalidStateException('No screenName/userId specified.');
		}
		$this->loader = $loader;
	}

	/**
	 * Render with predefined config.
	 * @param array $config Config overrides
	 * @return void
	 */
	public function render(array $config = null)
	{
		if ($config !== null) {
			$this->config = $config + $this->config;
		}
		$this->doRender();
	}

	/**
	 * Render full control (header, avatars, retweets, replies, intents)
	 * @param array $config Config overrides
	 * @return void
	 */
	public function renderFull(array $config = null)
	{
		$overrides = [
			'header' => true,
			'avatars' => true,
			'retweets' => true,
			'replies' => true,
			'intents' => true,
		];
		$this->config = $overrides + (array) $this->config;

		if ($config !== null) {
			$this->config = $config + $this->config;
		}
		$this->doRender();
	}

	/**
	 * Render medium control (avatars, retweets, replies; no header, no intents)
	 * @param array $config Config overrides
	 * @return void
	 */
	public function renderMedium(array $config = null)
	{
		$overrides = [
			'header' => false,
			'avatars' => true,
			'retweets' => true,
			'replies' => true,
			'intents' => false,
		];
		$this->config = $overrides + $this->config;

		if ($config !== null) {
			$this->config = $config + $this->config;
		}
		$this->doRender();
	}

	/**
	 * Render minimal control (replies, retweets; no header, no avatars, no intents)
	 * @param array $config Config overrides
	 * @return void
	 */
	public function renderMinimal(array $config = null)
	{
		$overrides = [
			'header' => false,
			'avatars' => false,
			'retweets' => true,
			'replies' => true,
			'intents' => false,
		];
		$this->config = $overrides + $this->config;

		if ($config !== null) {
			$this->config = $config + $this->config;
		}
		$this->doRender();
	}

	/**
	 * Get the tweet loader.
	 * @return ILoader;
	 */
	public function getLoader()
	{
		if ($this->loader === null) {
			$this->loader = new StandardLoader;
		}

		return $this->loader;
	}

	/**
	 * Set the tweet loader.
	 * @param ILoader $loader
	 * @return void
	 */
	public function setLoader(ILoader $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * Get the tweet formatter.
	 * @return IFormatter
	 */
	public function getFormatter()
	{
		if ($this->formatter === null) {
			$this->formatter = new StandardFormatter;
		}

		return $this->formatter;
	}

	/**
	 * Set the tweet formatter.
	 * @param IFormatter $formatter
	 * @return void
	 */
	public function setFormatter(IFormatter $formatter)
	{
		$this->formatter = $formatter;
	}

	/**
	 * Get the template file name.
	 * @return string
	 */
	public function getTemplateFile()
	{
		return dirname((new \ReflectionClass($this))->getFileName())
		. static::$templateDirectory
		. $this->templateFile;
	}

	/**
	 * @param string $filename
	 * @return $this
	 */
	public function setTemplateFile($filename)
	{
		$this->templateFile = $filename;

		return $this;
	}

	/**
	 * Render the component.
	 * @return void
	 */
	protected function doRender()
	{
		$this->template->setFile($this->getTemplateFile());
		$this->template->config = (object) $this->config;
		$this->template->getLatte()->addFilter('timeAgo', 'Helpers::timeAgoInWords');
		ob_start();
		try {
			$this->template->tweets = $this->getLoader()->getTweets($this->config);
			$this->template->render();
			ob_end_flush();
		} catch (TwitterException $e) {
			if (Debugger::$productionMode) {
				Debugger::log($e, Debugger::WARNING);
				ob_end_clean();
			} else {
				throw $e;
				ob_end_flush();
			}
		}
	}

	/**
	 * Custom helpers registration.
	 * @param string $class
	 * @return \Nette\Bridges\ApplicationLatte\Template
	 */
	protected function createTemplate($class = null)
	{
		/** @var Nette\Bridges\ApplicationLatte\Template $template */
		$template = parent::createTemplate($class);

		$formatter = $this->getFormatter();
		$latte = $template->getLatte();
		$latte->addFilter('avatar', function ($url) {
			return str_replace('_normal.', '_reasonably_small.', $url);
		});
		$latte->addFilter('tweetFormat', [$formatter, 'formatTweet']);
		$latte->addFilter('timeFormat', [$formatter, 'formatTime']);
		$latte->addFilter('userLink', [$formatter, 'formatUserUrl']);
		$latte->addFilter('intentLink', [$formatter, 'formatIntentUrl']);

		return $template;
	}

}

class TwitterException extends \Exception
{

}
