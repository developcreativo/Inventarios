<?php

namespace Developcreativo\Inventarios\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'name',
        'comments',
        'equipment_type',
        'avg_price',
        'available_items',
        'items_value',
        'last_order_id',
        'reorder_point',
        'reorder_flag',
    ];
}
