<?php

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class NpsMarketplaceAccountRequestModuleFrontController extends ModuleFrontController {

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
        if (Tools::isSubmit('company_name')
            && Tools::isSubmit('seller_name')
            && Tools::isSubmit('seller_phone')
            && Tools::isSubmit('seller_email')
            && Tools::isSubmit('seller_nip')
            && Tools::isSubmit('seller_regon'))
        {
            $company_name = trim(Tools::getValue('company_name'));
            $name = trim(Tools::getValue('seller_name'));
            $phone =  trim(Tools::getValue('seller_phone'));
            $email = trim(Tools::getValue('seller_email'));
            $nip = trim(Tools::getValue('seller_nip'));
            $regon = trim(Tools::getValue('seller_regon'));
            $company_description = trim(Tools::getValue('company_description'));
            $companyLogo = trim(Tools::getValue('company_logo'));

            if ($company_description != '' && !Validate::isMessage($company_description))
                $this->errors[] = Tools::displayError('Your company description contains invalid characters.');
            else if (!Validate::isName($company_name))
                $this->errors[] = Tools::displayError('Invalid company name');
            else if (!Validate::isName($name))
                $this->errors[] = Tools::displayError('Invalid seller name');
            else if (!Validate::isPhoneNumber($phone))
                $this->errors[] = Tools::displayError('Invalid phone number');
            else if (!Validate::isEmail($email))
                $this->errors[] = Tools::displayError('Invalid email addres');
            else if (!Validate::isNip($nip))
                $this->errors[] = Tools::displayError('Invalid NIP number');
            else if (!Validate::isRegon($regon))
                $this->errors[] = Tools::displayError('Invalid REGON number');

            $customer = $this->context->customer;

            $seller = new SellerCore();
            $seller->id_customer = $customer->id;
            $seller->company_name = array((int)(Configuration::get('PS_LANG_DEFAULT')) => $company_name);
            $seller->company_description = array((int)(Configuration::get('PS_LANG_DEFAULT')) => $company_description);
            $seller->name = array((int)(Configuration::get('PS_LANG_DEFAULT')) => $name);
            $seller->phone = $phone;
            $seller->email = $email;
            $seller->nip = $nip;
            $seller->regon = $regon;
            $seller->commision = Configuration::get('GLOBAL_COMMISION');

            if (Tools::getValue('add_product') == 'on')
            {
                $product_name = trim(Tools::getValue('product_name'));
                $product_short_description = trim(Tools::getValue('product_short_description'));
                $product_description = trim(Tools::getValue('product_description'));
                $product_price = trim(Tools::getValue('product_price'));
                $product_amount = trim(Tools::getValue('product_amount'));
                $product_date = trim(Tools::getValue('product_date'));
                $product_time = trim(Tools::getValue('product_time'));
                # TODO Read categories $product_category = trim(Tools::getValue('product_category'));
                
                if (!Validate::isGenericName($product_name))
                    $this->errors[] = Tools::displayError('Invalid product name');
                else if (!Validate::isMessage($product_short_description))
                    $this->errors[] = Tools::displayError('Invalid product short description');
                else if (!Validate::isMessage($product_description))
                    $this->errors[] = Tools::displayError('Invalid product description');
                else if (!Validate::isPhoneNumber($product_price))
                    $this->errors[] = Tools::displayError('Invalid product price');
                else if (!Validate::isInt($product_amount))
                    $this->errors[] = Tools::displayError('Invalid product amount number');
                #else if (!Validate::isNip($product_date))
                #    $this->errors[] = Tools::displayError('Invalid product date');
                #else if (!Validate::isRegon($product_time))
                #    $this->errors[] = Tools::displayError('Invalid product time');

                $product = new Product();
                $product->wholesale_price = $product_price;
                $product->name = array((int)(Configuration::get('PS_LANG_DEFAULT')) => $product_name);
                $product->quantity = $product_amount;
                $product->active = 0;
                $product->description = array((int)(Configuration::get('PS_LANG_DEFAULT')) => $product_description);
                $product->description_short = array((int)(Configuration::get('PS_LANG_DEFAULT')) => $product_short_description);
                #$product->available_date = $product_date;
                $product->link_rewrite =  array((int)(Configuration::get('PS_LANG_DEFAULT')) => Tools::link_rewrite($product_name));
                $product->save();
            }
            if(empty($this->errors))
            {
                $seller->save();
                if (isset($product))
                    $product->save();
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
            $state = 'none';
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
                'account_request_date' => $date,
                'user_agreement_url' => '#', #TODO Set real url's
                'processing_data_url' => '#'
            )
        );

        $this -> context -> smarty -> assign('categories_tree', $this -> getCategories());
        $this -> context -> smarty -> assign('category_partial_tpl_path', _PS_MODULE_DIR_.'npsmarketplace/views/templates/front/category_tree_partial.tpl');


        $this -> setTemplate('accountrequest.tpl');
    }

    private function getCategories()
    {
        $resultIds = array();
        $resultParents = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
        FROM `'._DB_PREFIX_.'category` c
        INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
        INNER JOIN `'._DB_PREFIX_.'category_shop` cs ON (cs.`id_category` = c.`id_category` AND cs.`id_shop` = '.(int)$this->context->shop->id.')
        WHERE (c.`active` = 1 OR c.`id_category` = '.(int)Configuration::get('PS_HOME_CATEGORY').')
        AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
        AND c.id_category IN (
            SELECT id_category
            FROM `'._DB_PREFIX_.'category_group`
            WHERE `id_group` IN ('.pSQL(implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id))).')
        )
        ORDER BY `level_depth` ASC, cs.`position` ASC');
        foreach ($result as &$row)
        {
            $resultParents[$row['id_parent']][] = &$row;
            $resultIds[$row['id_category']] = &$row;
        }
        $blockCategTree = $this->getCategoriesTree($resultParents, $resultIds);

        return $blockCategTree;
    }

    private function getCategoriesTree($resultParents, $resultIds, $maxDepth = 0, $id_category = null, $currentDepth = 0)
    {
        if (is_null($id_category))
            $id_category = $this->context->shop->getCategory();

        $children = array();
        if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth))
            foreach ($resultParents[$id_category] as $subcat)
                $children[] = $this->getCategoriesTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);

        if (!isset($resultIds[$id_category]))
            return false;
        
        $return = array(
            'id' => $id_category,
            'link' => $this->context->link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']),
            'name' =>  $resultIds[$id_category]['name'],
            'desc'=>  $resultIds[$id_category]['description'],
            'children' => $children
        );

        return $return;
    }
}
?>