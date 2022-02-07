<?php
namespace Justuno\Core\Sentry;
# 2020-08-13 "Port the `Df\Sentry\Context` class" https://github.com/justuno-com/core/issues/171
class Context {
	/**
	 * 2020-06-27
	 * @used-by \Justuno\Core\Sentry\Client::__construct()
	 */
	function __construct()
	{
		$this->clear();
	}

	/**
	 * Clean up existing context.
	 */
	function clear()
	{
		$this->tags = [];
		$this->extra = [];
		$this->user = null;
	}

	/**
	 * 2017-01-10
	 * @used-by clear()
	 * @used-by \Justuno\Core\Sentry\Client::capture()
	 * @used-by \Justuno\Core\Sentry\Client::extra_context()
	 * @var array(string => mixed)
	 */
	public $extra;
	/**
	 * 2017-01-10
	 * @used-by clear()
	 * @used-by \Justuno\Core\Sentry\Client::capture()
	 * @used-by \Justuno\Core\Sentry\Client::tags()
	 * @var array(string => string)
	 */
	public $tags;
	/**
	 * 2017-01-10
	 * @used-by clear()
	 * @used-by \Justuno\Core\Sentry\Client::get_user_data()
	 * @used-by \Justuno\Core\Sentry\Client::user()
	 * @var array(string => mixed)
	 */
	public $user;
}
