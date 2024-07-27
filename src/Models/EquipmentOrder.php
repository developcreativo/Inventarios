<?php

namespace Developcreativo\Inventarios\Models;

use App\Person;
use Illuminate\Database\Eloquent\Model;

class EquipmentOrder extends Model
{
    protected $fillable = [
        'order_date',
        'order_type',
        'id_equipment',
        'quantity',
        'order_price',
        'available_items_before',
        'available_items_after',
        'user_id',
        'id_usuario',
        'comments',
    ];

    protected $casts = [
        'order_date' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'id_equipment');
    }

    public function usuario()
    {
        return $this->belongsTo(Person::class, 'id_usuario');
    }
}
