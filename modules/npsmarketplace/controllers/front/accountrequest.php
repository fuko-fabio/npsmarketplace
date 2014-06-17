<?php
class NpsMarketplaceAccountRequestModuleFrontController extends ModuleFrontController {

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(_PS_JS_DIR_.'validate.js');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('company_name')
            && Tools::isSubmit('seller_name')
            && Tools::isSubmit('seller_phone')
            && Tools::isSubmit('seller_email'))
        {
            $companyName = trim(Tools::getValue('company_name'));
            $sellerName = trim(Tools::getValue('seller_name'));
            $sellerPhone =  str_replace(' ', '', trim(Tools::getValue('seller_phone')));
            $sellerEmail = trim(Tools::getValue('seller_email'));
            $companyDescription = trim(Tools::getValue('company_description'));
            $companyLogo = trim(Tools::getValue('company_logo'));

            if ($companyDescription != '' && !Validate::isMessage($companyDescription))
                $this->errors[] = Tools::displayError('Your company description contains invalid characters.');
            else if (!Validate::isName($companyName))
                $this->errors[] = Tools::displayError('Invalid company name');
            else if (!Validate::isName($sellerName))
                $this->errors[] = Tools::displayError('Invalid seller name');
            else if (!Validate::isPhoneNumber($sellerPhone))
                $this->errors[] = Tools::displayError('Invalid phone number');
            {
                $customer = $this->context->customer;

                $sql = 'INSERT INTO '._DB_PREFIX_.'seller(
                            id_customer,
                            state,
                            request_date,
                            company_name,
                            company_description,
                            company_logo,
                            name,
                            phone,
                            email)
                        VALUES (
                            '.$customer->id.',
                            1,
                            NOW(),
                            "'.$companyName.'",
                            "'.$companyDescription.'",
                            "'.$companyLogo.'",
                            "'.$sellerName.'",
                            '.$sellerPhone.',
                            "'.$sellerEmail.'")';

                if (!Db::getInstance()->execute($sql))
                    d("Nie udalo sie aktualizować bazy");
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
            $state = 0;
            $date = null;
            if ($result = Db::getInstance() -> executeS($query))
            {
                $state = $result[0]['state'];
                $date = $result[0]['request_date'];
            }
        }
        $this -> context -> smarty -> assign(
            array(
                'account_state' => $state,
                'account_request_date' => $date
            )
        );

        $this -> setTemplate('accountrequest.tpl');
    }

}
?>