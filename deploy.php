<?php

namespace Deployer;

use TijsVerkoyen\DeployerSumo\Utility\Configuration;
use TijsVerkoyen\DeployerSumo\Utility\Database;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

require 'recipe/symfony.php';
require 'contrib/cachetool.php';
require __DIR__ . '/vendor/tijsverkoyen/deployer-sumo/sumo.php';

// Define some variables
set('client', '');
set('project', '');
set('repository', '');
set('production_url', '');
set('production_user', '');
set('php_version', '8.2');

// Define staging
host('dev03.sumocoders.eu')
    ->setRemoteUser('sites')
    ->set('labels', ['stage' => 'staging'])
    ->set('deploy_path', '~/apps/{{client}}/{{project}}')
    ->set('branch', 'staging')
    ->set('bin/php', '{{php_binary}}')
    ->set('cachetool', '/var/run/php_{{php_version_numeric}}_fpm_sites.sock')
    ->set('document_root', '~/php{{php_version_numeric}}/{{client}}/{{project}}')
    ->set('writable_mode', 'chmod');

// Define production
host('apache11.websrv.be')
    ->setRemoteUser('{{production_user}}')
    ->set('labels', ['stage' => 'production'])
    ->setPort(2244)
    ->set('deploy_path', '~/wwwroot')
    ->set('branch', 'master')
    ->set('bin/php', '{{php_binary}}')
    ->set('document_root', '~/wwwroot/www')
    ->set('http_user', '{{production_user}}')
    ->set('writable_mode', 'chmod');

/*************************
 * No need to edit below *
 *************************/

set('php_binary', function () {
    return 'php' . get('php_version');
});

set('php_version_numeric', function () {
    return (int) filter_var(get('bin/php'), FILTER_SANITIZE_NUMBER_INT);
});

set('use_relative_symlink', false);

// Shared files/dirs between deploys
add('shared_files', ['.env.local']);
add('shared_dirs', ['public/files', 'public/media']);

// Writable dirs by web server
add('writable_dirs', ['public/files', 'public/media', 'var', 'var/cache', 'var/log', 'var/sessions']);

// Disallow stats
set('allow_anonymous_stats', false);

set('composer_options', '--verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader');

/*
 * Composer
 * Deployer also has this download&run snippet in its core, but they only use it
 * when a global `composer` option isn't available. We always want to use the
 * phar version from shared so we overwrite the default behaviour here.
 */
set('shared_folder', '{{deploy_path}}/shared');
set('bin/composer', function () {
    if (!test('[ -f {{shared_folder}}/composer.phar ]')) {
        run("cd {{shared_folder}} && curl -sLO https://getcomposer.org/download/latest-stable/composer.phar");
    }
    return '{{bin/php}} {{shared_folder}}/composer.phar';
});

set('keep_releases', 3);

// Upload the assets
desc('Uploads the assets');
task(
    'sumo:assets:upload',
    function () {
        upload('public/assets', '{{release_path}}/public');
    }
);

desc('Ask if the .env.local is filled in correctly');
task('sumo:check:env', function () {
    askConfirmation('Is the .env.local filled in correctly?');
});

$databaseUtility = new Database();

desc('Replace the local database with the remote database');
task(
    'sumo:db:get',
    function () use ($databaseUtility) {
        $remoteHost = Configuration::fromRemote()->get('FORK_DATABASE_HOST');
        $remotePort = Configuration::fromRemote()->get('FORK_DATABASE_PORT');
        $remoteName = Configuration::fromRemote()->get('FORK_DATABASE_NAME');
        $remoteUser = Configuration::fromRemote()->get('FORK_DATABASE_USER');
        $remotePassword = Configuration::fromRemote()->get('FORK_DATABASE_PASSWORD');

        $localHost = Configuration::fromLocal()->get('FORK_DATABASE_HOST');
        $localPort = Configuration::fromLocal()->get('FORK_DATABASE_PORT');
        $localName = Configuration::fromLocal()->get('FORK_DATABASE_NAME');
        $localUser = Configuration::fromLocal()->get('FORK_DATABASE_USER');
        $localPassword = Configuration::fromLocal()->get('FORK_DATABASE_PASSWORD');

        $remoteDatabaseUrl = parse_url("mysql://{$remoteUser}:{$remotePassword}@{$remoteHost}:{$remotePort}/{$remoteName}?serverVersion=5.7&charset=utf8mb4");
        $localDatabaseUrl = parse_url("mysql://{$localUser}:{$localPassword}@{$localHost}:{$localPort}/{$localName}?serverVersion=5.7&charset=utf8mb4");

        run(
            sprintf(
                'mysqldump --lock-tables=false --set-charset %1$s %2$s > {{deploy_path}}/db_download.tmp.sql',
                $databaseUtility->getConnectionOptions($remoteDatabaseUrl),
                $databaseUtility->getNameFromConnectionOptions($remoteDatabaseUrl)
            )
        );
        download(
            '{{deploy_path}}/db_download.tmp.sql',
            './db_download.tmp.sql'
        );
        run('rm {{deploy_path}}/db_download.tmp.sql');

        runLocally(
            sprintf(
                'mysql %1$s %2$s < ./db_download.tmp.sql',
                $databaseUtility->getConnectionOptions($localDatabaseUrl),
                $databaseUtility->getNameFromConnectionOptions($localDatabaseUrl)
            )
        );
        runLocally('rm ./db_download.tmp.sql');
    }
);

