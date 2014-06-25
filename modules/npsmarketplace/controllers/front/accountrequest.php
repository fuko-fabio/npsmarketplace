<?php
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