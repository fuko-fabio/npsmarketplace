<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsMarketplaceProductCombinationModuleFrontController extends ModuleFrontController {

     /**
     * @var _product Current product
     */
    protected $_product;

    public function setMedia() {
        parent::setMedia();
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/datetime_init.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap-datetimepicker.min.js');

        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap-datetimepicker.min.css');
    }

    public function postProcess() {
        if (Tools::isSubmit('submitCombination')) {
            if (!Combination::isFeatureActive())
                return;
            $seller = new Seller(null, $this->context->customer->id);
            if ($seller->id == null || !$seller->active || $seller->locked)
                Tools::redirect('index.php?controller=my-account');

            $module = new NpsMarketplace();
            $date = trim(Tools::getValue('date'));
            $time = trim(Tools::getValue('time'));
            $quantity = trim(Tools::getValue('quantity'));
            $expiry_date = trim(Tools::getValue('expiry_date'));
            
            if (!$this->isUnique($this->_product, $date, $time))
                $this -> errors[] = $module->l('Event with provided date and time already exists');

            if (empty($expiry_date))
                $this -> errors[] = $module->l('Product expiry date is required');
            else if (!Validate::isDateFormat($expiry_date))
                $this -> errors[] = $module->l('Invalid expiry date format');

            if (empty($date))
                $this -> errors[] = $module->l('Product date is required');
            else if (!Validate::isDateFormat($date))
                $this -> errors[] = $module->l('Invalid date format');

            if (empty($time))
                $this -> errors[] = $module->l('Product time is required');
            else if (!Validate::isTime($time))
                $this -> errors[] = $module->l('Invalid date format');

            if (empty($quantity))
                $this -> errors[] = $module->l('Product quantity is required');
            else if (!Validate::isInt($quantity))
                $this -> errors[] = $module->l('Invalid product quantity format');

            if (empty($this->errors)) {
                $this->_product->newEventCombination($date, $time, (int)$quantity, $expiry_date, $this->context->shop->id);
                $this->enableProductIfNeeded($seller, $this->_product);
                Tools::redirect('index.php?fc=module&module=npsmarketplace&controller=ProductsList');
            }
        }
    }

    /**
     * Initialize controller
     * @see FrontController::init()
     */
    public function init() {
        parent::init();

        $id_product = (int)Tools::getValue('id_product', 0);
        if ($id_product)
            $this->_product = new Product($id_product);
    }

    private function getFeatureValue($features, $name) {
        foreach ($features as $featre) {
            if ($featre['id_feature'] == Configuration::get('NPS_FEATURE_'.strtoupper($name).'_ID')) {
                $f = new FeatureValue($featre['id_feature_value']);
                return $f->value[$this->context->language->id];
            }
        }
    }

    private function enableProductIfNeeded($seller, $product) {
        if ($seller->active && !$seller->locked && !$product->active) {
            $default_product = new Product((int)$product->id, false, null, (int)$product->id_shop_default);
            $default_product->toggleStatus();
        }
    }

    private function isUnique($product, $date, $time) {
        $attributes = $product->getAttributeCombinations($this->context->language->id);
        $times = array();
        $dates = array();
        $date_attr_id = Configuration::get('NPS_ATTRIBUTE_DATE_ID');
        $time_attr_id = Configuration::get('NPS_ATTRIBUTE_TIME_ID');
        foreach ($attributes as $attr) {
            if ($attr['id_attribute_group'] == $date_attr_id) {
                $dates[] = $attr;
                continue;
            }
            if ($attr['id_attribute_group'] == $time_attr_id)
                $times[] = $attr;
        }
        $dates = array_filter($dates, function($el) use($date){ return $el['attribute_name'] == $date; });
        $times = array_filter($times, function($el) use($time){ return $el['attribute_name'] == $time; });

        if(empty($dates) || empty($times))
            return true;

        foreach ($dates as $d) {
            foreach ($times as $t) {
                if($d['id_product_attribute'] == $t['id_product_attribute'])
                    return false;
            }
        }
        return true;
    }

    public function initContent() {
        parent::initContent();
        if (!$this->context->customer->isLogged() && $this->php_self != 'authentication' && $this->php_self != 'password')
            Tools::redirect('index.php?controller=authentication?back=my-account');
        $seller = new Seller(null, $this->context->customer->id);
        if ($seller->id == null || !$seller->active)
            Tools::redirect('index.php?controller=my-account');
        else if ($seller->locked)
            Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'UnlockAccount'));

        $tpl_product = array();
        if (isset($this->_product->id)) {
            $features = $this->_product->getFeatures();
            $tpl_product = array(
                'id' => $this->_product->id,
                'name' => $this->_product->name,
                'description_short' => $this->_product->description_short,
                'description' => $this->_product->description,
                'price' => $this->_product->getPrice(),
                'reference' => $this->_product->reference,
                'town' => $this->getFeatureValue($features, 'town'),
                'address' => $this->getFeatureValue($features, 'address'),
                'district' => $this->getFeatureValue($features, 'district'),
            );
        }
        $this -> context -> smarty -> assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'user_agreement_url' =>'#',
            'product' => $tpl_product,
            'current_id_lang' => (int)$this->context->language->id,
            'languages' => Language::getLanguages(),
        ));
        $this->setTemplate('product_combination.tpl');
    }
}
?>