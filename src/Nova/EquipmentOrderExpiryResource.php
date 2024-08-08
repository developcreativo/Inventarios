<?php

namespace Developcreativo\Inventarios\Nova;

use App\Nova\Persons;
use App\Nova\Resource;
use Developcreativo\Inventarios\Models\EquipmentOrder;
use Developcreativo\Inventarios\Nova\Actions\ActionDistributions;
use Developcreativo\Inventarios\Nova\Actions\ActionEquipmentOrderViewer;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class EquipmentOrderExpiryResource extends Resource
{
    public static $model = EquipmentOrder::class;

    public static $title = 'id';

    public static $search = [
        'id', 'order_date'
    ];

    public static function label()
    {
        return __('Visor vencimiento equipamiento');
    }

    public static function singularLabel()
    {
        return __('Visor vencimiento equipamiento');
    }

    public static function group() {
        return __('Inventarios');
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query->whereNotNull('id_usuario')->where('order_type', 2);
    }
    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make( __( 'Person' ), 'usuario', Persons::class )->nullable()->searchable()->sortable(),
            Text::make(__('Branch'), function () {
                $ubicacion = $this->usuario->ubicacion;
                if (!isset($ubicacion) || !isset($ubicacion->sucursales)) {
                    return '';
                }

                return isset($ubicacion->sucursales['nombre_sucursal']) ? $ubicacion->sucursales['nombre_sucursal'] : '';
            }),

            Text::make(__('Customer'), function () {
                $cliente = $this->usuario->cliente;
                if (!isset($cliente)) {
                    return '';
                }

                return $cliente['nombre_cliente'];
            }),

            Text::make(__('Location'), function () {
                $ubicacion = $this->usuario->ubicacion;
                if (!isset($ubicacion)) {
                    return '';
                }

                return $ubicacion['nombre_ubicacion'];
            }),
            BelongsTo::make( __( 'Equipment' ), 'equipment', EquipmentResource::class )->nullable()->searchable()->sortable(),

            Date::make(__('Fecha Entrega'), 'order_date')
                ->sortable()
                ->rules('required', 'date'),

            Date::make(__('Fecha vencimiento'), 'valid_until')
                ->sortable()
                ->rules('required', 'date'),

            Number::make(__('Días restantes'), function () {
                $validUntil = $this->valid_until;
                $today = now();

                if ($validUntil) {
                    $remainingDays = $today <= $validUntil ? $validUntil->diffInDays($today, false) : 0;
                    $remainingDays = abs($remainingDays);
                } else {
                    $remainingDays = 0;
                }

                return $remainingDays;
            }),
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
        return [
            (new ActionEquipmentOrderViewer()),
            (new ActionDistributions())
        ];
    }

    /**
     * @param Request $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request )
    {
        return false;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function authorizedToDelete(Request $request )
    {
        return false;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request )
    {
        return false;
    }
}
