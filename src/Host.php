<?php
namespace Lbzy\HostConfig;

class Host
{
	# 当前实例
	private static $instance;
	# 当前的域名
	private static $host;
	# 解析得到的根域名
	private static $rootHost;
	# 根域名列表
	private static $rootHostList;
	# 解析得到根域名对应key
	private static $rootHostKey;

	private function __construct()
	{
		self::check();
		self::parseHost();
	}

	/**
	 * 获得实例
	 * @author Lbzy
	 * @DateTime 2019-02-13T13:50:51+0800
	 * @param    array                    $rootHostList [根域名列表]
	 * @return \Lbzy\HostConfig\Host
	 */
	static function instance(array $rootHostList = [])
	{
		if (! self::$instance) {
			self::$host 		= $_SERVER['HTTP_X_REAL_HOST'] ?? ($_SERVER['HTTP_HOST'] ?? '');
			self::$rootHostList = $rootHostList;
			self::$instance 	= new static;
		}
		return self::$instance;
	}

	/**
	 * 检测环境
	 * @author Lbzy
	 * @DateTime 2019-02-13T13:52:03+0800
	 * @throws  \Lbzy\HostConfig\HostException
	 * @return
	 */
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

	
	/**
	 * 解析域名
	 * @author Lbzy
	 * @DateTime 2019-02-13T13:54:25+0800
	 * @throws  \Lbzy\HostConfig\HostException
	 * @return 
	 */
	private static function parseHost()
	{
		
		foreach (self::$rootHostList as $rootHostKey => $rootHostValue) {
			# 字符串形式
			if (is_string($rootHostValue)) {
				if (self::isRootHost($rootHostValue)) {
					self::$rootHostKey 	= $rootHostKey;
					self::$rootHost 	= $rootHostValue;
					return;
				}
			}
			# 数组形式
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
		
		throw new HostException('Root host not found.');
	}

	/**
	 * 检测是否是根域名
	 * @author Lbzy
	 * @DateTime 2019-02-13T13:53:39+0800
	 * @param    string                   $checkHost [被检测域名]
	 * @return   boolean
	 */
	private static function isRootHost(string $checkHost)
	{
		$checkHostLength = mb_strlen($checkHost);
		switch (mb_strlen(self::$host) <=> $checkHostLength) {
			case 1:
				return mb_substr(self::$host, -1 - $checkHostLength, null, 'UTF-8') == ('.' . $checkHost);
				break;
			case 0:
				return self::$host == $checkHost;
				break;
			case -1:
				return false;
				break;
			default:
				# code...
				break;
		}
	}

	/**
	 * 获取解析到的域名
	 * @author Lbzy
	 * @DateTime 2019-02-13T13:55:03+0800
	 * @return string
	 */
	function getHost()
	{
		return self::$host;
	}

	/**
	 * 获取根域名
	 * @author Lbzy
	 * @DateTime 2019-02-13T13:55:23+0800
	 * @return string
	 */
	function getRootHost()
	{
		return self::$rootHost;
	}

	/**
	 * 获取根域名对应的key
	 * @author Lbzy
	 * @DateTime 2019-02-13T13:56:00+0800
	 * @return string
	 */
	function getRootHostKey()
	{
		return self::$rootHostKey;
	}

}