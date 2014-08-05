<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/CategoriesList.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/ProductRequestProcessor.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class NpsMarketplaceProductModuleFrontController extends ModuleFrontController
{

        /**
     * @var _product Current product
     */
    protected $_product;

    public function setMedia()
    {
        parent::setMedia();
        $this -> addJS ("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");
        $this -> addJS(_PS_JS_DIR_.'validate.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/product_map.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/datetime_init.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap.min.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap-datetimepicker.min.js');

        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap.css');
        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap-datetimepicker.min.css');
        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/map.css');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('product_name')
            && Tools::isSubmit('product_price')
            && Tools::isSubmit('product_amount')
            && Tools::isSubmit('product_town')
            && Tools::isSubmit('product_address')
            && Tools::isSubmit('product_district'))
        {
            $seller = new Seller(null, $this->context->customer->id);
            $pp = new ProductRequestProcessor($this->context);
            $product = $pp->processSubmit($seller->active);
            $this->errors = $pp->errors;
            if(empty($this->errors))
            {
                if (!$seller->assignProduct($product->id))
                    $this->errors[] = Tools::displayError('An error occurred while updating seller information.');
                Tools::redirect('index.php?fc=module&module=npsmarketplace&controller=ProductsList');
            }
        }
    }

    /**
     * Initialize product controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        
        $id_product = (int)Tools::getValue('id_product', 0);
        // Initialize product
        if ($id_product)
        {
            $this->_product = new Product($id_product);
            $seller = new Seller(null, $this->context->customer->id);
            if (Validate::isLoadedObject($this->_product) && Validate::isLoadedObject($seller) && Seller::sellerHasProduct($seller->id, $id_product))
            {
                if (Tools::isSubmit('delete'))
                {
                    if ($this->_product->delete())
                        Tools::redirect('index.php?fc=module&module=npsmarketplace&controller=ProductsList');
                    $this->errors[] = Tools::displayError('This product cannot be deleted.');
                }
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

        $tpl_product = array('categories' => array(), 'town' => null);
        if (isset($this->_product->id)) {
            $features = $this->_product->getFeatures();
            $tpl_product = array(
                'id' => $this->_product->id,
                'name' => $this->_product->name,
                'description_short' => $this->_product->description_short,
                'description' => $this->_product->description,
                'price' => $this->_product->getPrice(),
                'quantity' => Product::getQuantity($this->_product->id),
                'reference' => $this->_product->reference,
                'town' => $this->getFeatureValue($features, 'town'),
                'address' => $this->getFeatureValue($features, 'address'),
                'district' => $this->getFeatureValue($features, 'district'),
                'categories' => $this->_product->getCategories(),
            );
        }
        // TODO Read towns
        $towns = array('Kraków', 'Warszawa');
        $categoriesList = new CategoriesList($this->context);
        $this -> context -> smarty -> assign(array(
            'user_agreement_url' =>'#',
            'categories_tree' => $categoriesList -> getTree(),
            'category_partial_tpl_path' =>_PS_MODULE_DIR_.'npsmarketplace/views/templates/front/category_tree_partial.tpl',
            'product_fieldset_tpl_path'=> _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/product_fieldset.tpl',
            'product' => $tpl_product,
            'edit_product' => array_key_exists('id', $tpl_product),
            'current_id_lang' => (int)$this->context->language->id,
            'languages' => Language::getLanguages(),
            'towns' => $towns,
        ));

        $this->setTemplate('product.tpl');
    }
}
?>