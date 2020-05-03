<?php
declare(strict_types=1);

namespace Seeds;

use Common\BaseClass\BaseSeeder;

/**
 * Seeds will be created only if table is empty.
 * One table can have only one seeds file.
 *
 * Seeds should be used in development or testing process, so they can not be
 * created in production. If you need to add some information to production
 * database use migration method afterMigration() to call seeder or update
 * table data manually.
 */
class VendorSeeder extends BaseSeeder
{
    protected string $table = 'vendor';

    protected function seedTable(): void
    {
        $data = [
            [
                'lib_name' => 'PHP .env',
                'lib_url' => 'vlucas/phpdotenv',
                'version' => '4.1',
                'environment' => 'production',
                'description' => 'Env configuration reader',
            ], [
                'lib_name' => 'PHP Cron Scheduler',
                'lib_url' => 'peppeocchi/php-cron-scheduler',
                'version' => '3.0',
                'environment' => 'production',
                'description' => 'Cronjobs scheduler',
            ], [
                'lib_name' => 'Dice',
                'lib_url' => 'level-2/dice',
                'version' => '4.0',
                'environment' => 'production',
                'description' => 'Depenency injection container generator',
            ], [
                'lib_name' => 'Carbon',
                'lib_url' => 'nesbot/carbon',
                'version' => '2.31',
                'environment' => 'production',
                'description' => 'Time manipulation lib',
            ], [
                'lib_name' => 'Guzzle',
                'lib_url' => 'guzzlehttp/guzzle',
                'version' => '6.5',
                'environment' => 'production',
                'description' => 'Http/Curl requests lib',
            ], [
                'lib_name' => 'Laravel Eloquent DB',
                'lib_url' => 'illuminate/database',
                'version' => '7.4',
                'environment' => 'production',
                'description' => 'Laravel DB manager required to create migrations and seeds',
            ], [
                'lib_name' => 'Laravel Filesystem',
                'lib_url' => 'illuminate/filesystem',
                'version' => '7.4',
                'environment' => 'production',
                'description' => 'Laravel File manager required to create migrations and seeds',
            ], [
                'lib_name' => 'Phinx',
                'lib_url' => 'robmorgan/phinx',
                'version' => '0.11.5',
                'environment' => 'production',
                'description' => 'Migrations manager required to run Eloquent migrations',
            ], [
                'lib_name' => 'Non existing vendor',
                'lib_url' => 'test/vendor',
                'version' => '7.77',
                'environment' => 'development',
                'description' => 'Test description',
            ], [
                'lib_name' => 'Phalcon IDE stubs',
                'lib_url' => 'phalcon/ide-stubs',
                'version' => '4.0',
                'environment' => 'development',
                'description' => 'Phalcon IDE helpers',
            ], [
                'lib_name' => 'Phalcon DD',
                'lib_url' => 'phalcon/dd',
                'version' => '1.1',
                'environment' => 'development',
                'description' => 'Phalcon dump debugging manager',
            ], [
                'lib_name' => 'PHPUnit',
                'lib_url' => 'phpunit/phpunit',
                'version' => '9.0',
                'environment' => 'development',
                'description' => 'Code unit testing tool',
            ], [
                'lib_name' => 'Paratest',
                'lib_url' => 'brianium/paratest',
                'version' => '4.0',
                'environment' => 'development',
                'description' => 'Parallel Testing for PHPUnit',
            ], [
                'lib_name' => 'PHP Mock',
                'lib_url' => 'php-mock/php-mock',
                'version' => '2.2',
                'environment' => 'development',
                'description' => 'PHP test tool for mocking functions & classes',
            ], [
                'lib_name' => 'PHP CS fixer',
                'lib_url' => 'friendsofphp/php-cs-fixer',
                'version' => '2.16',
                'environment' => 'development',
                'description' => 'Verification of Code Standards and auto fixing',
            ], [
                'lib_name' => 'PHP Code Sniffer',
                'lib_url' => 'squizlabs/php_codesniffer',
                'version' => '3.5',
                'environment' => 'development',
                'description' => 'Verification of Code Standards and auto fixing',
            ], [
                'lib_name' => 'PHP Code Sniffer ruleset',
                'lib_url' => 'sebastiaanluca/php-codesniffer-ruleset',
                'version' => '0.4.3',
                'environment' => 'development',
                'description' => 'PHP Code Sniffer rules configuration',
            ], [
                'lib_name' => 'PHP Code Sniffer sniffs',
                'lib_url' => 'moxio/php-codesniffer-sniffs',
                'version' => '2.4',
                'environment' => 'development',
                'description' => 'PHP Code Sniffer rules configuration',
            ],
        ];

        $this->eloquent::table($this->table)->insert($data);
    }
}
