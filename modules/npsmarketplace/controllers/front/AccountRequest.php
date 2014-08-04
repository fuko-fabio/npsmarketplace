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
        $this -> addJS(_PS_JS_DIR_.'validate.js');
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
                $this->mailToSeller($seller);
                $this->mailToAdmin($seller);
                Tools::redirect('index.php?controller=my-account' );
            }
        }
    }

    private function mailToAdmin($seller) {
        $customer = $this->context->customer;
        $mail_params = array(
            '{lastname}' => $customer->lastname,
            '{firstname}' => $customer->firstname,
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
            '{seller_shop_url}' => 'TODO',
            '{product_guide_url}' => 'TODO',
            '{seller_guide_url}' => 'TODO',
        );
        Mail::Send($this->context->language->id,
                   'seller_account_request',
                   Mail::l('Seller account request'),
                   $mail_params,
                   $customer->email,
                   $customer->firstname.' '.$customer->lastname);
    }

    private function mailToAdmin() {
        $customer = $this->context->customer;
        $mail_params = array(
            '{lastname}' => $customer->lastname,
            '{firstname}' => $customer->firstname,
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__
        );
        Mail::Send($this->context->language->id,
                   'seller_account_request',
                   Mail::l('Seller account request'),
                   $mail_params,
                   $customer->email,
                   $customer->firstname.' '.$customer->lastname);
    }

    public function initContent() {
    $this -> page_name =
 'accountrequest';
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

        $this -> context -> smarty -> assign(
            array(
                'seller' => array('image' => ''),
                'account_state' => $account_state,
                'account_request_date' => $date,
                'current_id_lang' => (int)$this->context->language->id,
                'languages' => Language::getLanguages(),
                'user_agreement_url' => '#', #TODO Set real url's
                'processing_data_url' => '#',
                'seller_fieldset_tpl_path' => _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/seller_fieldset.tpl',
            )
        );

        $this -> setTemplate('account_request.tpl');
    }

 }
?>