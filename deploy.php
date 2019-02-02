<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'recipe/npm.php';

/*
 |-------------------------------------------------------------------
 | Setup Deployment Variables
 | -------------------------------------------------------------------
 | This will fetch the deployment variables from your .env file.
 | It will then make sure that all required variables are set in
 | order to run the deployment.
 */
with(new \Dotenv\Dotenv(__DIR__))->load();

set('deployment_host', getenv('DEPLOY_HOST'));
set('deployment_user', getenv('DEPLOY_USER'));
set('deployment_path', getenv('DEPLOY_PATH'));
set('deployment_repository', getenv('DEPLOY_REPOSITORY'));
set('deployment_horizon', getenv('DEPLOY_HORIZON'));
set('deployment_websockets', getenv('DEPLOY_WEBSOCKETS'));

checkEnvVariablesAreSet();

// Project repository
set('repository', get('deployment_repository'));

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Turn on SSH multiplexing to speed up execution
set('ssh_multiplexing', true);

// Write Mode needs to be chmod.
set('writable_mode', 'chmod');

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', [
    'storage/app/backups',
]);


// Hosts

host(get('deployment_host'))
    ->user(get('deployment_user'))
    ->set('deploy_path', get('deployment_path'));

/*
 |-------------------------------------------------------------------
 | Tasks
 | -------------------------------------------------------------------
 | These are some custom deployment tasks, such as backing up the
 | database, overriding the default migrate, terminating horizon,
 | and building npm assets.
 */

desc('Execute artisan clear-compiled');
task('artisan:clear_compiled', function () {
    run('{{bin/php}} {{release_path}}/artisan clear-compiled');
});

desc('Execute artisan backup:run');
task('artisan:backup:run', function () {
    if (! test('[ -s {{release_path}}/.env ]')) {
        writeln("<fg=yellow;options=bold;>Warning: </><fg=yellow;>Your .env file is empty! Skipping...</>");
        return;
    }

    run('{{bin/php}} {{release_path}}/artisan backup:run');
});

// Override default artisan migrate command
desc('Execute artisan migrate');
task('artisan:migrate', function () {
    if (! test('[ -s {{release_path}}/.env ]')) {
        writeln("<fg=yellow;options=bold;>Warning: </><fg=yellow;>Your .env file is empty! Skipping...</>");
        return;
    }

    run('{{bin/php}} {{release_path}}/artisan migrate --force');
})->once();

desc('Execute artisan horizon:terminate');
task('artisan:horizon:terminate', function () {
    if (get('deployment_horizon') === true) {
        run('{{bin/php}} {{release_path}}/artisan horizon:terminate');
    }
});

desc('Execute artisan websockets:serve');
task('artisan:websockets:serve', function () {
    if (get('deployment_websockets') === true) {
        run('{{bin/php}} {{release_path}}/artisan websockets:serve');
    }
});


desc('Execute npm run development');
task('npm:development', '{{bin/npm}} run development');

desc('Execute npm run production');
task('npm:production', '{{bin/npm}} run production');


/*
 |-------------------------------------------------------------------
 | Task Groups
 | -------------------------------------------------------------------
 | Group together multiple tasks, so they can be added to the
 | deployment flow easier.
 */

task('npm', [
    'npm:install',
    'npm:production'
]);

task('artisan_clear_caches', [
    'artisan:cache:clear',
    'artisan:route:cache'
]);

task('artisan_backup_and_migrate', [
    'artisan:backup:run',
    'artisan:migrate'
]);


/*
 |-------------------------------------------------------------------
 | Deploy tasks
 | -------------------------------------------------------------------
 | Add the additional tasks to the deployment flow.
 */

// After composer install; run npm install and npm production
after('deploy:vendors', 'npm');

// Run additional artisan commands
after('artisan:storage:link', 'artisan:clear_compiled');

after('artisan:view:clear', 'artisan_clear_caches');

after('artisan:optimize', 'artisan_backup_and_migrate');

// [Optional] Uncomment to run horizon:terminate
before('deploy:symlink', 'artisan:horizon:terminate');

// [Optional] if deploy fails automatically unlock.
fail('deploy', 'deploy:failed');
after('deploy:failed', 'deploy:unlock');


/*
 |-------------------------------------------------------------------
 | Helper methods
 | -------------------------------------------------------------------
 | These are some helper methods to make sure they needed .env
 | variables are set and for showing error messages.
 */

function checkEnvVariablesAreSet() {
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

function errorMessage($message) {
    echo "\e[1;31;mError: " .$message. "\e[0m\n";
}
