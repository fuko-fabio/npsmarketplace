<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/CategoriesList.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/SellerRequestProcessor.php');
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/ProductRequestProcessor.php');

class NpsMarketplaceAccountRequestModuleFrontController extends ModuleFrontController {

    private $_merchant_mails;
    const __MA_MAIL_DELIMITOR__ = ',';

    public function setMedia()
    {
        parent::setMedia();
        $this -> addJS(array(
                _PS_JS_DIR_.'validate.js',
                _PS_JS_DIR_.'tiny_mce/tiny_mce.js',
                _PS_JS_DIR_.'tinymce.inc.js',
            ));
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

    private function mailToSeller($seller) {
        $customer = $this->context->customer;
        $mail_params = array(
            '{lastname}' => $customer->lastname,
            '{firstname}' => $customer->firstname,
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
            '{seller_shop_url}' => $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $seller->id)),
            '{product_guide_url}' => Configuration::get('NPS_PRODUCT_GUIDE_URL'),
            '{seller_guide_url}' => Configuration::get('NPS_SELLER_GUIDE_URL'),
        );
        return Mail::Send($this->context->language->id,
            'seller_account_request',
            Mail::l('Seller account request'),
            $mail_params,
            $seller->email,
            null,
            strval(Configuration::get('PS_SHOP_EMAIL')),
            strval(Configuration::get('PS_SHOP_NAME')),
            null,
            null,
            _NPS_MAILS_DIR_);
    }

    private function mailToAdmin($seller) {
        $lang_id = intval(Configuration::get('PS_LANG_DEFAULT'));
        $name = strval(Configuration::get('PS_SHOP_NAME'));
        $email = strval(Configuration::get('PS_SHOP_EMAIL'));
        $mail_params = array(
            '{name}' => $seller->name[$lang_id],
            '{company_name}' => $seller->company_name[$lang_id],
            '{company_description}' => $seller->company_description[$lang_id],
            '{email}' => $seller->email,
            '{phone}' => $seller->phone,
            '{nip}' => $seller->nip,
            '{regon}' => $seller->regon,
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
            '{admin_link}' => $this->context->link->getAdminLink('AdminSellers'),
        );
        return Mail::Send($lang_id,
            'memberalert',
            Mail::l('New seller registration!'),
            $mail_params,
            explode(self::__MA_MAIL_DELIMITOR__, $this->_merchant_mails),
            null,
            strval(Configuration::get('PS_SHOP_EMAIL')),
            strval(Configuration::get('PS_SHOP_NAME')),
            null,
            null,
            _NPS_MAILS_DIR_);
    }

    public function initContent() {
        $this -> page_name = 'accountrequest';
        $this -> display_column_right = false;
        if (!is_null(Configuration::get('NPS_MERCHANT_MAILS')) && Configuration::get('NPS_MERCHANT_MAILS')!='') 
            $this->_merchant_mails = Configuration::get('NPS_MERCHANT_MAILS');
        else
            $this->_merchant_mails = Configuration::get('PS_SHOP_EMAIL');

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