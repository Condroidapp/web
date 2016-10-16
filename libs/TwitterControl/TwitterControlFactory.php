<?php

namespace Smasty\Components\Twitter;

interface TwitterControlFactory
{

	/**
	 * @param array $config
	 * @return \Smasty\Components\Twitter\Control
	 */
	public function create(array $config);

}
