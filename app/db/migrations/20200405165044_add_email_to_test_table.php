<?php
declare(strict_types=1);

use Common\Config\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddEmailToTestTable extends Migration
{
    public function up(): void
    {
        $this->schema->table('test', function (Blueprint $table) {
            $table->string('email', 255)
                ->after('password')
            ;
        });
    }

    public function down(): void
    {
        $this->schema->table('test', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
}
