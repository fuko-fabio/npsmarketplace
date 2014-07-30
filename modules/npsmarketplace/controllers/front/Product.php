<?php
/*
*  @author Norbert Pabian
*  @copyright  
*  @license    
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
        $this -> addJS(_PS_JS_DIR_.'validate.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/product.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap.min.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap-datetimepicker.min.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/fileinput.min.js');

        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap.css');
        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap-datetimepicker.min.css');
        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/fileinput.css');
        
    }

    public function postProcess()
    {
        if (Tools::isSubmit('product_name')
            && Tools::isSubmit('product_price')
            && Tools::isSubmit('product_amount')
            && Tools::isSubmit('product_date')
            && Tools::isSubmit('product_time'))
        {
            $pp = new ProductRequestProcessor($this->context);
            $product = $pp->processAdd(1);
            $this->errors = $pp->errors;
            if(empty($this->errors))
            {
                $seller = new Seller(null, $this->context->customer->id);
                if (!Validate::isLoadedObject($seller))
                    $this->errors[] = Tools::displayError('An error occurred while updating seller information.');
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

    public function initContent()
    {
        parent::initContent();
        
        $tpl_product = array('categories' => array());
        if (isset($this->_product->id)) {
            $tpl_product = array(
                'id' => $this->_product->id,
                'name' => $this->_product->name,
                'description_short' => $this->_product->description_short,
                'description' => $this->_product->description,
                'price' => $this->_product->getPrice(),
                'quantity' => Product::getQuantity($this->_product->id),
                'reference' => $this->_product->reference,
                'available_date' => $this->_product->available_date,
                'available_time' => '', #TODO
                'categories' => $this->_product->getCategories(),
            );
        }

        $categoriesList = new CategoriesList($this->context);
        $this -> context -> smarty -> assign(array(
            'user_agreement_url' =>'#',
            'categories_tree' => $categoriesList -> getTree(),
            'category_partial_tpl_path' =>_PS_MODULE_DIR_.'npsmarketplace/views/templates/front/category_tree_partial.tpl',
            'product_fieldset_tpl_path'=> _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/product_fieldset.tpl',
            'product' => $tpl_product,
            'current_id_lang' => (int)$this->context->language->id,
            'languages' => Language::getLanguages()
        ));

        $this->setTemplate('product.tpl');
    }
}
?>