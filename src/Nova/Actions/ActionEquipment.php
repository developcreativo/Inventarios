<?php

namespace Developcreativo\Inventarios\Nova\Actions;

use App\Traits\AccessScopeTraits;
use Brightspot\Nova\Tools\DetachedActions\DetachedAction;
use Carbon\Carbon;
use Developcreativo\Inventarios\Models\Equipment;
use Illuminate\Support\Facades\Storage;
use KossShtukert\LaravelNovaSelect2\Select2;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Date;
use League\Csv\Writer;

class ActionEquipment  extends DetachedAction
{
    use AccessScopeTraits;
    public function label()
    {
        return __('Exportar equipos');
    }

    public function name()
    {
        return __('Exportar equipos');
    }

    public function handle(ActionFields $fields)
    {
        $equipment_type     = $fields->equipment_type;
//        if ($from == null || $to == null) {
//            return DetachedAction::danger(__('The fields are required'));
//        }

        $trans = __('Equipos');
        $now              = now()->format('U');
        $fileName         = "tmp/$trans-$now.csv";
        $storageInstance  = Storage::disk('reportes');
        $putFileOnStorage = $storageInstance->put($fileName, '');
        $fileContent      = $storageInstance->get($fileName);

        $query = Equipment::query();

        if ($equipment_type) {
            $query = $query->where('equipment_type', $equipment_type);
        }


        $headers = [
            0 => [
                __('ID'),
                __('Name'),
                __('Comments'),
                __('Equipment Type'),
                __('Avg Price'),
                __('Available Items'),
                __('Items Value'),
                __('Reorder Point'),
                __('Reorder Flag'),
            ]
        ];

        // $records = DB::select($query);
        $records = $query->get();


        $records = collect($records)->map(function ($x) {
            $equipment_type = \App\Claves::query()->where( 'clave', 'equipment_type' )->where('valor', $x->equipment_type)->first();
            return (array)[
                $x->id,
                $x->name,
                $x->comments,
                isset($equipment_type) ? $equipment_type['descrip_larga'] : '',
                round($x->avg_price, 2),
                $x->available_items,
                $x->items_value,
                $x->last_order_id,
                $x->reorder_point,
                $x->reorder_flag,
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
            Select2::make(__('Equipment Type'), 'equipment_type')
                ->options(\App\Claves::query()->where( 'clave', 'tipo_equipo' )->pluck( 'descrip_larga', 'valor' ))
                ->configuration([
                    'placeholder'             => __('Choose an option'),
                    'allowClear'              => true,
                    'minimumResultsForSearch' => 1,
                    'multiple'                => false,
                ])->sortable(),
        ];
    }
}