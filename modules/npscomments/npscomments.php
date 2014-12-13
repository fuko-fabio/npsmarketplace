<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if ( !defined( '_PS_VERSION_' ) )
    exit;

if ( !defined( '_NPS_SEL_IMG_DIR_' ) )
    define('_NPS_SEL_IMG_DIR_', _PS_IMG_DIR_.'seller/');

if ( !defined( '_THEME_SEL_DIR_' ) )
    define('_THEME_SEL_DIR_', _PS_IMG_.'seller/');

if ( !defined( '_NPS_MAILS_DIR_' ) )
    define('_NPS_MAILS_DIR_', dirname(__FILE__).'/mails/');

if ( !defined( '_NPS_MARKETPLACE_DIR_' ) )
    define('_NPS_MARKETPLACE_DIR_', _PS_MODULE_DIR_.'/npsmarketplace/');

require_once(_NPS_MARKETPLACE_DIR_.'/classes/Seller.php');

class npscomments extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';

    public function __construct()
    {
        $this->name = 'npscomments';
        $this->tab = 'market_place';
        $this->version = 1.0;
        $this->author = 'Norbert Pabian';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        parent::__construct();
        $this->displayName = $this->l( 'nps Marketplace Seller Comments' );
        $this->description = $this->l( 'Allow customers to add seller coments.' );
        $this->confirmUninstall = $this->l('Are you sure you want to delete module?');
    }

    public function install()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", trim($sql));

        if (!parent::install()
            || !$this->registerHook('productTab')
            || !$this->registerHook('productTabContent')
            || !$this->registerHook('sellerTab')
            || !$this->registerHook('sellerTabContent')
            || !Configuration::updateValue('NPS_SELLER_COMMENTS_MODERATE', 1)
            || !Configuration::updateValue('NPS_SELLER_COMMENTS_ALLOW_GUESTS', 0)
            || !Configuration::updateValue('NPS_SELLER_COMMENTS_MINIMAL_TIME', 30)
            || !$this->_createTables($sql)
            || !$this->_createTab())
            return false;
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->unregisterHook('productTab')
            || !$this->unregisterHook('productTabContent')
            || !$this->unregisterHook('sellerTab')
            || !$this->unregisterHook('sellerTabContent')
            || !Configuration::deleteByName('NPS_SELLER_COMMENTS_MODERATE')
            || !Configuration::deleteByName('NPS_SELLER_COMMENTS_ALLOW_GUESTS')
            || !Configuration::deleteByName('NPS_SELLER_COMMENTS_MINIMAL_TIME')
            || !$this->_deleteTab()
            || !$this->_deleteTables())
            return false;
        return true;
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submitModerate')) {
            Configuration::updateValue('NPS_SELLER_COMMENTS_MODERATE', (int)Tools::getValue('NPS_SELLER_COMMENTS_MODERATE'));
            Configuration::updateValue('NPS_SELLER_COMMENTS_ALLOW_GUESTS', (int)Tools::getValue('NPS_SELLER_COMMENTS_ALLOW_GUESTS'));
            Configuration::updateValue('NPS_SELLER_COMMENTS_MINIMAL_TIME', (int)Tools::getValue('NPS_SELLER_COMMENTS_MINIMAL_TIME'));
            $output .= $this->displayConfirmation($this->l('Seller comments settings updated'));
        }
        return $output.$this->displayForm();
    }

    public function hookProductTab($params)
    {
        $id_seller = (int)Seller::getSellerByProduct(Tools::getValue('id_product'));
        if(isset($id_seller) && $id_seller > 0) {
            require_once(_PS_MODULE_DIR_.'/npscomments/classes/SellerComment.php');
            require_once(_PS_MODULE_DIR_.'/npscomments/classes/SellerCommentCriterion.php');

            $this->context->controller->addJS(array(
                _PS_MODULE_DIR_.'npscomments/js/jquery.rating.pack.js',
                _PS_MODULE_DIR_.'npscomments/js/jquery.textareaCounter.plugin.js',
                _PS_MODULE_DIR_.'npscomments/js/npscomments.js'));
            $this->context->controller->addCSS(_PS_MODULE_DIR_.'/npscomments/npscomments.css', 'all');

            $average = SellerComment::getAverageGrade((int)$id_seller);

            $this->context->smarty->assign(array(
                                            'allow_guests' => (int)Configuration::get('NPS_SELLER_COMMENTS_ALLOW_GUESTS'),
                                            'comments' => SellerComment::getBySeller($id_seller),
                                            'criterions' => SellerCommentCriterion::getBySeller($id_seller, $this->context->language->id),
                                            'averageTotal' => round($average['grade']),
                                            'nbComments' => (int)(SellerComment::getCommentNumber($id_seller))
                                       ));
            return $this->display(__FILE__, 'views/templates/hook/tab.tpl');
        }
    }

    public function hookProductTabContent($params) {
        $id_seller = (int)Seller::getSellerByProduct(Tools::getValue('id_product'));
        if(isset($id_seller) && $id_seller > 0) {
            require_once(_PS_MODULE_DIR_.'/npscomments/classes/SellerComment.php');
            require_once(_PS_MODULE_DIR_.'/npscomments/classes/SellerCommentCriterion.php');

            $seller = new Seller($id_seller);
            $id_guest = (!$id_customer = (int)$this->context->cookie->id_customer) ? (int)$this->context->cookie->id_guest : false;
            $customerComment = SellerComment::getByCustomer($seller->id, (int)$this->context->cookie->id_customer, true, (int)$id_guest);
            $averages = SellerComment::getAveragesBySeller($seller->id, $this->context->language->id);
            $averageTotal = 0;
            foreach ($averages as $average)
                $averageTotal += (float)($average);
            $averageTotal = count($averages) ? ($averageTotal / count($averages)) : 0;

            $this->context->smarty->assign(array(
                 'npscomments_logged' => $this->context->customer->isLogged(true),
                 'npscomments_action_url' => '',
                 'seller' => $seller,
                 'npscomments' => SellerComment::getBySeller($seller->id, 1, null, $this->context->cookie->id_customer),
                 'npscomments_criterions' => SellerCommentCriterion::getBySeller($seller->id, $this->context->language->id),
                 'npscomments_averages' => $averages,
                 'npscomments_path' => $this->_path,
                 'npscomments_averageTotal' => $averageTotal,
                 'npscomments_allow_guests' => (int)Configuration::get('NPS_SELLER_COMMENTS_ALLOW_GUESTS'),
                 'npscomments_too_early' => ($customerComment && (strtotime($customerComment['date_add']) + Configuration::get('NPS_SELLER_COMMENTS_MINIMAL_TIME')) > time()),
                 'npscomments_delay' => Configuration::get('NPS_SELLER_COMMENTS_MINIMAL_TIME'),
                 'npscomments_secure_key' => $this->secure_key,
                 'npscomments_cover' => '',
                 'npscomments_cover_image' => Seller::getImageLink($seller->id, null, $this->context),
                 'npscomments_mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
                 'npscomments_nbComments' => (int)SellerComment::getCommentNumber($seller->id),
                 'npscomments_controller_url' => $this->context->link->getModuleLink('npscomments', 'SellerComments', array('id_seller' => $seller->id)),
                 'npscomments_moderation_active' => (int)Configuration::get('NPS_SELLER_COMMENTS_MODERATE'),
                 'current_id_lang' => (int)$this->context->language->id,
            ));
    
            $this->context->controller->pagination((int)SellerComment::getCommentNumber($seller->id));

            return $this->display(__FILE__, 'views/templates/hook/npscomments.tpl');
        }
    }

    public function hookSellerTab() {
        $id_seller = Tools::getValue('id_seller');
        if(isset($id_seller) && $id_seller > 0) {
            require_once(_PS_MODULE_DIR_.'/npscomments/classes/SellerComment.php');
            require_once(_PS_MODULE_DIR_.'/npscomments/classes/SellerCommentCriterion.php');
            $average = SellerComment::getAverageGrade((int)$id_seller);

            $this->context->smarty->assign(array(
                                            'allow_guests' => (int)Configuration::get('NPS_SELLER_COMMENTS_ALLOW_GUESTS'),
                                            'comments' => SellerComment::getBySeller($id_seller),
                                            'criterions' => SellerCommentCriterion::getBySeller($id_seller, $this->context->language->id),
                                            'averageTotal' => round($average['grade']),
                                            'nbComments' => (int)(SellerComment::getCommentNumber($id_seller))
                                       ));

            return $this->display(__FILE__, 'views/templates/hook/tab.tpl');
        }
    }

    public function hookSellerTabContent($params) {
        if(isset($params['seller'])) {
            require_once(_PS_MODULE_DIR_.'/npscomments/classes/SellerComment.php');
            require_once(_PS_MODULE_DIR_.'/npscomments/classes/SellerCommentCriterion.php');

            $this->context->controller->addJS($this->_path.'js/jquery.rating.pack.js');
            $this->context->controller->addJS($this->_path.'js/jquery.textareaCounter.plugin.js');
            $this->context->controller->addJS($this->_path.'js/npscomments.js');
            $this->context->controller->addCSS($this->_path.'npscomments.css', 'all');

            $seller = $params['seller'];
            $id_guest = (!$id_customer = (int)$this->context->cookie->id_customer) ? (int)$this->context->cookie->id_guest : false;
            $customerComment = SellerComment::getByCustomer($seller->id, (int)$this->context->cookie->id_customer, true, (int)$id_guest);
            $averages = SellerComment::getAveragesBySeller($seller->id, $this->context->language->id);
            $averageTotal = 0;
            foreach ($averages as $average)
                $averageTotal += (float)($average);
            $averageTotal = count($averages) ? ($averageTotal / count($averages)) : 0;

            $this->context->smarty->assign(array(
                 'npscomments_logged' => $this->context->customer->isLogged(true),
                 'npscomments_action_url' => '',
                 'seller' => $seller,
                 'npscomments' => SellerComment::getBySeller($seller->id, 1, null, $this->context->cookie->id_customer),
                 'npscomments_criterions' => SellerCommentCriterion::getBySeller($seller->id, $this->context->language->id),
                 'npscomments_averages' => $averages,
                 'npscomments_path' => $this->_path,
                 'npscomments_averageTotal' => $averageTotal,
                 'npscomments_allow_guests' => (int)Configuration::get('NPS_SELLER_COMMENTS_ALLOW_GUESTS'),
                 'npscomments_too_early' => ($customerComment && (strtotime($customerComment['date_add']) + Configuration::get('NPS_SELLER_COMMENTS_MINIMAL_TIME')) > time()),
                 'npscomments_delay' => Configuration::get('NPS_SELLER_COMMENTS_MINIMAL_TIME'),
                 'npscomments_secure_key' => $this->secure_key,
                 'npscomments_cover' => '',
                 'npscomments_cover_image' => Seller::getImageLink($seller->id, null, $this->context),
                 'npscomments_mediumSize' => Image::getSize(ImageType::getFormatedName('medium')),
                 'npscomments_nbComments' => (int)SellerComment::getCommentNumber($seller->id),
                 'npscomments_controller_url' => $this->context->link->getModuleLink('npscomments', 'SellerComments', array('id_seller' => $seller->id)),
                 'npscomments_moderation_active' => (int)Configuration::get('NPS_SELLER_COMMENTS_MODERATE'),
                 'current_id_lang' => (int)$this->context->language->id,
            ));

            $this->context->controller->pagination((int)SellerComment::getCommentNumber($seller->id));

            return $this->display(__FILE__, 'views/templates/hook/npscomments.tpl');
        }
    }

    private function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0] = $this->moderateForm();

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
         
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );
         
        // Load current value
        $helper->fields_value = $this->getConfigFieldsValues();
        return $helper->generateForm($fields_form);
    }

    public function moderateForm() {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Seller Comments Configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'is_bool' => true, //retro compat 1.5
                        'label' => $this->l('All reviews must be validated by an employee'),
                        'name' => 'NPS_SELLER_COMMENTS_MODERATE',
                        'values' => array(
                                        array(
                                            'id' => 'active_on',
                                            'value' => 1,
                                            'label' => $this->l('Enabled')
                                        ),
                                        array(
                                            'id' => 'active_off',
                                            'value' => 0,
                                            'label' => $this->l('Disabled')
                                        )
                                    ),
                    ),
                    array(
                        'type' => 'switch',
                        'is_bool' => true, //retro compat 1.5
                        'label' => $this->l('Allow guest reviews'),
                        'name' => 'NPS_SELLER_COMMENTS_ALLOW_GUESTS',
                        'values' => array(
                                        array(
                                            'id' => 'active_on',
                                            'value' => 1,
                                            'label' => $this->l('Enabled')
                                        ),
                                        array(
                                            'id' => 'active_off',
                                            'value' => 0,
                                            'label' => $this->l('Disabled')
                                        )
                                    ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Minimum time between 2 reviews from the same user'),
                        'name' => 'NPS_SELLER_COMMENTS_MINIMAL_TIME',
                        'class' => 'fixed-width-xs',
                        'suffix' => 'seconds',
                    ),
                ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submitModerate',
                )
            )
        );
    }

    public function getConfigFieldsValues()
    {
        return array(
            'NPS_SELLER_COMMENTS_MODERATE' => Tools::getValue('NPS_SELLER_COMMENTS_MODERATE', Configuration::get('NPS_SELLER_COMMENTS_MODERATE')),
            'NPS_SELLER_COMMENTS_ALLOW_GUESTS' => Tools::getValue('NPS_SELLER_COMMENTS_ALLOW_GUESTS', Configuration::get('NPS_SELLER_COMMENTS_ALLOW_GUESTS')),
            'NPS_SELLER_COMMENTS_MINIMAL_TIME' => Tools::getValue('NPS_SELLER_COMMENTS_MINIMAL_TIME', Configuration::get('NPS_SELLER_COMMENTS_MINIMAL_TIME')),
        );
    }

    private function _createTab()
    {
        $tabs = Tab::getCollectionFromModule('npsmarketplace');

        $sellers_tab = new Tab();
        $sellers_tab->id_parent = $tabs[0]->id;
        $sellers_tab->position = 1;
        $sellers_tab->module = $this->name;
        $sellers_tab->class_name = 'AdminSellersComments';
        $languages = Language::getLanguages();
        foreach ($languages AS $language)
        {
            $sellers_tab->{'name'}[intval($language['id_lang'])] = 'Comments';
        }
        return $sellers_tab->add();
    }

    private function _deleteTab()
    {
        $tabs = Tab::getCollectionFromModule($this->name);
        if (!empty($tabs))
        {
            foreach ($tabs as $tab)
            {
                $tab->delete();
            }
            return true;
        }
        return false;
    }

    /* Set database */
    private function _createTables($sql)
    {
        foreach ($sql as $query)
            if (!Db::getInstance()->execute(trim($query)))
                return false;

        return true;
    }

    private function _deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'seller_comment`,
            `'._DB_PREFIX_.'seller_comment_criterion`,
            `'._DB_PREFIX_.'seller_comment_criterion_seller`,
            `'._DB_PREFIX_.'seller_comment_criterion_lang`,
            `'._DB_PREFIX_.'seller_comment_grade`,
            `'._DB_PREFIX_.'seller_comment_usefulness`,
            `'._DB_PREFIX_.'seller_comment_report`');
    }
}
?>
