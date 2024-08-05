<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValidUntil extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_orders', function (Blueprint $table) {
            $table->date('valid_until')->nullable()->default(null);
        });
    }

    public function down(): void
    {
        Schema::table('equipment_orders', function (Blueprint $table) {
            $table->dropColumn('valid_until');
        });
    }
}
