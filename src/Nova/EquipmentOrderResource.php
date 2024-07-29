<?php

namespace Developcreativo\Inventarios\Nova;

use App\Nova\Persons;
use App\Nova\Resource;
use Developcreativo\Inventarios\BroadcasterBelongsToField;
use Developcreativo\Inventarios\BroadcasterField;
use Developcreativo\Inventarios\BroadcasterSelectField;
use Developcreativo\Inventarios\ListenerField;
use Developcreativo\Inventarios\Models\Equipment;
use Developcreativo\Inventarios\Models\EquipmentOrder;
use Developcreativo\Inventarios\Traits\HasCallbacks;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use KossShtukert\LaravelNovaSelect2\Select2;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class EquipmentOrderResource extends Resource
{
    use HasCallbacks;

    public static function label() {
        return __("Movimientos");
    }
    public static function singularLabel() {
        return  __("Movimientos");
    }

    public static function group() {
        return __('Inventarios');
    }

    public static $model = EquipmentOrder::class;

    public static $title = 'id';

    public static $search = [
        'id', 'id_usuario', 'comments'
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Date::make(__('Order Date'), 'order_date')
                ->sortable()
                ->rules('required', 'date'),

            BroadcasterSelectField::make(__('Order Type'), 'order_type')
                ->options(\App\Claves::query()->where( 'clave', 'tipo_movimiento' )->pluck( 'descrip_larga', 'valor' ))
                ->rules('required')
                ->broadcastTo('available_items_after')->sortable(),

            BroadcasterBelongsToField::make(__('Equipment'), 'equipment', \Developcreativo\Inventarios\Nova\EquipmentResource::class)
                ->broadcastTo(['available_items_after', 'available_items_before']),
//            BroadcasterSelectField::make(__('Equipment'), 'equipment', \Developcreativo\Inventarios\Nova\EquipmentResource::class)
//                ->sortable()
//                ->rules('required'),

//            Number::make('Quantity')
//                ->sortable()
//                ->rules('required', 'integer'),

            BroadcasterField::make(__('Quantity'), 'quantity')
                ->broadcastTo('available_items_after'),

            Number::make(__('Order Price'), 'order_price')
                ->sortable()
                ->rules('required', 'numeric'),


            ListenerField::make(__('Available Items Before'), 'available_items_before')
                ->listensTo('available_items_before')
                ->readonly()
                ->calculateWith(function (Collection $values) {
                    $value = $values->get('equipment');
                    $product = Equipment::query()->find($value);
                    $available_items = !empty($product) ? $product->available_items :  0;
                    return $available_items;
                })->showCalculationButton(false),

//            Number::make('Available Items After')
//                ->sortable()
//                ->rules('required', 'integer'),

            ListenerField::make(__('Available Items After'), 'available_items_after')
                ->listensTo('available_items_after')
                ->readonly()
                ->calculateWith(function (Collection $values) {
                    $value = $values->get('equipment');
                    $quantity = $values->get('quantity');
                    $order_type = $values->get('order_type');
                    $product = Equipment::query()->find($value);
                    $available_items = !empty($product) ? $product->available_items :  0;
                    if ($order_type == 1) {
                        $available_items = $available_items + $quantity;
                    }

                    if ($order_type == 2) {
                        $available_items = $available_items - $quantity;
                    }
                    return $available_items;
                })->showCalculationButton(false),

            BelongsTo::make( __( 'Person' ), 'usuario', Persons::class )->nullable()->searchable()->sortable(),

            Text::make(__('Comments'), 'comments')
                ->sortable()
                ->rules('required'),
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


    public static function beforeSave(Request $request, $model)
    {
        $available_items_before = $request->get('available_items_before');
        $available_items_after = $request->get('available_items_after');
        $model->user_id = Auth::id();
        $model->available_items_before = $available_items_before;
        $model->available_items_after = $available_items_after;

    }

    public static function afterSave(Request $request, $model)
    {
        $equipment = $request->get('equipment');
        $available_items_after = $request->get('available_items_after');
        $product = Equipment::query()->find($equipment);
        $product->last_order_id = $model->id;
        $product->available_items = $available_items_after;
        $product->save();
    }

    /**
     * @param Request $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request )
    {
        return auth()->user()->can(__( "Create equipment" ));
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function authorizedToDelete(Request $request )
    {
        return auth()->user()->can(__( "Delete equipment" ));
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request )
    {
        return auth()->user()->can(__( "Update equipment" ));
    }

    /**
     * @param Request $request
     * @return true
     */
    public function authorizedToView(Request $request)
    {
        return auth()->user()->can(__( "View equipment" ));
    }


    /**
     * @param Request $request
     * @return bool
     */
    public static function authorizedToViewAny(Request $request)
    {
        return auth()->user()->can(__( "View equipment" ));
    }

}
