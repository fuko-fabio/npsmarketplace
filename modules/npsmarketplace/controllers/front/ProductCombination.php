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

    public function setMedia()
    {
        parent::setMedia();
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/datetime_init.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap.min.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap-datetimepicker.min.js');

        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap.css');
        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap-datetimepicker.min.css');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('date_time') && Tools::isSubmit('quantity'))
        {
            if (!Combination::isFeatureActive())
                return;
            $date_time = trim(Tools::getValue('date_time'));
            $quantity = trim(Tools::getValue('quantity'));
            $seller = new Seller(null, $this->context->customer->id);
            $attr = new Attribute();
            $attr->name[$this->context->language->id] = $date_time;
            $attr->id_attribute_group = Configuration::get('NPS_ATTRIBUTE_DT_ID');
            $attr->position = -1;
            $attr->save();

            $id_combination = $this->_product->addAttribute(
                0,//$price,
                null,//$weight,
                null,//$unit_impact,
                null,//$ecotax,
                null,//$id_images,
                null,//$reference,
                null,//$ean13,
                false);
           $combination = new Combination($id_combination);
           $combination->setAttributes(array($attr->id));
           StockAvailable::setQuantity($this->_product->id, $attr->id, $quantity, $this->context->shop->id);
           Tools::redirect('index.php?fc=module&module=npsmarketplace&controller=ProductsList');
        }
    }

    /**
     * Initialize controller
     * @see FrontController::init()
     */
    public function init()
    {
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

    public function initContent()
    {
        parent::initContent();

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
            'user_agreement_url' =>'#',
            'product' => $tpl_product,
            'current_id_lang' => (int)$this->context->language->id,
            'languages' => Language::getLanguages(),
        ));
        $this->setTemplate('product_combination.tpl');
    }
}
?>