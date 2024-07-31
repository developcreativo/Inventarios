<?php

namespace Developcreativo\Inventarios\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrderTypeScope implements Scope {

    public function apply( Builder $builder, Model $model ) {
        $builder->where('clave','=','tipo_movimiento');
    }
}
