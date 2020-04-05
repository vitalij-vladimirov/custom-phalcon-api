<?php
declare(strict_types=1);

use Common\Config\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTestTable extends Migration
{
    public function up(): void
    {
        $this->schema->create('test', function (Blueprint $table) {
            $table->id();
            $table->string('username', 64);
            $table->string('password', 32);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists('test');
    }
}
