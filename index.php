<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

// CLI/Nginx/Cron: We need to set the "current working directory" to this folder
chdir(__DIR__);

// vendors not installed
if (!is_dir(__DIR__ . '/vendor')) {
    echo 'Your install is missing some dependencies. If you have composer
         installed you should run: <code>composer install</code>. If you
         don\'t have composer installed you really should, see
         http://getcomposer.org for more information';

    return;
}

require_once __DIR__ . '/autoload.php';

use ForkCMS\App\AppKernel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

// get environment and debug mode from environment variables
$env = getenv('FORK_ENV') ?: 'prod';
$debug = getenv('FORK_DEBUG') === '1';

// Fork has not yet been installed
$parametersFile = __DIR__ . '/app/config/parameters.yml';
$request = Request::createFromGlobals();
if (!file_exists($parametersFile)) {
    $env = 'install';
    if (strpos($request->getRequestUri(), '/install') !== 0) {
        // check .htaccess
        if (!$request->query->has('skiphtaccess') && !file_exists('.htaccess')) {
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

if (extension_loaded('newrelic')) {
    newrelic_name_transaction(strtok($request->getRequestUri(), '?'));
}

if ($debug) {
    Debug::enable();
}

$kernel = new AppKernel($env, $debug);
$response = $kernel->handle($request);
if ($response->getCharset() === null && $kernel->getContainer() instanceof ContainerInterface) {
    $response->setCharset(
        $kernel->getContainer()->getParameter('kernel.charset')
    );
}
$response->send();
$kernel->terminate($request, $response);
