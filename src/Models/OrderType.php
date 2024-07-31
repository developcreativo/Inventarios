<?php

namespace Developcreativo\Inventarios\Models;

use Developcreativo\Inventarios\Scopes\OrderTypeScope;
use Illuminate\Database\Eloquent\Model;

class OrderType extends Model
{
    protected $table = 'claves';

    protected $attributes = [
        'clave' => 'tipo_movimiento'
    ];

    public $timestamps = false;

    protected $primaryKey = "valor";

    protected $fillable = [
        'valor',
        'descrip_corta',
        'descrip_larga'
    ];

    protected static function boot() {
        parent::boot();

        static::addGlobalScope(new OrderTypeScope());
    }
}