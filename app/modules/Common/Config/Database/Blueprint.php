<?php
declare(strict_types=1);

namespace Common\Config\Database;

use Illuminate\Database\Schema\Blueprint as LaravelBlueprint;
use Illuminate\Support\Facades\DB;

class Blueprint extends LaravelBlueprint
{
    public function timestamps($precision = 0)
    {
        $this->timestamp('created_at')
            ->default(DB::raw('CURRENT_TIMESTAMP'));
        $this->timestamp('updated_at')
            ->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
    }
}
