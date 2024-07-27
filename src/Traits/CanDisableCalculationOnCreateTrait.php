<?php

namespace Developcreativo\Inventarios\Traits;
use Laravel\Nova\Http\Requests\NovaRequest;

trait CanDisableCalculationOnCreateTrait
{
    /**
     * Ability to disable calculation on update form
     *
     * @var bool
     */
    public $disableOnCreate = false;

    /*
     * Function to disable calculation on update form
     */
    public function disableCalculationOnCreate($bool = true) {
        $this->disableOnCreate = $bool;
        return $this;
    }
}