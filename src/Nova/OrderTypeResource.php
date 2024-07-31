<?php

namespace Developcreativo\Inventarios\Nova;

use App\Nova\Resource;
use Developcreativo\Inventarios\Models\OrderType;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class OrderTypeResource extends Resource
{
    public static $model = OrderType::class;

    public static $title = 'descrip_corta';

    public static $displayInNavigation = false;

    public static function label()
    {
        return __('Order Type');
    }

    /**
     * @return string
     */
    public static function singularLabel()
    {
        return __('Order Type');
    }

    public static $search = [
        'valor',
        'descrip_corta'
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make(__('Value'),'valor')->hideWhenCreating()->hideWhenUpdating(),
            Text::make(__('Short description'),'descrip_corta')->rules('required'),
            Textarea::make(__('Long description'),'descrip_larga')->rules('required')->alwaysShow()
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
