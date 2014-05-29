<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = require __DIR__.'/../app/bootstrap.php.cache';

try {
	// Use APC for autoloading to improve performance
	$apcLoader = new ApcClassLoader('chitanka-rbe', $loader);
	$loader->unregister();
	$apcLoader->register(true);
} catch (\RuntimeException $e) {
	// APC not enabled
}

require __DIR__.'/../app/AppKernel.php';
//require __DIR__.'/../app/AppCache.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
