<?php

namespace Developcreativo\Inventarios\Models;

use App\Claves;
use App\Person;
use App\Ve360v1\Models\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'equipo_talla_id',
        'valid_until'
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'valid_until' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'id_equipment');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(OrderType::class, 'order_type', 'valor');
    }

    public function equipo_talla(): BelongsTo
    {
        return $this->belongsTo(Claves::query()->where( 'clave', 'equipo_talla' ), 'equipo_talla_id', 'valor');
    }
}
