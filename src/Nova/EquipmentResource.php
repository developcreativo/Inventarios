<?php

namespace Developcreativo\Inventarios\Nova;

use App\Nova\Resource;
use Developcreativo\Inventarios\BroadcasterField;
use Developcreativo\Inventarios\ListenerField;
use Developcreativo\Inventarios\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use KossShtukert\LaravelNovaSelect2\Select2;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class EquipmentResource extends Resource
{
    public static function label() {
        return __("Equipos");
    }
    public static function singularLabel() {
        return  __("Equipos");
    }

    public static function group() {
        return __('Inventarios');
    }

    public static $model = Equipment::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name', 'comments'
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),



            Text::make(__('Name'), 'name')
                ->sortable()
                ->rules('required'),

            Text::make(__('Comments'), 'comments')
                ->sortable()
                ->rules('required'),


            Select2::make(__('Equipment Type'), 'equipment_type')
                ->options(\App\Claves::query()->where( 'clave', 'tipo_equipo' )->pluck( 'descrip_larga', 'valor' ))
                ->rules('required')
                ->configuration([
                    'placeholder'             => __('Choose an option'),
                    'allowClear'              => true,
                    'minimumResultsForSearch' => 1,
                    'multiple'                => false,
                ])->sortable(),

            BroadcasterField::make(__('Avg Price'), 'avg_price')
                ->broadcastTo('items_value'),

            BroadcasterField::make(__('Available Items'), 'available_items')
                ->broadcastTo('items_value'),

            ListenerField::make(__('Items Value'), 'items_value')
                ->listensTo('items_value')
                ->disableCalculationOnUpdate()
                ->calculateWith(function (Collection $values) {
                    $avg_price = $values->get('avg_price');
                    $available_items = $values->get('available_items');
                    return $avg_price * $available_items;
                })->showCalculationButton(false),

//            Number::make(__('Last Order Id'), 'last_order_id')
//                ->sortable()
//                ->rules('required', 'integer'),

            Number::make(__('Reorder Point'), 'reorder_point')
                ->sortable()
                ->rules('required', 'integer'),

            Boolean::make(__('Reorder Flag'), 'reorder_flag')
                ->sortable(),
        ];
    }

    public function cards(Request $request): array
    {
        return [];
    }

    public function filters(Request $request): array
    {
        return [];
    }

    public function lenses(Request $request): array
    {
        return [];
    }

    public function actions(Request $request): array
    {
        return [];
    }
}
