<?php

namespace seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class TipoEquipoSeeder extends Seeder
{
    public function run(): void
    {
        \App\Claves::create(
            [
                'clave' => 'tipo_movimiento',
                'valor' => 1,
                'descrip_larga' => 'Entrada',
                'descrip_corta' => 'Entrada'
            ]
        );

        \App\Claves::create(
            [
                'clave' => 'tipo_movimiento',
                'valor' => 2,
                'descrip_larga' => 'Salida',
                'descrip_corta' => 'Salida'
            ]
        );

        \App\Claves::create(
            [
                'clave' => 'tipo_equipo',
                'valor' => 1,
                'descrip_larga' => 'Uniformes',
                'descrip_corta' => 'Uniformes'
            ]
        );

        \App\Claves::create(
            [
                'clave' => 'tipo_equipo',
                'valor' => 2,
                'descrip_larga' => 'Camaras',
                'descrip_corta' => 'Camaras'
            ]
        );

        \App\Claves::create(
            [
                'clave' => 'tipo_equipo',
                'valor' => 3,
                'descrip_larga' => 'Seguridad',
                'descrip_corta' => 'Seguridad'
            ]
        );

        \App\Claves::create(
            [
                'clave' => 'tipo_equipo',
                'valor' => 4,
                'descrip_larga' => 'Bicicleta',
                'descrip_corta' => 'Bicicleta'
            ]
        );


        Permission::updateOrCreate(['name' => __("View Orders")], ['group' => 'Inventarios']);
        Permission::updateOrCreate(['name' => __("Create Orders")], ['group' => 'Inventarios']);
        Permission::updateOrCreate(['name' => __("Update Orders")], ['group' => 'Inventarios']);
        Permission::updateOrCreate(['name' => __("Delete Orders")], ['group' => 'Inventarios']);

        Permission::updateOrCreate(['name' => __("View equipment")], ['group' => 'Inventarios']);
        Permission::updateOrCreate(['name' => __("Create equipment")], ['group' => 'Inventarios']);
        Permission::updateOrCreate(['name' => __("Update equipment")], ['group' => 'Inventarios']);
        Permission::updateOrCreate(['name' => __("Delete equipment")], ['group' => 'Inventarios']);
    }
}
