<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\Type;
class CreateEquipmentTable extends Migration
{
    public function up(): void
    {
        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('comments');
            $table->integer('equipment_type');
            $table->double('avg_price', 15, 2)->nullable()->default(0);
            $table->unsignedBigInteger('available_items')->nullable()->default(0);
            $table->double('items_value');
            $table->integer('last_order_id');
            $table->integer('reorder_point');
            $table->boolean('reorder_flag');
            $table->unsignedBigInteger('id_talla')->nullable()->default(null);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
}
