<?php

namespace Developcreativo\Inventarios;

use Developcreativo\Inventarios\Nova\EquipmentOrderExpiryResource;
use Developcreativo\Inventarios\Nova\EquipmentOrderResource;
use Developcreativo\Inventarios\Nova\EquipmentResource;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class Inventarios extends Tool
{
    public $equipmentOrderResource = EquipmentOrderResource::class;
    protected $equipmentResource = EquipmentResource::class;

    protected $equipmentOrderExpiryResource = EquipmentOrderExpiryResource::class;
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::resources([
            $this->equipmentOrderResource,
            $this->equipmentResource,
            $this->equipmentOrderExpiryResource,
        ]);
        Nova::script('inventarios', __DIR__.'/../dist/js/tool.js');
        Nova::style('inventarios', __DIR__.'/../dist/css/tool.css');
    }


    /**
     * @param string $roleResource
     *
     * @return mixed
     */
    public function equipmentResource($resourceClass)
    {
        $this->equipmentResource = $resourceClass;
        return $this;
    }

    public function equipmentOrderResource($resourceClass)
    {
        $this->equipmentOrderResource = $resourceClass;
        return $this;
    }
}
