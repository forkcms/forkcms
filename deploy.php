<?php

namespace Deployer;

use Symfony\Component\Yaml\Yaml;
use TijsVerkoyen\DeployerSumo\Utility\Path;

require 'recipe/symfony.php';
require 'contrib/cachetool.php';
require __DIR__ . '/vendor/tijsverkoyen/deployer-sumo/sumo.php';

// Define some variables
set('client', '$client');
set('project', '$project');
set('repository', '$repository');
set('user', '$user');
set('production_url', '$productionUrl');
set('sentry_organization', '$sentryOrganization');
set('sentry_project_slug', '$sentryProjectSlug');
set('sentry_token', '$sentryToken');
set('symfony_env', 'prod');
set('writable_recursive', true);

// Returns Composer binary path in found. Otherwise try to install latest
// composer version to `.dep/composer.phar`. To use specific composer version
// download desired phar and place it at `.dep/composer.phar`.
set('bin/composer', function () {
    if (test('[ -f {{deploy_path}}/.dep/composer.phar ]')) {
        return '{{bin/php}} {{deploy_path}}/.dep/composer.phar';
    }

    if (commandExist('composer')) {
        return '{{bin/php}} ' . locateBinaryPath('composer');
    }

    writeln("Composer binary wasn't found. Installing latest composer to \"{{deploy_path}}/.dep/composer.phar\".");
    run("cd {{deploy_path}} && curl -sS https://getcomposer.org/installer | {{bin/php}}");
    run('mv {{deploy_path}}/composer.phar {{deploy_path}}/.dep/composer.phar');
    return '{{bin/php}} {{deploy_path}}/.dep/composer.phar';
});

// Define staging
host('dev03.sumocoders.eu')
    ->user('sites')
    ->stage('staging')
    ->set('deploy_path', '~/apps/{{client}}/{{project}}')
    ->set('branch', 'staging')
    ->set('bin/php', 'php7.4')
    ->set('cachetool', '/var/run/php_74_fpm_sites.sock')
    ->set('document_root', '~/php74/{{client}}/{{project}}')
    ->set('keep_releases', 3);

// Define production
//host('$host')
//    ->user('{{user}}')
//    ->stage('production')
//    ->port(2244)
//    ->set('http_user', '{{user')
//    ->set('writable_mode', 'chmod')
//    ->set('deploy_path', '~/wwwroot')
//    ->set('branch', 'master')
//    ->set('bin/php', '$phpBinary')
//    ->set('bin/cachetool', '/data/vhosts/{{user}}/wwwroot/shared/cachetool-7.0.0.phar')
//    ->set('cachetool', '/data/vhosts/{{user}}/.sock/$sockFile --tmp-dir=/data/vhosts/{{user}}/.temp')
//    ->set('document_root', '~/wwwroot/www')
//    ->set('keep_releases', 3);

/*************************
 * No need to edit below *
 *************************/

// only run clearing the cache on staging, as production does not use PHP FPM.
task('cachetool:clear:opcache')->select('staging');

set('use_relative_symlink', false);

// Shared files/dirs between deploys
add('shared_files', ['.env.local', 'app/config/parameters.yml']);
add('shared_dirs', ['src/Frontend/Files']);

// Writable dirs by web server
add('writable_dirs', [
    'src/Frontend/Cache',
    'src/Backend/Cache',
    'src/Frontend/Files',
    'var/cache',
    'var/logs'
]);

// Disallow stats
set('allow_anonymous_stats', false);

set('shared_folder', '{{deploy_path}}/shared');

// composer
set('bin/composer', function () {
    if (!test('[ -f {{shared_folder}}/composer.phar ]')) {
        run("cd {{shared_folder}} && curl -sLO https://getcomposer.org/download/latest-stable/composer.phar");
    }
    return '{{bin/php}} {{shared_folder}}/composer.phar';
});

set('composer_options', '--verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader');

// Sentry
set(
    'sentry',
    [
        'organization' => get('sentry_organization'),
        'projects' => [
            get('sentry_project_slug'),
        ],
        'token' => get('sentry_token')
    ]
);

// Limit the number of releases that should be kept
set('keep_releases', 3);


