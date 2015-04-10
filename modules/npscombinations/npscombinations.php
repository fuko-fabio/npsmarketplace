<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if ( !defined( '_PS_VERSION_' ) )
    exit;

class NpsCombinations extends Module {

    public function __construct() {
        $this->name = 'npscombinations';
        $this->tab = 'market_place';
        $this->version = 1.0;
        $this->author = 'Norbert Pabian';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        parent::__construct();
        $this->displayName = $this->l( 'nps Products combinations' );
        $this->description = $this->l( 'Shows tab with all product combinations.' );
    }

    public function install() {
        if (!parent::install()
            || !$this->registerHook('productTab')
            || !$this->registerHook('productTabContent'))
            return false;
        return true;
    }

    public function uninstall() {
        if (!parent::uninstall()
            || !$this->unregisterHook('productTab')
            || !$this->unregisterHook('productTabContent'))
            return false;
        return true;
    }


    public function hookProductTab($params) {
        $product = $params['product'];
        if (!Product::isAdvertisment($product->id)) {
            $this->context->controller->addCss(($this->_path).'npscombinations.css');
            $this->context->controller->addJS(($this->_path).'js/npscombinations.js');
            return $this->display(__FILE__, 'tab.tpl');
        }
    }

    public function hookProductTabContent($params) {
        $product = $params['product'];
        if (!Product::isAdvertisment($product->id)) {
            $this->context->smarty->assign(array(
                'npscombinations' => $this->getCombinations($product),
            ));
            return ($this->display(__FILE__, 'tab_content.tpl'));
        }
    }

    private function getCombinations($product) {
        $result = array();
        foreach($product->getAttributeCombinations($this->context->language->id) as $key => $comb) {
            $id = $comb['id_product_attribute'];
            $group = $comb['id_attribute_group'];
            if ($group == Configuration::get('NPS_ATTRIBUTE_DATE_ID'))
                $result[$id]['date'] = $comb['attribute_name'];
            else if ($group == Configuration::get('NPS_ATTRIBUTE_TIME_ID'))
                $result[$id]['time'] = $comb['attribute_name'];
            else if ($group == Configuration::get('NPS_ATTRIBUTE_TYPE_ID'))
                $result[$id]['type'] = $comb['attribute_name'];
            else if ($group == Configuration::get('NPS_ATTRIBUTE_NAME_ID')) {
                $result[$id]['id_product_attribute'] = $id;
                $result[$id]['name'] = $comb['attribute_name'];
                $result[$id]['price'] = $comb['price'];
                $result[$id]['quantity'] = $comb['quantity'];
                $query = ProductAttributeExpiryDate::getByProductAttribute($id);
                if ($query) {
                    $date_time = new DateTime($query);
                    $result[$id]['expiry_date'] = $date_time->format('Y-m-d');
                    $result[$id]['expiry_time'] = $date_time->format('H:i');
                }
            }
        }
        return $result;
    }
}
?>
