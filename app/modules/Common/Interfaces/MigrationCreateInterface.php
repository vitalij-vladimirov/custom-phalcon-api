<?php
declare(strict_types=1);

namespace Common\Interfaces;

use Illuminate\Database\Schema\Blueprint;

interface MigrationCreateInterface
{
    public function createSchema(Blueprint $table): void;
}
