<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if (!defined('_PS_VERSION_'))
    exit;

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class NpsVouchers extends Module {
    const INSTALL_SQL_FILE = 'install.sql';

    public function __construct() {
        $this->name = 'npsvouchers';
        $this->tab = 'administration';
        $this->version = 1.0;
        $this->author = 'Norbert Pabian';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        parent::__construct();
        $this->displayName = $this->l( 'nps Vouchers generation for sellers' );
        $this->description = $this->l('This module allows your sellers to generate vouchers for their products');
    }

    public function install() {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", trim($sql));
        $shop_url = Tools::getHttpHost(true).__PS_BASE_URI__;
        
        return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('displayCustomerAccount')
            && $this->createTables($sql);
    }

    public function uninstall() {
        if (!parent::uninstall()
            || !$this->unregisterHook('header')
            || !$this->unregisterHook('displayCustomerAccount')
            || !$this->deleteTables())
            return false;
        return true;
    }

    private function createTables($sql) {
        foreach ($sql as $query)
            if (!Db::getInstance()->execute(trim($query)))
                return false;

        return true;
    }

    private function deleteTables() {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'seller_cart_rule`');
    }

    public function hookHeader() {
        $this->context->controller->addCss(($this->_path).'css/npsvouchers.css');
    }

    public function hookDisplayCustomerAccount() {
        $seller = new Seller(null, $this->context->customer->id);
       if ($seller->requested == 1 && $seller->active == 1 && $seller->locked == 0) {
            $this->context->smarty->assign(array(
                'vouchers_link' => $this->context->link->getModuleLink('npsvouchers', 'List'),
            )
        );
        return $this->display(__FILE__, 'customer_account.tpl');
       }
    }
}
