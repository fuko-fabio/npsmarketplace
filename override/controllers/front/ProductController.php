<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class ProductController extends ProductControllerCore {

    public function init() {
        parent::init();
        if (!empty($this->errors))
            Tools::redirectAdmin($this->context->link->getPageLink('not-found'));
    }

    public function initContent() {
        $this->context->smarty->assign(array(
            'HOOK_EXTRA_PRODUCT_IMAGE' => Hook::exec('displayExtraProductImage'),
            'extras' => Product::getExtras($this->product->id, $this->context->language->id),
            'date_id_attribute_group' => Configuration::get('NPS_ATTRIBUTE_DATE_ID'),
            'time_id_attribute_group' => Configuration::get('NPS_ATTRIBUTE_TIME_ID'),
            'type_id_attribute_group' => Configuration::get('NPS_ATTRIBUTE_TYPE_ID'),
            'name_id_attribute_group' => Configuration::get('NPS_ATTRIBUTE_NAME_ID')
        ));

        parent::initContent();
    }
}
