<?php

namespace Deployer;

require 'recipe/common.php';

// Project  name
set('application', 'notify_parser');
set('keep_releases', 5);
set('default_timeout', null);

set('cachetool_conn', '0.0.0.0:9000');

add('shared_dirs', [
    'storage/logs'
]);

// Hosts
host('10.10.60.112')
    ->stage('prod')
    ->set('deploy_path', '/var/www/{{ application }}')
    ->user('rzk')
    ->port(10022)
    ->multiplexing(true)
    ->addSshOption('StrictHostKeyChecking', 'no');


// Tasks
task('upload', function () {
    upload(__DIR__ . '/notify-parser/', '{{release_path}}');
})->desc('Environment setup');


task('cachetool:clear:cache', function () {
    run("cachetool opcache:reset --fcgi={{cachetool_conn}}");
});

task('deploy:migrations', function () {
    run("cd {{release_path}} && php artisan migrate");
});

task('deploy:db:seed', function () {
    run("cd {{release_path}} && php artisan db:seed");
})->once();

task('release', [
    'deploy:prepare',
    'deploy:release',
    'upload',
    'deploy:migrations',
    // needs to be run on first deploy or manualy
    // 'deploy:db:seed',
    'deploy:shared'
]);

task('deploy', [
  'release',
  'deploy:symlink',
  'cleanup',
  'success'
]);

after('deploy:symlink', 'cachetool:clear:cache');
after('rollback', 'cachetool:clear:cache');

after('deploy:failed', 'deploy:unlock');

