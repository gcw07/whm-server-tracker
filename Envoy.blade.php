@setup
    require __DIR__.'/vendor/autoload.php';
    $dotenv = new \Dotenv\Dotenv(__DIR__);
    try {
        $dotenv->load();
        $dotenv->required(['DEPLOY_SERVER', 'DEPLOY_USER', 'DEPLOY_REPOSITORY', 'DEPLOY_PATH'])->notEmpty();
    } catch ( Exception $e )  {
        echo $e->getMessage();
        exit;
    }

    $server = getenv('DEPLOY_SERVER');
    $user = getenv('DEPLOY_USER');
    $repository = getenv('DEPLOY_REPOSITORY');
    $path = getenv('DEPLOY_PATH');

    if ( substr($path, 0, 1) !== '/' ) throw new Exception('Careful - your deployment path does not begin with /');

    $env = isset($env) ? $env : "production";
    $branch = isset($branch) ? $branch : "master";

    $baseDir = rtrim($path, '/');
    $releasesDir = "{$baseDir}/releases";
    $persistentDir = "{$baseDir}/persistent";
    $currentDir = "{$baseDir}/current";
    $newReleaseName = date('Ymd-His');
    $newReleaseDir = "{$releasesDir}/{$newReleaseName}";

    function logMessage($message) {
        return "echo '\033[32m" .$message. "\033[0m';\n";
    }
@endsetup

@servers(['local' => '127.0.0.1', 'remote' => $server])

@task('init', ['on' => 'remote'])
    {{ logMessage("🏃  Initializing Site...") }}
    su {{ $user }};
    if [ ! -d {{ $currentDir }} ]; then
        {{ logMessage("💎  Create releases directories...") }}
        [ -d {{ $releasesDir }} ] || mkdir {{ $releasesDir }};
        cd {{ $releasesDir }};
        mkdir {{ $newReleaseDir }};

        {{ logMessage("🌀  Cloning repository ($newReleaseName)...") }}
        git clone {{ $repository }} --branch={{ $branch }} --depth=1 -q {{ $newReleaseName }}

        {{ logMessage("🗃  Setup storage directory...") }}
        mkdir {{ $persistentDir }}
        mv {{ $newReleaseDir }}/storage {{ $persistentDir }}/storage
        ln -s {{ $persistentDir }}/storage {{ $newReleaseDir }}/storage

        {{ logMessage("🌥  Setup .env file...") }}
        cp {{ $newReleaseDir }}/.env.example {{ $baseDir }}/.env
        ln -s {{ $baseDir }}/.env {{ $newReleaseDir }}/.env
        cd {{ $newReleaseDir }};

        {{ logMessage("🛁  Clean up release directory...") }}
        cd {{ $baseDir }};
        rm -rf {{ $newReleaseDir }}

        {{ logMessage("🚀  Deployment path initialized. Run 'envoy run deploy' now.") }}
    else
        {{ logMessage("🏃  Deployment path already initialized (current symlink exists)!") }}
    fi
@endtask

@story('deploy')
startDeployment
cloneRepository
runComposer
runYarn
generateAssets
updateSymlinks
optimizeInstallation
backupDatabase
migrateDatabase
blessNewRelease
{{--restartQueue--}}
cleanOldReleases
finishDeploy
@endstory

@task('startDeployment', ['on' => 'local'])
{{ logMessage("🏃  Starting deployment ($newReleaseName)...") }}
@endtask

@task('cloneRepository', ['on' => 'remote'])
{{ logMessage("🌀  Cloning repository...") }}
su {{ $user }};
[ -d {{ $releasesDir }} ] || mkdir {{ $releasesDir }};
cd {{ $releasesDir }};

# Create the release dir
mkdir {{ $newReleaseDir }};

# Clone the repo
git clone {{ $repository }} --branch={{ $branch }} --depth=1 -q {{ $newReleaseName }}

# Configure sparse checkout
cd {{ $newReleaseDir }}
git config core.sparsecheckout true
echo "*" > .git/info/sparse-checkout
echo "!storage" >> .git/info/sparse-checkout
echo "!public/build" >> .git/info/sparse-checkout
git read-tree -mu HEAD

# Mark release
cd {{ $newReleaseDir }}
echo "{{ $newReleaseName }}" > public/release-name.txt
@endtask

@task('runComposer', ['on' => 'remote'])
{{ logMessage("🚚  Running Composer...") }}
su {{ $user }};
cd {{ $newReleaseDir }};
composer install --prefer-dist --no-scripts --no-dev -q -o;
@endtask

@task('runYarn', ['on' => 'remote'])
{{ logMessage("📦  Running Yarn...") }}
su {{ $user }};
cd {{ $newReleaseDir }};
yarn config set ignore-engines true
yarn
@endtask

@task('generateAssets', ['on' => 'remote'])
{{ logMessage("🌅  Generating assets...") }}
su {{ $user }};
cd {{ $newReleaseDir }};
yarn run production --no-progress
@endtask

@task('updateSymlinks', ['on' => 'remote'])
{{ logMessage("🔗  Updating symlinks to persistent data...") }}
su {{ $user }};

# Remove the storage directory and replace with persistent data
rm -rf {{ $newReleaseDir }}/storage;
cd {{ $newReleaseDir }};
ln -nfs {{ $persistentDir }}/storage storage;

# Import the environment config
cd {{ $newReleaseDir }};
ln -nfs {{ $baseDir }}/.env .env;
@endtask

@task('optimizeInstallation', ['on' => 'remote'])
{{ logMessage("✨  Optimizing installation...") }}
su {{ $user }};
cd {{ $newReleaseDir }};
php artisan clear-compiled;
@endtask

@task('backupDatabase', ['on' => 'remote'])
{{ logMessage("📀  Backing up database...") }}
su {{ $user }};
cd {{ $newReleaseDir }}
php artisan backup:run
@endtask

@task('migrateDatabase', ['on' => 'remote'])
{{ logMessage("🙈  Migrating database...") }}
su {{ $user }};
cd {{ $newReleaseDir }};
php artisan migrate --force;
@endtask

@task('blessNewRelease', ['on' => 'remote'])
{{ logMessage("🙏  Blessing new release...") }}
su {{ $user }};
ln -nfs {{ $newReleaseDir }} {{ $currentDir }};
cd {{ $newReleaseDir }}
php artisan view:clear
php artisan cache:clear
php artisan route:cache
php artisan config:cache
@endtask

@task('restartQueue', ['on' => 'remote'])
{{ logMessage("🙏  Restarting queue...") }}
su {{ $user }};
cd {{ $newReleaseDir }}
php artisan horizon:terminate
@endtask

@task('cleanOldReleases', ['on' => 'remote'])
{{ logMessage("🚾  Cleaning up old releases...") }}
su {{ $user }};

# Delete all but the 5 most recent.
cd {{ $releasesDir }}
ls -dt {{ $releasesDir }}/* | tail -n +6 | xargs -d "\n" chown -R {{ $user }} .;
ls -dt {{ $releasesDir }}/* | tail -n +6 | xargs -d "\n" rm -rf;
@endtask

@task('finishDeploy', ['on' => 'local'])
{{ logMessage("🚀  Application deployed!") }}
@endtask