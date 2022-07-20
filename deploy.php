<?php

namespace Deployer;

require_once __DIR__.'/vendor/autoload.php';

import('recipe/laravel.php');
import('contrib/npm.php');

/*
 |-------------------------------------------------------------------
 | Setup Deployment Variables
 | -------------------------------------------------------------------
 | This will fetch the deployment variables from your .env file.
 | It will then make sure that all required variables are set in
 | order to run the deployment.
 */
with(\Dotenv\Dotenv::createImmutable(__DIR__))->load();

set('deployment_host', $_SERVER['DEPLOY_HOST']);
set('deployment_port', $_SERVER['DEPLOY_PORT']);
set('deployment_user', $_SERVER['DEPLOY_USER']);
set('deployment_path', $_SERVER['DEPLOY_PATH']);
set('deployment_repository', $_SERVER['DEPLOY_REPOSITORY']);

checkEnvVariablesAreSet();

// Project repository
set('repository', get('deployment_repository'));

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Turn on SSH multiplexing to speed up execution
set('ssh_multiplexing', true);

// Write Mode needs to be chmod.
set('writable_mode', 'chmod');

// Set composer path
set('bin/composer', 'composer');

// Shared files/dirs between deploys
//add('shared_files', []);
//add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', [
    'storage/app/backups',
]);

// Hosts
host(get('deployment_host'))
    ->setPort(get('deployment_port', 22))
    ->setRemoteUser(get('deployment_user'))
    ->setDeployPath(get('deployment_path'));

/*
 |-------------------------------------------------------------------
 | Tasks
 | -------------------------------------------------------------------
 | These are some custom deployment tasks, such as backing up the
 | database, overriding the default migrate, terminating horizon,
 | and building npm assets.
 */

//desc('Execute artisan clear-compiled');
//task('artisan:clear_compiled', artisan('clear-compiled'));

desc('Run artisan backup:run');
task('artisan:backup:run', artisan('backup:run', ['skipIfNoEnv']));

desc('Run artisan websockets:serve');
task('artisan:websockets:serve', artisan('websockets:serve', ['skipIfNoEnv']));

desc('Execute npm run development');
task('npm:run:development', function () {
    run('cd {{release_path}} && {{bin/npm}} run development');
});

desc('Execute npm run production');
task('npm:run:production', function () {
    run('cd {{release_path}} && {{bin/npm}} run production');
});

/*
 |-------------------------------------------------------------------
 | Deploy tasks
 | -------------------------------------------------------------------
 | Add the additional tasks to the deployment flow.
 */

// Run additional artisan commands
//after('artisan:storage:link', 'artisan:clear_compiled');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Main deploy task
desc('Deploy your project');
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'npm:install',
    'npm:run:production',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:cache:clear',
    'artisan:route:cache',
    'artisan:backup:run',
    'artisan:migrate',
    'deploy:publish',
    'artisan:horizon:terminate',
    //    'artisan:websockets:serve',
]);

/*
 |-------------------------------------------------------------------
 | Helper methods
 | -------------------------------------------------------------------
 | These are some helper methods to make sure they needed .env
 | variables are set and for showing error messages.
 */

function checkEnvVariablesAreSet()
{
    $notSet = false;
    if (empty(get('deployment_host'))) {
        errorMessage('The .env DEPLOY_HOST variable is not set.');
        $notSet = true;
    }

    if (empty(get('deployment_user'))) {
        errorMessage('The .env DEPLOY_USER variable is not set.');
        $notSet = true;
    }

    if (empty(get('deployment_path'))) {
        errorMessage('The .env DEPLOY_PATH variable is not set.');
        $notSet = true;
    }

    if (empty(get('deployment_repository'))) {
        errorMessage('The .env DEPLOY_REPOSITORY variable is not set.');
        $notSet = true;
    }

    if ($notSet) {
        exit;
    }
}

function errorMessage($message)
{
    echo "\e[1;31;mError: ".$message."\e[0m\n";
}
