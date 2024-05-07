<?php

namespace Classiebit\Eventmie\FormFields;
use Facades\Classiebit\Eventmie\Eventmie;

use TCG\Voyager\FormFields\AbstractHandler;

use Classiebit\Eventmie\Models\Country;

class CountryDropdown extends AbstractHandler
{
    protected $codename = 'country';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        $this->country         = new Country;
        // get countries
        $countries         = $this->country->get_countries(false); 
        
        $view = 'eventmie::vendor.voyager.formfields.country';

        return Eventmie::view($view, [
            'row' => $row,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent,
            'countries' => $countries,

        ]);
    }
}