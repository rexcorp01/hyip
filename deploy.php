<?php
namespace Deployer;

require 'recipe/common.php';

use function Deployer\{host, task, run, set, get, add, before, after, upload, writeln};

// Project name
set('application', 'h-script');

// Project repository
set('repository', 'git@hscript.github.com:ValeriiVasyliev/h-script.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', false);

//set('writable_use_sudo', true);

set('writable_mode', 'chmod');

set('writable_chmod_mode', '0775');

set('http_group', 'www-data');

set('http_user', 'deployer');

host('hscript_production')
	->hostname('165.227.139.182')
	->port(22)
	->user('deployer')
	->forwardAgent(true)
   	->multiplexing(true)
	->set('deploy_path', '/var/www/hyipcreate-hscript-deployer')
	->set('keep_releases', 3);

// Shared files/dirs between deploys
set('shared_files', [
	'_config.php'
]);
set('shared_dirs', [
    'tpl_c',
    'upload',
    'logs'
]);

// Writable dirs by web server
set('writable_dirs', [
    'tpl_c',
    'upload',
    //'logs',
]);


set('composer_action', 'install');

set('composer_options', '{{composer_action}} --no-dev --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction --no-scripts');

// Remove unnecessary stuff
set('clear_paths', [
	'.git',
	'.github',
	'.gitignore',
	'deploy.php',
	'composer.json',
	'composer.lock'
]);

// Tasks
desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
