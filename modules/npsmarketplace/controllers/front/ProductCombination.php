<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsMarketplaceProductCombinationModuleFrontController extends ModuleFrontController {

    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;

     /**
     * @var _product Current product
     */
    protected $_product;

    public function setMedia() {
        parent::setMedia();
        $this->addJS (_PS_MODULE_DIR_.'npsmarketplace/js/datetime_init.js');
        $this->addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap-datetimepicker.min.js');
        $this->addJS(_PS_JS_DIR_.'validate.js');

        $this->addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap-datetimepicker.min.css');
    }

    public function postProcess() {
        if (Tools::isSubmit('submitCombination')) {
            if (!Combination::isFeatureActive())
                return;
            $seller = new Seller(null, $this->context->customer->id);
            if ($seller->id == null || !$seller->active || $seller->locked)
                Tools::redirect('index.php?controller=my-account');

            $date = trim(Tools::getValue('date'));
            $time = trim(Tools::getValue('time'));
            $quantity = trim(Tools::getValue('quantity'));
            $expiry_date = trim(Tools::getValue('expiry_date'));
            $expiry_time = trim(Tools::getValue('expiry_time'));
            
            if (!$this->isUnique($this->_product, $date, $time))
                $this -> errors[] = $this->module->l('Event with provided date and time already exists', 'ProductCombination');

            if (empty($expiry_date))
                $this -> errors[] = $this->module->l('Product expiry date is required', 'ProductCombination');
            else if (!Validate::isDateFormat($expiry_date))
                $this -> errors[] = $this->module->l('Invalid expiry date format', 'ProductCombination');

            if (empty($date))
                $this -> errors[] = $this->module->l('Product date is required', 'ProductCombination');
            else if (!Validate::isDateFormat($date))
                $this -> errors[] = $this->module->l('Invalid date format', 'ProductCombination');

            if (empty($time))
                $this -> errors[] = $this->module->l('Product time is required', 'ProductCombination');
            else if (!Validate::isTime($time))
                $this -> errors[] = $this->module->l('Invalid date format', 'ProductCombination');

            if (empty($expiry_time))
                $this -> errors[] = $this->module->l('Product expiry time is required', 'Product');
            else if (!Validate::isTime($expiry_time))
                $this -> errors[] = $this->module->l('Invalid expiry time format', 'Product');

            if (strtotime($date.' '.$time) < time())
                $this -> errors[] = $this->module->l('You are trying to add event in the past. Check event date and time.', 'Product');
            if (strtotime($expiry_date.' '.$expiry_time) > strtotime($date.' '.$time))
                $this -> errors[] = $this->module->l('Expiry date and time cannot be later than event date.', 'Product');

            if (empty($quantity))
                $this -> errors[] = $this->module->l('Product quantity is required', 'ProductCombination');
            else if (!Validate::isInt($quantity))
                $this -> errors[] = $this->module->l('Invalid product quantity format', 'ProductCombination');

            if (empty($this->errors)) {
                $dt = new DateTime($expiry_date.' '.$expiry_time);
                if ($time == $expiry_time)
                    $dt->modify('-15 min');
                $this->_product->newEventCombination($date, $time, (int)$quantity, $dt, $this->context->shop->id);
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
            $settings = new P24SellerCompany(null, $seller->id);
            if ($settings->id != null) {
                $default_product = new Product((int)$product->id, false, null, (int)$product->id_shop_default);
                $default_product->toggleStatus();
            }
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
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();

        $seller = new Seller(null, $this->context->customer->id);
        if ($seller->id == null || !$seller->active)
            Tools::redirect('index.php?controller=my-account');
        else if ($seller->locked)
            Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'UnlockAccount'));

        $extras = Product::getExtras($this->_product->id, $this->context->language->id);
        if ($extras && $extras['type'] != 0)
            Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'ProductsList'));
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
            'user_agreement_url' => Configuration::get('NPS_SELLER_AGREEMENT_URL'),
            'product' => $tpl_product,
            'current_id_lang' => (int)$this->context->language->id,
            'languages' => Language::getLanguages(),
        ));
        $this->setTemplate('product_combination.tpl');
    }
}
?>