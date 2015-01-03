<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class SellerHelper {

    public function __construct($context, $address, $errors) {
        $this->_address = $address;
        $this->errors = $errors;
        $this->context = $context;
    }

    public function initAddressContent() {
        $this->assignCountries();
        $this->assignVatNumber();
        $this->assignAddressFormat();

        // Assign common vars
        $this->context->smarty->assign(array(
            'address_validation' => Address::$definition['fields'],
            'one_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'),
            'onr_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'), //retro compat
            'ajaxurl' => _MODULE_DIR_,
            'errors' => $this->errors,
            'token' => Tools::getToken(false),
            'select_address' => (int)Tools::getValue('select_address'),
            'address' => $this->_address,
            'id_address' => (Validate::isLoadedObject($this->_address)) ? $this->_address->id : 0,
        ));
    }

    /**
     * Assign template vars related to countries display
     */
    protected function assignCountries()
    {
        // Get selected country
        if (Tools::isSubmit('id_country') && !is_null(Tools::getValue('id_country')) && is_numeric(Tools::getValue('id_country')))
            $selected_country = (int)Tools::getValue('id_country');
        else if (isset($this->_address) && isset($this->_address->id_country) && !empty($this->_address->id_country) && is_numeric($this->_address->id_country))
            $selected_country = (int)$this->_address->id_country;
        else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        {
            // get all countries as language (xy) or language-country (wz-XY)
            $array = array();
            preg_match("#(?<=-)\w\w|\w\w(?!-)#",$_SERVER['HTTP_ACCEPT_LANGUAGE'],$array);
            if (!Validate::isLanguageIsoCode($array[0]) || !($selected_country = Country::getByIso($array[0])))
                $selected_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
        }
        else
            $selected_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');

        // Generate countries list
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES'))
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        else
            $countries = Country::getCountries($this->context->language->id, true);

        // @todo use helper
        $list = '';
        foreach ($countries as $country)
        {
            $selected = ($country['id_country'] == $selected_country) ? 'selected="selected"' : '';
            $list .= '<option value="'.(int)$country['id_country'].'" '.$selected.'>'.htmlentities($country['name'], ENT_COMPAT, 'UTF-8').'</option>';
        }

        // Assign vars
        $this->context->smarty->assign(array(
            'countries_list' => $list,
            'countries' => $countries,
        ));
    }

    /**
     * Assign template vars related to address format
     */
    protected function assignAddressFormat()
    {
        $id_country = is_null($this->_address)? 0 : (int)$this->_address->id_country;
        $ordered_adr_fields = AddressFormat::getOrderedAddressFields($id_country, true, true);
        $this->context->smarty->assign('ordered_adr_fields', $ordered_adr_fields);
    }

    /**
     * Assign template vars related to vat number
     * @todo move this in vatnumber module !
     */
    protected function assignVatNumber()
    {
        $vat_number_exists = file_exists(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');
        $vat_number_management = Configuration::get('VATNUMBER_MANAGEMENT');
        if ($vat_number_management && $vat_number_exists)
            include_once(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');

        if ($vat_number_management && $vat_number_exists && VatNumber::isApplicable(Configuration::get('PS_COUNTRY_DEFAULT')))
            $vat_display = 2;
        else if ($vat_number_management)
            $vat_display = 1;
        else
            $vat_display = 0;

        $this->context->smarty->assign(array(
            'vatnumber_ajax_call' => file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php'),
            'vat_display' => $vat_display,
        ));
    }
}