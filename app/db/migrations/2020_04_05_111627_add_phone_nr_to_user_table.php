<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Common\Service\Database\Blueprint;

class AddPhoneNrToUserTable extends Migration
{
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table
                ->string('phone_nr', 20)
                ->after('email')
            ;
        });
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('phone_nr');
        });
    }
}
