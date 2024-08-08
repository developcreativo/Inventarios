<?php

namespace Developcreativo\Inventarios\Nova\Actions;

use App\Traits\AccessScopeTraits;
use Brightspot\Nova\Tools\DetachedActions\DetachedAction;
use Carbon\Carbon;
use Developcreativo\Inventarios\Models\EquipmentOrder;
use Illuminate\Support\Facades\Storage;
use KossShtukert\LaravelNovaSelect2\Select2;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Date;
use League\Csv\Writer;

class ActionEquipmentOrderViewer  extends DetachedAction
{
    use AccessScopeTraits;
    public function label()
    {
        return __('Reporte vencimiento equipamiento');
    }

    public function name()
    {
        return __('Reporte vencimiento equipamiento');
    }

    public function handle(ActionFields $fields)
    {
        $sucursal_id    = $fields->sucursal_id;
        $cliente_id    = $fields->cliente_id;
        $from    = $fields->from;
        $to     = $fields->to;

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
        $uploadedFileUrl  = $storageInstance->url($fileName, Carbon::now()->addMinutes(1));

        return DetachedAction::redirect($uploadedFileUrl);
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

        ];
    }
}