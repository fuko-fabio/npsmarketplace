<?php
/*
*  @author Norbert Pabian
*  @copyright  
*  @license    
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/CategoriesList.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/ProductRequestProcessor.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class NpsMarketplaceAddProductModuleFrontController extends ModuleFrontController
{
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
            $pp = new ProductRequestProcessor();
            $pp = new ProductRequestProcessor();
            $product = $pp->processAdd();
            $this->errors = $pp->errors;
            if(empty($this->errors))
            {
                $seller = new SellerCore(null, $this->context->customer->id);
                if (!Validate::isLoadedObject($seller) && !$seller->assignProduct($product->id))
                    $this->errors[] = Tools::displayError('An error occurred while updating seller information.');
            }
        }
    }

  public function initContent()
  {
    parent::initContent();
    
    $categoriesList = new CategoriesList($this->context);
    $this -> context -> smarty -> assign('user_agreement_url', '#');
    $this -> context -> smarty -> assign('categories_tree', $categoriesList -> getTree());
    $this -> context -> smarty -> assign('category_partial_tpl_path', _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/category_tree_partial.tpl');
    $this -> context -> smarty -> assign('product_fieldset_tpl_path', _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/product_fieldset_partial.tpl');
    
    $this->setTemplate('addproduct.tpl');
  }
}
?>