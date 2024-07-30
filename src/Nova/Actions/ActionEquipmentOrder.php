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

class ActionEquipmentOrder  extends DetachedAction
{
    use AccessScopeTraits;
    public function label()
    {
        return __('Export equipment');
    }

    public function name()
    {
        return __('Export equipment');
    }

    public function handle(ActionFields $fields)
    {
        $order_type     = $fields->order_type;
        $from    = $fields->from;
        $to     = $fields->to;

//        if ($from == null || $to == null) {
//            return DetachedAction::danger(__('The fields are required'));
//        }

        $trans = __('Movimientos');
        $now              = now()->format('U');
        $fileName         = "tmp/$trans-$now.csv";
        $storageInstance  = Storage::disk('reportes');
        $putFileOnStorage = $storageInstance->put($fileName, '');
        $fileContent      = $storageInstance->get($fileName);

        $query = EquipmentOrder::query();

        if ($order_type) {
            $query = $query->where('order_type', $order_type);
        }


        if ($from !== null && $to !== null) {
            $query = $query->whereBetween('order_date', [$from, $to]);
        }

        $headers = [
            0 => [
                __('Order Date'),
                __('Order Type'),
                __('Equipment'),
                __('Quantity'),
                __('Order Price'),
                __('Available Items Before'),
                __('Available Items After'),
                __('Person'),
                __('Comments'),
            ]
        ];

        // $records = DB::select($query);
        $records = $query->get();


        $records = collect($records)->map(function ($x) {
            $order_type = \App\Claves::query()->where( 'clave', 'order_type' )->where('valor', $x->order_type)->first();
            return (array)[
                $x->order_date,
                $order_type->descrip_larga,
                isset( $x->equipment) ? $x->equipment['name'] : '',
                $x->quantity,
                $x->order_price,
                $x->available_items_before,
                $x->available_items_after,
                isset( $x->usuario) ? $x->usuario['nombre'] : '',
                $x->comments,
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
            Date::make(__('From'), 'from'),
            Date::make(__('To'), 'to'),
            Select2::make(__('Order Type'), 'order_type')
                ->options(\App\Claves::query()->where( 'clave', 'order_type' )->pluck( 'descrip_larga', 'valor' ))
                ->configuration([
                    'placeholder'             => __('Choose an option'),
                    'allowClear'              => true,
                    'minimumResultsForSearch' => 1,
                    'multiple'                => false,
                ])->sortable(),
        ];
    }
}