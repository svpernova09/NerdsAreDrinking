@servers(['prod' => 'drunk@nerdsdrinking.com'])

@task('deploy:prod', ['on' => 'prod'])
cd /home/drunk/NerdsAreDrinking
git pull origin master
composer install
php artisan migrate --force
@endtask