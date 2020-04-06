<?php
declare(strict_types=1);

namespace Common\Interfaces;

use Illuminate\Database\Schema\Blueprint;

interface MigrationUpdateInterface
{
    public function updateSchema(Blueprint $table): void;
    public function rollbackSchema(Blueprint $table): void;
}
