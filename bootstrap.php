<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/*
 * Define services to be used throughout Fork CMS.
 *
 * @author Dieter Vanden Eynde <dieter@dieterve.be>
 */

use Symfony\Component\DependencyInjection\ContainerBuilder;

$container = new ContainerBuilder();

// database
$container->setParameter('database.type', DB_TYPE);
$container->setParameter('database.hostname', DB_HOSTNAME);
$container->setParameter('database.port', DB_PORT);
$container->setParameter('database.username', DB_USERNAME);
$container->setParameter('database.password', DB_PASSWORD);
$container->setParameter('database.database', DB_DATABASE);

$container->register('database', 'SpoonDatabase')
          ->addArgument('%database.type%')
          ->addArgument('%database.hostname%')
          ->addArgument('%database.username%')
          ->addArgument('%database.password%')
          ->addArgument('%database.database%')
          ->addArgument('%database.port%');
$container->get('database')->execute('SET CHARACTER SET utf8, NAMES utf8, time_zone = "+0:00"');