/*****************
 * Task sections *
 *****************/
// Overrides from symfony and deployer-sumo
/**
 * Install assets from public dir of bundles
 * @Override from symfony.php - don't know if we need this for Fork
 */
task(
    'deploy:assets:install',
    function () {
        // do nothing
    }
)->desc('Generate and upload bundle assets');

/**
 * Migrate database
 * @Override from symfony.php which executes doctrine:migrations
 */
task('database:migrate', function () {
    // do nothing - delete when we start using doctrine:migrations
})->desc('Migrate database');

/**
 * @Override from deployer-sumo/assets
 */
task('sumo:assets:install', function() {
    // do nothing - no public directory in fork
})->desc('Install bundle\'s web assets under a public directory');

desc('Run the build script which will build our needed assets.');
task(
    'sumo:assets:fix-node-version',
    function () {
        $nvmPath = trim(shell_exec('echo $HOME/.nvm/nvm.sh'));

        if (!file_exists($nvmPath)) {
            writeln('Nvm not found on local system. Aborting');
            return;
        }

        $nvmRcFile = '.nvmrc';

        // If there is no .nvmrc file, stop
        if (!file_exists($nvmRcFile)) {
            writeln('No .nvmrc file found. Aborting.');
            return;
        }

        writeln(runLocally('. ' . $nvmPath . ' && nvm install'));
    }
);

task('sumo:assets:build', function () {
});

task('sumo:assets:upload', function () {
});

task(
    'deploy:theme:build',
    function () {
        $packageFile = file_get_contents('package.json');
        $package = json_decode($packageFile, true);

        if (!array_key_exists('theme', $package)) {
            writeln(
                [
                    '<comment>No theme found in package.json</comment>',
                ]
            );

            return;
        }

        if (commandExist('nvm')) {
            runLocally('nvm install');
            runLocally('nvm exec node_modules/.bin/gulp build');
        } else {
            runLocally('node_modules/.bin/gulp build');
        }
    }
)->desc('Generate bundle assets');
before('deploy:theme:build', 'sumo:assets:fix-node-version');
after('deploy:update_code', 'deploy:theme:build');

// Upload tasks
task(
    'deploy:theme:upload',
    function () {
        $packageFile = file_get_contents('package.json');
        $package = json_decode($packageFile, true);

        if (!array_key_exists('theme', $package)) {
            writeln(
                [
                    '<comment>No theme found in package.json</comment>',
                ]
            );

            return;
        }

        $theme = $package['theme'];
        $remotePath = '{{release_path}}/src/Frontend/Themes/' . $theme . '/Core';

        upload(__DIR__ . '/src/Frontend/Themes/' . $theme . '/Core/', $remotePath);
     }
)->desc('Upload bundle assets');
after('deploy:theme:build', 'deploy:theme:upload');

task(
    'fork:cache:clear',
    function() {
        run('{{bin/console}} fork:cache:clear --env={{symfony_env}}');
        run('if [ -f {{deploy_path}}/shared/config/parameters.yml ]; then touch {{deploy_path}}/shared/config/parameters.yml; fi');
    }
)
    ->desc('Clear Fork CMS cache');
before('deploy:cache:clear', 'fork:cache:clear');

// Migrations
task('database:migrations:run', function () {
    if (!test('[ -d {{release_path}}/migrations/ ]')) {
        return;
    }

    cd('{{deploy_path}}/shared/');

    if (!test('[ -f database_migrations ]')) {
        run('touch database_migrations');
    }

    $parameters = Yaml::parse(run('cat app/config/parameters.yml'))['parameters'];
    $executedMigrations = explode("\n", run('cat database_migrations'));

    cd('{{release_path}}/migrations/');

    $dirs = explode(',', run('find {{release_path}}/migrations/* -maxdepth 0 -type d | tr "\n" ","'));
    foreach ($dirs as $dir) {
        if (empty($dir)) {
            continue;
        }

        $shortName = basename($dir);
        if (in_array($shortName, $executedMigrations)) {
            continue;
        }

        if (test('[ -f ' . $shortName . '/update.sql ]')) {
            writeln('<comment>Running update.sql for ' . $shortName . '</comment>');

            run('mysql --default-character-set="utf8" --host=' . $parameters['database.host'] . ' --port=' . $parameters['database.port'] . ' --user=' . $parameters['database.user'] . ' --password=' . $parameters['database.password'] . ' ' . $parameters['database.name'] . ' < ' . $shortName . '/update.sql');
        }

        if (test('[ -f ' . $shortName . '/update.php ]')) {
            writeln('<comment>Running update.php for ' . $shortName . '</comment>');

            run('cd {{release_path}} && {{bin/php}} ' . $dir . '/update.php');
        }

        run('echo ' . $shortName . ' | tee -a {{deploy_path}}/shared/database_migrations');
    }

    // remove DB backup
    run('rm {{deploy_path}}/mysql_backup.sql');
})->desc('Run database migrations');
before('fork:cache:clear', 'database:migrations:run');