desc('Replace the remote database with the local database');
task(
    'sumo:db:put',
    function () use ($databaseUtility) {
        $remoteHost = Configuration::fromRemote()->get('FORK_DATABASE_HOST');
        $remotePort = Configuration::fromRemote()->get('FORK_DATABASE_PORT');
        $remoteName = Configuration::fromRemote()->get('FORK_DATABASE_NAME');
        $remoteUser = Configuration::fromRemote()->get('FORK_DATABASE_USER');
        $remotePassword = Configuration::fromRemote()->get('FORK_DATABASE_PASSWORD');

        $localHost = Configuration::fromLocal()->get('FORK_DATABASE_HOST');
        $localPort = Configuration::fromLocal()->get('FORK_DATABASE_PORT');
        $localName = Configuration::fromLocal()->get('FORK_DATABASE_NAME');
        $localUser = Configuration::fromLocal()->get('FORK_DATABASE_USER');
        $localPassword = Configuration::fromLocal()->get('FORK_DATABASE_PASSWORD');

        $remoteDatabaseUrl = parse_url("mysql://{$remoteUser}:{$remotePassword}@{$remoteHost}:{$remotePort}/{$remoteName}?serverVersion=5.7&charset=utf8mb4");
        $localDatabaseUrl = parse_url("mysql://{$localUser}:{$localPassword}@{$localHost}:{$localPort}/{$localName}?serverVersion=5.7&charset=utf8mb4");

        // create a backup
        // @todo make separate backup dir
        run(
            sprintf(
                'mysqldump --lock-tables=false --set-charset %1$s %2$s > {{deploy_path}}/backup_%3$s.sql',
                $databaseUtility->getConnectionOptions($remoteDatabaseUrl),
                $databaseUtility->getNameFromConnectionOptions($remoteDatabaseUrl),
                date('YmdHi')
            )
        );

        runLocally(
            sprintf(
                'mysqldump --column-statistics=0 --lock-tables=false --set-charset %1$s %2$s > ./db_upload.tmp.sql',
                $databaseUtility->getConnectionOptions($localDatabaseUrl),
                $databaseUtility->getNameFromConnectionOptions($localDatabaseUrl)
            )
        );

        upload('./db_upload.tmp.sql', '{{deploy_path}}/db_upload.tmp.sql');
        runLocally('rm ./db_upload.tmp.sql');

        run(
            sprintf(
                'mysql %1$s %2$s < {{deploy_path}}/db_upload.tmp.sql',
                $databaseUtility->getConnectionOptions($remoteDatabaseUrl),
                $databaseUtility->getNameFromConnectionOptions($remoteDatabaseUrl)
            )
        );
        run('rm {{deploy_path}}/db_upload.tmp.sql');
    }
);

desc('Cleanup the codebase');
task('sumo:files:cleanup', function () {
    run('rm -rf {{release_path}}/.github');
    run('rm -rf {{release_path}}/.git');
    run('rm -rf {{release_path}}/.gitattributes');
    run('rm -rf {{release_path}}/Dockerfile');
    run('rm -rf {{release_path}}/docker-compose.yml');
    run('rm -rf {{release_path}}/.scrutinizer.yml');
    run('rm -rf {{release_path}}/.codecov.yml');
    run('rm -rf {{release_path}}/php.ini');
    run('rm -rf {{release_path}}/phpunit.xml.dist');
    run('rm -rf {{release_path}}/phpstan.neon');
    run('rm -rf {{release_path}}/UPGRADE***');
    run('rm -rf {{release_path}}/var/docks');
    run('rm -rf {{release_path}}/var/docker');
    run('rm -rf {{release_path}}/.gitlab-ci');
    run('rm -rf {{release_path}}/.phpcs.xml.dist');
});

/**********************
 * Flow configuration *
 **********************/
// Clear the Opcache
after('deploy:symlink', 'cachetool:clear:opcache');
// Unlock the deploy when it failed
after('deploy:failed', 'deploy:unlock');
// Check config before migrating database
before('database:migrate', 'sumo:check:env');
// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');
// Remove unneeded files
before('deploy:symlink', 'sumo:files:cleanup');
