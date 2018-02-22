<?php

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

// @ForkCMS: vendors not installed
if (!is_dir(__DIR__ . '/../vendor')) {
    echo 'Your install is missing some dependencies. If you have composer
         installed you should run: <code>composer install</code>. If you
         don\'t have composer installed you really should, see
         http://getcomposer.org for more information';

    return;
}

// @ForkCMS: we changed the default from '/../vendor/autoload.php` to
require __DIR__.'/../autoload.php';

// The check is to ensure we don't use .env in production
if (!isset($_SERVER['APP_ENV'])) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }
    (new Dotenv())->load(__DIR__.'/../.env');
}

$env = $_SERVER['APP_ENV'] ?? 'dev';
$debug = $_SERVER['APP_DEBUG'] ?? ('prod' !== $env);

// @ForkCMS: Fork has not yet been installed
$parametersFile = __DIR__ . '/../config/parameters.yml';
$request = Request::createFromGlobals();
if (!file_exists($parametersFile)) {
    $env = 'install';
    if (strpos($request->getRequestUri(), '/install') !== 0) {
        // check .htaccess
        if (!$request->query->has('skiphtaccess') && !file_exists('../.htaccess')) {
            echo 'Your install is missing the .htaccess file. Make sure you show
                 hidden files while uploading Fork CMS. Read the article about
                 <a href="http://www.fork-cms.com/community/documentation/detail/installation/webservers">webservers</a>
                 for further information. <a href="?skiphtaccess">Skip .htaccess
                 check</a>';

            return;
        }

        header('Location: /install');

        return;
    }
}

// @ForkCMS
if (extension_loaded('newrelic')) {
    newrelic_name_transaction(strtok($request->getRequestUri(), '?'));
}

if ($debug) {
    umask(0000);

    Debug::enable();
}

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts(explode(',', $trustedHosts));
}

$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);

// @ForkCMS
if ($response->getCharset() === null && $kernel->getContainer() instanceof ContainerInterface) {
    $response->setCharset(
        $kernel->getContainer()->getParameter('kernel.charset')
    );
}

$response->send();
$kernel->terminate($request, $response);
