<?php
declare(strict_types=1);

namespace Common\Config\Database;

use Phalcon\Config;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Builder;
use Phinx\Migration\AbstractMigration;

class Migration extends AbstractMigration
{
    public Capsule $capsule;
    public Builder $schema;

    public function init()
    {
        /** @var Config $config */
        $config = $GLOBALS['config'];

        $this->capsule = new Capsule;
        $this->capsule->addConnection([
            'driver'    => $config->database->adapter,
            'host'      => $config->database->host,
            'port'      => $config->database->port,
            'database'  => $config->database->dbname,
            'username'  => $config->database->username,
            'password'  => $config->database->password,
            'charset'   => $config->database->charset,
            'collation' => $config->database->collation,
        ]);

        $this->capsule->bootEloquent();
        $this->capsule->setAsGlobal();
        $this->schema = $this->capsule->schema();
    }
}