task('locale:migrations:run', function () {
    if (!test('[ -d {{release_path}}/migrations/ ]')) {
        return;
    }

    cd('{{deploy_path}}/shared/');

    if (!test('[ -f locale_migrations ]')) {
        run('touch locale_migrations');
    }

    $executedMigrations = explode("\n", run('cat locale_migrations'));

    cd('{{release_path}}/migrations/');

    $dirs = explode(',', run('find {{release_path}}/migrations/* -maxdepth 0 -type d | tr "\n" ","'));
    foreach ($dirs as $dir) {
        if (empty($dir)) {
            continue;
        }

        $shortName = basename($dir);
        if (in_array($shortName, $executedMigrations)) {
            continue;
        }

        if (test('[ -f ' . $shortName . '/locale.xml ]')) {
            writeln('<comment>Installing locale.xml for ' . $shortName . '</comment>');

            run('{{bin/console}} forkcms:locale:import -f ' . $dir . '/locale.xml --env={{symfony_env}}');
        }

        run('echo ' . $shortName . ' | tee -a {{deploy_path}}/shared/locale_migrations');
    }
})->desc('Run locale migrations');
before('database:migrations:run', 'locale:migrations:run');

task(
    'database:backup',
    function () {
        cd('{{deploy_path}}/shared/');
        $parameters = Yaml::parse(run('cat app/config/parameters.yml'))['parameters'];

        run('mysqldump --skip-lock-tables --default-character-set="utf8" --host=' . $parameters['database.host'] . ' --port=' . $parameters['database.port'] . ' --user=' . $parameters['database.user'] . ' --password=' . $parameters['database.password'] . ' ' . $parameters['database.name'] . ' > {{deploy_path}}/mysql_backup.sql');
    }
)->desc('Create a backup of the database');
before('database:migrations:run', 'database:backup');

$pathUtility = new Path();

desc('Symlink the document root to the public folder');
task(
    'sumo:symlink:document-root',
    function () use ($pathUtility) {
        if (!get('document_root', false)) {
            return;
        }

        $publicPath = get('deploy_path') . '/current/';
        $currentSymlink = run(
            'if [ -L {{document_root}} ]; then readlink {{document_root}}; fi'
        );

        // already linked, so we can stop here
        if ($currentSymlink === $pathUtility->expandPath($publicPath)) {
            return;
        }

        // Show a warning when the document root exists. So we don't overwrite
        // existing stuff.
        if ($currentSymlink === '' && test('[ -e {{document_root}} ]')) {
            writeln(
                [
                    '<comment>Document root already exists</comment>',
                    '<comment>To link it, issue the following command:</comment>',
                    sprintf(
                        '<comment>ln -sf %1$s %2$s</comment>',
                        $publicPath,
                        get('document_root')
                    ),
                ]
            );
            return;
        }

        run(sprintf('mkdir -p %1$s', dirname(get('document_root'))));
        run('rm -f {{document_root}}');
        run(sprintf('{{bin/symlink}} %1$s {{document_root}}', $publicPath));
    }
);

/**********************
 * Flow configuration *
 **********************/
// Clear the Opcache
after('deploy:symlink', 'cachetool:clear:opcache');
// Unlock the deploy when it failed
after('deploy:failed', 'deploy:unlock');
// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');
