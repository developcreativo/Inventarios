<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentTable extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('comments');
            $table->integer('equipment_type');
            $table->double('avg_price');
            $table->integer('available_items');
            $table->double('items_value');
            $table->integer('last_order_id');
            $table->integer('reorder_point');
            $table->boolean('reorder_flag');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
}
