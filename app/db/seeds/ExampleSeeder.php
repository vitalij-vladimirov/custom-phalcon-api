<?php
declare(strict_types=1);

namespace Seeds;

use Common\BaseClasses\BaseSeeder;

/**
 * Seeds will be created only if table is empty.
 * One table can have only one seeds file.
 *
 * Seeds should be used in development or testing process, so they can not be
 * created in production. If you need to add some information to production
 * database use migration method afterMigration() to call seeder or update
 * table data manually.
 */
class ExampleSeeder extends BaseSeeder
{
    protected string $table = 'example';

    protected function seedTable(): void
    {
        $data = [
            [
                "lib_name" => "PHP .env",
                "lib_url" => "vlucas/phpdotenv",
                "version" => "4.1",
                "environment" => "production",
                "description" => "Env configuration reader",
            ], [
                "lib_name" => "PHP Cron scheduler",
                "lib_url" => "peppeocchi/php-cron-scheduler",
                "version" => "3.0",
                "environment" => "production",
                "description" => "Cronjobs scheduler",
            ], [
                "lib_name" => "Dice",
                "lib_url" => "level-2/dice",
                "version" => "4.0",
                "environment" => "production",
                "description" => "Depenency injection container generator",
            ], [
                "lib_name" => "Carbon",
                "lib_url" => "nesbot/carbon",
                "version" => "2.31",
                "environment" => "production",
                "description" => "Time manipulation lib",
            ], [
                "lib_name" => "Guzzle",
                "lib_url" => "guzzlehttp/guzzle",
                "version" => "6.5",
                "environment" => "production",
                "description" => "Http/Curl requests lib",
            ],
        ];

//        TODO: create easy and understandable insertion, maybe use Laravel ORM
//        $keys = '`' . implode('`,`', array_keys($data[0])) . '`';
//
//        $values = '';
//        foreach ($data as $row) {
//            if (!empty($values)) {
//                $values .= ',';
//            }
//            $values .= '(\'' . implode('\',\'', $row) . '\')';
//        }
//
//        $this->db->execute('INSERT INTO `' . $this->table . '` (' . $keys . ') VALUES ' . $values);
    }
}
