<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_orders', function (Blueprint $table) {
            $table->id();
            $table->dateTime('order_date');
            $table->integer('order_type');
            $table->integer('id_equipment');
            $table->integer('quantity');
            $table->double('order_price');
            $table->integer('available_items_before');
            $table->integer('available_items_after');
            $table->integer('user_id');
            $table->string('id_usuario')->nullable()->default(null);
            $table->text('comments');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_orders');
    }
}
