<?php

/**
 * class DistribucionesViewDocuments
 * Copyright (c) 2023.  FaceIt
 * @author Kelly Salazar <developmentcreativo@gmail.com>
 */

namespace Developcreativo\Inventarios\Nova\Actions;

use App\Cliente;
use App\DistributionUsers;
use App\Mail\DistribucionDocumentExpiredMail;
use App\Person;
use App\Services\HasUser;
use Brightspot\Nova\Tools\DetachedActions\DetachedAction;
use Carbon\Carbon;
use Developcreativo\Ajaxselected\Ajaxselected;
use Developcreativo\Inventarios\Mail\DistribucionEquipamentExpiredMail;
use Developcreativo\Inventarios\Models\EquipmentOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use KossShtukert\LaravelNovaSelect2\Select2;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Date;
use League\Csv\Writer;

class ActionDistributions extends DetachedAction
{
    public $confirmButtonText = 'Send to Distribution List';
    public function label()
    {
        return __('Send to Distribution List');
    }

    public function name()
    {
        return __('Send to Distribution List');
    }

    public function handle(ActionFields $fields)
    {
        $sucursal_id    = $fields->sucursal_id;
        $cliente_id    = $fields->cliente_id;
        $from    = $fields->from;
        $to     = $fields->to;
        $distrubicion  = $fields->id_distrubicion;

        $trans = __('vencimiento_equipamiento');
        $now              = now()->format('U');
        $fileName         = "tmp/$trans-$now.csv";
        $storageInstance  = Storage::disk('reportes');
        $putFileOnStorage = $storageInstance->put($fileName, '');
        $fileContent      = $storageInstance->get($fileName);

        $query = EquipmentOrder::query()->with(['equipment', 'usuario']);

        if ($sucursal_id !== null) {
            $query = $query->whereHas('usuario.ubicacion', function ($query) use ($sucursal_id) {
                $query->where('sucursal', $sucursal_id);
            });
        }


        if ($cliente_id) {
            $query = $query->whereHas('usuario', function ($query) use ($cliente_id) {
                $query->where('id_cliente', $cliente_id);
            });
        }

        if ($from !== null && $to !== null) {
            $query = $query->whereBetween('valid_until', [$from, $to]);
        }

        $headers = [
            0 => [
                __('ID'),
                __('Person'),
                __('Customer'),
                __('Location'),
                __('Equipment'),
                __('Delivery date'),
                __('Expiration date'),
                __('Remaining days'),
            ]
        ];

        // $records = DB::select($query);
        $records = $query->get();


        $records = collect($records)->map(function ($x) {
            $validUntil = $x->valid_until;
            $today = now();

            if ($validUntil) {
                $remainingDays = $today <= $validUntil ? $validUntil->diffInDays($today, false) : 0;
                $remainingDays = abs($remainingDays);
            } else {
                $remainingDays = 0;
            }
            return (array)[
                $x->id,
                isset( $x->usuario) ? $x->usuario['nombre'] : '',
                isset( $x->usuario->cliente) ? $x->usuario->cliente['nombre_cliente'] : '',
                isset( $x->usuario->ubicacion) ? $x->usuario->ubicacion['nombre_ubicacion'] : '',
                isset( $x->equipment) ? $x->equipment['name'] : '',
                $x->order_date,
                $x->valid_until,
                $remainingDays,
            ];
        })->toArray();

        if (count($records) > 99000) {
            return DetachedAction::danger(__('The query is larger than 99000 records. Please narrow your filters'));
        }

        $writer = Writer::createFromString($fileContent, 'w');
        $writer->insertAll($headers);
        $writer->insertAll($records);
        $csvContent       = $writer->getContent();
        $putFileOnStorage = $storageInstance->put($fileName, $csvContent, 'public');
        $distrubicionUsers = DistributionUsers::query()->where('distribution_id', $distrubicion)->get();
        foreach ($distrubicionUsers as $index => $distrubicionUser) {
            $user = DB::table('users')->where('id', $distrubicionUser['user_id'])->first();
            if ($user) {
                try {
                    Mail::to($user)
                        ->queue(new DistribucionEquipamentExpiredMail($fileName));
                } catch (\Exception $exception) {

                }
            }

        }
        return;
    }

    public function fields()
    {
        return [
            Select2::make(__('Branch'), 'sucursal_id')
                ->options(\App\Sucursales::query()->pluck( 'nombre_sucursal', 'id' ))
                ->configuration([
                    'placeholder'             => __('Choose an option'),
                    'allowClear'              => true,
                    'minimumResultsForSearch' => 1,
                    'multiple'                => false,
                ])->sortable(),

            Select2::make(__('Customer'), 'cliente_id')
                ->options(\App\Cliente::query()->pluck( 'nombre_cliente', 'id' ))
                ->configuration([
                    'placeholder'             => __('Choose an option'),
                    'allowClear'              => true,
                    'minimumResultsForSearch' => 1,
                    'multiple'                => false,
                ])->sortable(),
            Date::make(__('From'), 'from'),
            Date::make(__('To'), 'to'),

            Select2::make(__('Distribution'), 'id_distrubicion')
                ->options(\App\Distribution::orderBy('name', 'asc')->pluck('name', 'id'))
                ->configuration([
                    'placeholder'             => __('Choose an option'),
                    'allowClear'              => true,
                    'minimumResultsForSearch' => 1,
                    'multiple'                => false,
                ])->sortable(),
        ];
    }
}