<?php /** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Deployer;

use RuntimeException;

require 'recipe/laravel.php';

$localEnvPath = __DIR__.'/.envs';
if (! file_exists($localEnvPath) && ! mkdir($localEnvPath, 0755, true) && ! is_dir($localEnvPath)) {
    throw new RuntimeException(sprintf('Directory "%s" was not created', $localEnvPath));
}

$vaultPath = getenv('VAULT_HABITS_PATH');
$localEnvName = 'env.deployer';
$localEnvFile = "$localEnvPath/$localEnvName";

if (! file_exists($localEnvFile)) {
    echo '🔐 Fetching env.deployer from Vault...'.PHP_EOL;
    exec("vault kv get -field=env_file $vaultPath/$localEnvName > $localEnvFile");
}

if (file_exists($localEnvFile)) {
    $vars = parse_ini_file($localEnvFile, false, INI_SCANNER_TYPED);
    foreach ($vars as $key => $value) {
        putenv("$key=$value");
    }
}

// ==== CONFIG ====
set('application', 'Habits Tracker');
set('repository', 'git@github.com:andresfb/habits-tracker.git');
set('git_tty', true);
set('keep_releases', 3);
set('local_env_dir', __DIR__.'/.envs');
set('shared_files', ['.env']);
set('shared_dirs', ['storage']);
set('writable_dirs', ['storage/app', 'storage/framework', 'bootstrap/cache']);
set('allow_anonymous_stats', false);
set('git_verbose', true);
set('ssh_multiplexing', true);

// List files/directories you want to remove after clone
set('cleanup_files', [
    '.editorconfig',
    '.gitignore',
    '.gitattributes',
    '.env.example',
    '.prettierignore',
    '.prettierrc',
    '.yek',
    '.php-cs-fixer.php',
    '.phpcs.xml',
    'tests',
    'docs',
    'phpunit.xml',
    'README.md',
    'LICENSE',
    'deploy.php',
    'peck.json',
    'phpstan-baseline.neon',
    'phpstan.neon',
    'rector.php',
    'yek.yaml',
    'snippets.md',
    'duster.json',
    'pint.json',
    'tlint.json',
]);

// ==== HOSTS ====
$hosts = [
    'primus' => getenv('WEB_PRIMUS'),
    'secundos' => getenv('WEB_SECUNDOS'),
    'horizon' => getenv('HORIZON'),
    'prod-php-83' => getenv('PROD_PHP_83'),
];

$users = [
    'primus-user' => getenv('WEB_PRIMUS_USER'),
    'secundos-user' => getenv('WEB_SECUNDOS_USER'),
    'horizon-user' => getenv('HORIZON_USER'),
    'prod-php-83-user' => getenv('PROD_PHP_83_USER'),
];

$skipSuprvs = [
    'primus-skip-suprvs' => (bool) getenv('WEB_PRIMUS_SKIP_SUPRVS'),
    'secundos-skip-suprvs' => (bool) getenv('WEB_SECUNDOS_SKIP_SUPRVS'),
    'prod-php-83-skip-suprvs' => (bool) getenv('PROD_PHP_83_SUPRVS'),
];

$skipNpm = [
    'horizon-skip-npm' => true,
];

$envFiles = [
    'prod-php-83-env' => 'env.internal',
];

$deployPath = getenv('DEPLOY_PATH');

foreach ($hosts as $name => $ip) {
    $userKey = $name.'-user';
    $skipSuprvsKey = $name.'-skip-suprvs';
    $skipNpmKey = $name.'-skip-npm';
    $envKey = $name.'-env';

    host($name)
        ->setHostname($ip)
        ->setRemoteUser($users[$userKey])
        ->set('http_user', $users[$userKey])
        ->set('deploy_path', $deployPath)
        ->set('env_file', $envFiles[$envKey] ?? 'env.app')
        ->set('skip_npm', $skipNpm[$skipNpmKey] ?? false)
        ->set('skip_supervisor', $skipSuprvs[$skipSuprvsKey] ?? false);
}

// ==== TASKS ====

task('env:pull', function () {
    $envBase = get('env_file');
    $envDir = get('local_env_dir');
    $envFile = getenv('VAULT_HABITS_PATH').'/'.$envBase;
    $localEnvPath = "$envDir/$envBase";

    if (! file_exists($localEnvPath)) {
        runLocally("mkdir -p $envDir");
        runLocally("vault kv get -field=env_file $envFile > $localEnvPath");
    }
});

// upload the .env files
task('env:upload', function () {
    $envBase = get('env_file');
    $envHost = 'env.{{alias}}';

    $envDir = get('local_env_dir');
    $localEnvPath = "$envDir/$envBase";
    $localEnvHost = "$envDir/$envHost";

    runLocally("cp $localEnvPath $localEnvHost");
    upload($localEnvHost, '{{deploy_path}}/shared/.env');
});

// Clean up unnecessary files from release
task('deploy:cleanup_release', function () {
    $releasePath = get('release_path');

    foreach (get('cleanup_files') as $file) {
        run("if [ -e $releasePath/$file ]; then rm -rf $releasePath/$file; fi");
    }
});

// Create symlinks to shared mounted folders
task('storage:symlinks', function () {
    $releasePath = get('release_path');

    run("rm -rf $releasePath/storage/logs");
    run("ln -sfn /server-logs/habits $releasePath/storage/logs");
});

// Composer install (with first deploy check)
task('deploy:vendors', function () {
    $releasePath = get('release_path');
    $sharedVendor = "$releasePath/../shared/vendor";

    if (! test("[ -d $sharedVendor ]")) {
        writeln('🧩 First deploy: composer install --no-dev');
        run("cd $releasePath && composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader");
    } else {
        writeln('🔁 Subsequent deploy: composer install');
        run("cd $releasePath && composer install --prefer-dist --no-interaction --optimize-autoloader");
    }
});

// -- disable migrations completely
task('artisan:migrate', function () {
    writeln('⚠️ Skipping database migrations.');
});

task('build:assets', function () {
    if (get('skip_npm', false)) {
        writeln('⚠️  Skipping asset build on horizon host.');

        return;
    }

    // Otherwise, run npm install & build in your release path
    run('cd {{release_path}} && npm install');
    run('cd {{release_path}} && npm run build');
});

// Laravel optimize commands
task('laravel:optimize', function () {
    run('{{bin/php}} {{release_path}}/artisan optimize:clear');
    run('{{bin/php}} {{release_path}}/artisan optimize');
});

// Supervisor restart
task('supervisor:restart', function () {
    if (get('skip_supervisor', false)) {
        writeln('⚠️  Supervisor is not installed on this server – skipping.');

        return;
    }

    $checkSupervisor = run('systemctl is-active supervisor.service || true');

    if (trim($checkSupervisor) === 'active') {
        writeln('🔄 Supervisor is running, restarting programs...');
        run('sudo systemctl restart supervisor.service');
    } else {
        writeln('🚀 Supervisor not running. Starting supervisor service...');
        run('sudo systemctl start supervisor.service');
    }

    run('sleep 2'); // Wait a bit
});

// ==== CLEANUP LOCAL SECRETS AFTER DEPLOY ====
task('deploy:cleanup_local_secrets', function () {
    $localEnvDir = __DIR__.'/.envs';

    if (is_dir($localEnvDir)) {
        writeln('🧹 Deleting local .envs folder...');
        runLocally('rm -rf '.escapeshellarg($localEnvDir));
    }
});

// ==== HOOKS ====
before('deploy:shared', 'env:pull');
after('env:pull', 'env:upload');
after('deploy:update_code', 'deploy:cleanup_release');
after('artisan:storage:link', 'storage:symlinks');
before('deploy:symlink', 'build:assets');
after('build:assets', 'laravel:optimize');
after('deploy:symlink', 'supervisor:restart');
after('deploy:success', 'deploy:cleanup_local_secrets');
after('deploy:failed', 'deploy:unlock');
after('deploy:failed', 'deploy:cleanup');
