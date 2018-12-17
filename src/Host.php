<?php
namespace Lbzy\HostConfig;

class Host
{
	private static $instance;
	private static $host;
	private static $rootHost;
	private static $rootHostList;
	private static $rootHostKey;

	private function __construct()
	{
		self::check();
		self::parseHost();
	}

	static function instance(array $rootHostList = [])
	{
		if (! self::$instance) {
			self::$host 		= $_SERVER['HTTP_HOST'] ?? '';
			self::$rootHostList = $rootHostList;
			self::$instance 	= new static;
		}
		return self::$instance;
	}

	private static function check()
	{
		if (PHP_SAPI == 'cli') {
			throw new HostException('Can not running in cli.');
		}
		if (empty(self::$host)) {
			throw new HostException('Http host is empty.');
		}
		if (empty(self::$rootHostList)) {
			throw new HostException('Root host list is empty.');
		}
	}

	private static function isRootHost(string $hostString)
	{
		
		switch (mb_strlen(self::$host) <=> mb_strlen($hostString)) {
			case 1:
				return mb_substr(self::$host, -1 - mb_strlen($hostString), null, 'UTF-8') == ('.' . $hostString);
				break;
			case 0:
				return self::$host == $hostString;
				break;
			case -1:
				return false;
				break;
			default:
				# code...
				break;
		}

	}

	private static function parseHost()
	{
		
		foreach (self::$rootHostList as $rootHostKey => $rootHostValue) {
			
			if (is_string($rootHostValue)) {
				
				if (self::isRootHost($rootHostValue)) {
					self::$rootHostKey 	= $rootHostKey;
					self::$rootHost 	= $rootHostValue;
					return;
				}
			}

			if (is_array($rootHostValue)) {
				foreach ($rootHostValue as $rootHostVo) {
					
					if (! is_string($rootHostVo)) {
						throw new HostException('Root host list error.');
					}

					if (self::isRootHost($rootHostVo)){
						self::$rootHostKey 	= $rootHostKey;
						self::$rootHost 	= $rootHostVo;
						return;
					}
				}
			}

		}
		if (! self::$rootHost) {
			throw new HostException('Root host not found.');
		}
	}

	function getHost()
	{
		return self::$host;
	}

	function getRootHost()
	{
		return self::$rootHost;
	}

	function getRootHostKey()
	{
		return self::$rootHostKey;
	}

}