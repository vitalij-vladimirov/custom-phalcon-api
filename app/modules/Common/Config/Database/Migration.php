<?php
declare(strict_types=1);

namespace Common\Config\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Builder;
use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration
{
    public Capsule $capsule;
    public Builder $schema;

    // TODO: get config from env or main config file
    public function init()
    {
        $this->capsule = new Capsule;
        $this->capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => 'phalcon-api-db',
            'port'      => 3306,
            'database'  => 'phalcon_api',
            'username'  => 'api',
            'password'  => 'api',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        $this->capsule->bootEloquent();
        $this->capsule->setAsGlobal();
        $this->schema = $this->capsule->schema();
    }
}
