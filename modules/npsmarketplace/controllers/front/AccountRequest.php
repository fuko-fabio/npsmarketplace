<?php
/*
*  @author Norbert Pabian
*  @copyright  
*  @license    
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/CategoriesList.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/SellerRequestProcessor.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/ProductRequestProcessor.php');

class NpsMarketplaceAccountRequestModuleFrontController extends ModuleFrontController {

    public function setMedia()
    {
        parent::setMedia();
        $this -> addJS ("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places");
        $this -> addJS(_PS_JS_DIR_.'validate.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/product.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap.min.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap-datetimepicker.min.js');
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/fileinput.min.js');
        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap.css');
        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap-datetimepicker.min.css');
        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/map.css');
        
    }

    public function postProcess()
    {
        if (Tools::isSubmit('company_name')
            && Tools::isSubmit('seller_name')
            && Tools::isSubmit('seller_phone')
            && Tools::isSubmit('seller_email')
            && Tools::isSubmit('seller_nip')
            && Tools::isSubmit('seller_regon'))
        {
            $sp = new SellerRequestProcessor($this->context);
            $seller = $sp->processSubmit();
            $this->errors = $sp->errors;
            if(empty($this->errors))
            {
                if (Tools::getValue('add_product') == 'on')
                {
                    $pp = new ProductRequestProcessor($this->context);
                    $product = $pp->processSubmit();
                    $this->errors = $pp->errors;
                    if(empty($this->errors)) {
                        $seller->assignProduct($product->id);
                    }
                }
                $customer = $this->context->customer;
                $mail_params = array(
                    '{lastname}' => $customer->lastname,
                    '{firstname}' => $customer->firstname,
                    '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                    '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__
                );
                if (Mail::Send($this->context->language->id, 'seller_account_request', Mail::l('Seller account request'), $mail_params, $customer->email, $customer->firstname.' '.$customer->lastname))
                    $this->context->smarty->assign(array('confirmation' => 2, 'customer_email' => $customer->email));
                else
                   $this->errors[] = Tools::displayError('An error occurred while sending the email.');

                Tools::redirect('index.php?controller=my-account');
            }
        }
    }

    public function initContent() {
        $this -> page_name = 'accountrequest';
        $this -> display_column_right = false;
        parent::initContent();

        if (!$this->context->customer->isLogged() && $this->php_self != 'authentication' && $this->php_self != 'password')
            Tools::redirect('index.php?controller=authentication?back=my-account'); 
        $id_customer = $this -> context -> customer -> id;
        if ($id_customer)
        {
            $query = new DbQuery();
            $query
                -> select('*')
                -> from('seller')
                -> where('`id_customer` = '.$id_customer);
            $account_state = 'none';
            $date = null;
            if ($result = Db::getInstance() -> executeS($query))
            {
                $date = $result[0]['request_date'];
                $active = $result[0]['active'];
                $locked = $result[0]['locked'];
                $requested = $result[0]['requested'];
                if ($requested == 1 && $active == 0 && $locked == 0)
                    $account_state = 'requested';
                else if ($requested == 1 && $active == 1 && $locked == 0)
                    $account_state = 'active';
                else if ($requested == 1 && $locked == 1)
                    $account_state = 'locked';
            }
        }

        $categoriesList = new CategoriesList($this->context);
        $tpl_product = array('categories' => array());

        $this -> context -> smarty -> assign(
            array(
                'seller' => array('image' => ''),
                'account_state' => $account_state,
                'account_request_date' => $date,
                'product' => $tpl_product,
                'current_id_lang' => (int)$this->context->language->id,
                'languages' => Language::getLanguages(),
                'user_agreement_url' => '#', #TODO Set real url's
                'processing_data_url' => '#',
                'categories_tree' => $categoriesList -> getTree(),
                'category_partial_tpl_path' => _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/category_tree_partial.tpl',
                'product_fieldset_tpl_path'=> _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/product_fieldset.tpl',
                'seller_fieldset_tpl_path' => _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/seller_fieldset.tpl',
            )
        );

        $this -> setTemplate('account_request.tpl');
    }

 }
?>