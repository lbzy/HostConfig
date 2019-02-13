######如何使用
>composer require lbzy/host-config
```PHP
try {
	// 配置根域名key
	define('ENV_TEST', 'test');
	define('ENV_ONLINE', 'online');
	// 配置根域名
	define('ROOT_HOST_LIST', [
		ENV_ONLINE 	=> ['test1.com', 'test2.com'],
		ENV_TEST 	=> 'localhost',
	]);
	// 解析
	$hostInstance 	= \Lbzy\HostConfig\Host::instance(ROOT_HOST_LIST);
	// define('HOST', $hostInstance->getHost());
	// define('ROOT_HOST', $hostInstance->getRootHost());
	// define('ROOT_HOST_KEY', $hostInstance->getRootHostKey());
	// 获得对应的根域名key
	// 如果访问根域名为test1.com或test2.com的地址,返回 online
	// 如果访问根域名为localhost的地址,返回 test
	$rootHostKey = $hostInstance->getRootHostKey();
	// 加载对应配置
	$configFile 	= 'yourDir' . $rootHostKey . '.php';
	if (file_exists($configFile)) {
		// 你的代码
		// define('ENV_CONFIG', require $configFile);
	} else {
		die('config file not exists.');
	}
} catch (\Throwable $e) {
	die('config error.');
}
```
