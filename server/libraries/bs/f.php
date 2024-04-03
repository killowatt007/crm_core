<?php
defined('EXE') or die('Access');

/**
 * $version 1.1
 * $deps s:bs\libraries\mail\Mail v1.1
 */

class F
{
	private static $app = null;
	private static $fab = null;
	private static $mail = null;
	private static $helpers = [];

	public static function getDBO($name = null, $type = 'a')
	{
		include_once PATH_ROOT .'/libraries/bs/dbo/dbo.php';
		return new \bs\libraries\dbo\DBO($name, $type);
	}

	public static function getMail()
	{
		if (!self::$mail)
		{
			include_once PATH_ROOT .'/libraries/bs/mail/mail.php';
			self::$mail = new \bs\libraries\mail\Mail();
		}

		return self::$mail;
	}

	public static function getRegistry($data = null)
	{
		include_once PATH_ROOT .'/libraries/bs/registry/registry.php';
		return new \bs\libraries\registry\Registry($data);
	}

	public static function getApp()
	{
		if (!self::$app)
		{
			include PATH_ROOT.'/libraries/bs/app/app.php';
			self::$app = new \bs\libraries\app\App();
		}

		return self::$app;
	}

	public static function std($args)
	{
		$std = new stdClass;

		foreach ($args as $key => &$value) 
			$std->$key = &$value;

		return $std;
	}

	public static function getParams($data = [])
	{
		include_once PATH_ROOT .'/libraries/bs/params.php';
		return new \bs\libraries\Params($data);
	}

	public static function getHelper($name = 'helper')
	{
		if (!isset(self::$helpers[$name]))
		{
			include PATH_ROOT.'/libraries/bs/helper/arr.php';
			self::$helpers[$name] = new \bs\libraries\helper\Arr();
		}

		return self::$helpers[$name];
	}
}