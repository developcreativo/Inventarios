<?php

namespace Developcreativo\Inventarios\Nova;

use App\Nova\Resource;
use Developcreativo\Inventarios\BroadcasterField;
use Developcreativo\Inventarios\ListenerHiddenField;
use Developcreativo\Inventarios\Models\Equipment;
use Developcreativo\Inventarios\Nova\Actions\ActionEquipment;
use Developcreativo\Inventarios\Traits\HasCallbacks;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use KossShtukert\LaravelNovaSelect2\Select2;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class EquipmentResource extends Resource
{
    use HasCallbacks;

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

            Select2::make(__('Talla'), 'id_talla')
                ->options(\App\Claves::query()->where( 'clave', 'equipo_talla' )->pluck( 'descrip_larga', 'valor' ))
                ->configuration([
                    'placeholder'             => __('Choose an option'),
                    'allowClear'              => true,
                    'minimumResultsForSearch' => 1,
                    'multiple'                => false,
                ])->nullable()->sortable(),

            BroadcasterField::make(__('Avg Price'), 'avg_price')
                ->broadcastTo('items_value')
                ->rules('required'),

            BroadcasterField::make(__('Available Items'), 'available_items')
                ->broadcastTo('items_value'),

            Number::make(__('Items Value'), 'items_value')
                ->exceptOnForms(),
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

    public static function beforeSave(Request $request, $model)
    {
        $avg_price = $request->get('avg_price');
        $available_items = $request->get('available_items');
        $model->items_value = $avg_price * $available_items;
    }

    public static function afterSave(Request $request, $model)
    {
        $avg_price = $request->get('avg_price');
        $available_items = $request->get('available_items');
        $model->items_value = $avg_price * $available_items;
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
        return [
            ( new ActionEquipment())->canSee( function ( $request){
                return true;
            } )
        ];
    }

    /**
     * @param Request $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request )
    {
        return auth()->user()->can(__( "Create Orders" ));
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function authorizedToDelete(Request $request )
    {
        return auth()->user()->can(__( "Delete Orders" ));
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request )
    {
        return auth()->user()->can(__( "Update Orders" ));
    }

    /**
     * @param Request $request
     * @return true
     */
    public function authorizedToView(Request $request)
    {
        return true;
    }


    /**
     * @param Request $request
     * @return bool
     */
    public static function authorizedToViewAny(Request $request)
    {
        return auth()->user()->can(__( "View Orders" ));
    }
}
