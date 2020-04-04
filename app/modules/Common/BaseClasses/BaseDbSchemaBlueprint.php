<?php
declare(strict_types=1);

namespace Common\BaseClasses;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class BaseDbSchemaBlueprint extends Blueprint
{
    public function timestamps($precision = 0)
    {
        $this->timestamp('created_at')
            ->default(DB::raw('CURRENT_TIMESTAMP'));
        $this->timestamp('updated_at')
            ->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
    }
}
