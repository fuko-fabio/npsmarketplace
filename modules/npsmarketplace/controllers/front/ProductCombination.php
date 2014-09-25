<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsMarketplaceProductCombinationModuleFrontController extends ModuleFrontController
{
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
            $date = trim(Tools::getValue('date'));
            $time = trim(Tools::getValue('time'));
            $quantity = trim(Tools::getValue('quantity'));
            $available_date = trim(Tools::getValue('available_date'));

            $d = array();
            $t = array();
            foreach (Language::getLanguages() as $key => $lang) {
                $d[$lang['id_lang']] = $date;
                $t[$lang['id_lang']] = $time;
            }

            $date_attr = new Attribute();
            $date_attr->name = $d;
            $date_attr->id_attribute_group = Configuration::get('NPS_ATTRIBUTE_DATE_ID');
            $date_attr->position = -1;
            $date_attr->save();

            $time_attr = new Attribute();
            $time_attr->name = $t;
            $time_attr->id_attribute_group = Configuration::get('NPS_ATTRIBUTE_TIME_ID');
            $time_attr->position = -1;
            $time_attr->save();

            $id_product_attribute = $this->_product->addCombinationEntity(
                0,//$wholesale_price
                0,//$price
                0,//$weight
                0,//$unit_impact
                0,//$ecotax
                $quantity,
                0,//$id_images
                0,//$reference,
                null,//$id_supplier
                0,//$ean13
                false,//$default
                null,//$location = null
                null,//$upc = null
                1,//$minimal_quantity = 1
                array(),//$id_shop_list = array()
                $available_date);
            StockAvailable::setProductDependsOnStock((int)$this->_product->id, $this->_product->depends_on_stock, null, (int)$id_product_attribute);
            StockAvailable::setProductOutOfStock((int)$this->_product->id, $this->_product->out_of_stock, null, (int)$id_product_attribute);

            $combination = new Combination((int)$id_product_attribute);
            $combination->setAttributes(array($date_attr->id, $time_attr->id));

            StockAvailable::setQuantity((int)$this->_product->id, (int)$id_product_attribute, $quantity, $this->context->shop->id);
            Tools::redirect('index.php?fc=module&module=npsmarketplace&controller=ProductsList');
        }
    }

    /**
     * Initialize controller
     * @see FrontController::init()
     */
    public function init() {
        parent::init();

        $id_product = (int)Tools::getValue('id_product', 0);

        if ($id_product) {
            $this->_product = new Product($id_product);
            $seller = new Seller(null, $this->context->customer->id);
            if (Validate::isLoadedObject($this->_product) && Validate::isLoadedObject($seller) && Seller::sellerHasProduct($seller->id, $id_product)) {
                if (Tools::isSubmit('delete'))
                    d('delete');
            }
            elseif ($this->ajax)
                exit;
            else
                Tools::redirect('index.php?fc=module&module=npsmarketplace&controller=ProductsList');
        }
    }

    private function getFeatureValue($features, $name) {
        foreach ($features as $featre) {
            if ($featre['id_feature'] == Configuration::get('NPS_FEATURE_'.strtoupper($name).'_ID')) {
                $f = new FeatureValue($featre['id_feature_value']);
                return $f->value[$this->context->language->id];
            }
        }
    }

    public function initContent() {
        parent::initContent();
        if (!$this->context->customer->isLogged() && $this->php_self != 'authentication' && $this->php_self != 'password')
            Tools::redirect('index.php?controller=authentication?back=my-account');
        $seller = new Seller(null, $this->context->customer->id);
        if ($seller->id == null) 
            Tools::redirect('index.php?controller=my-account');

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