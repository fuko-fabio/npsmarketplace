<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if (!defined('_PS_VERSION_'))
    exit;

include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');
include_once(_PS_MODULE_DIR_.'npsvouchers/classes/OrderSellerCartRule.php');

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
        $this->description = $this->l('This module allows your sellers to generate vouchers for products');
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
            && $this->registerHook('displaySellerOrderDetail')
            && $this->registerHook('displaySellerOrderCartRules')
            && $this->registerHook('actionValidateOrder')
            && $this->createTables($sql);
    }

    public function uninstall() {
        if (parent::uninstall()
            && $this->unregisterHook('header')
            && $this->unregisterHook('displayCustomerAccount')
            && $this->unregisterHook('displaySellerOrderDetail')
            && $this->unregisterHook('displaySellerOrderCartRules')
            && $this->unregisterHook('actionValidateOrder')
            && $this->deleteTables())
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
            `'._DB_PREFIX_.'order_seller_cart_rule`,
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

    public function hookDisplaySellerOrderDetail($params) {
        $seller = $params['seller'];
        $order = $params['order'];
        if ($seller->id && $order->id) {
            $tickets = CartTicket::getAllTicketsByCartId(Cart::getCartIdByOrderId($order->id), $seller->id);
            $this->smarty->assign(array(
                'cart_rules' => $this->getCartRules($seller->id, $order->id)
            ));

            return $this->display(__FILE__, 'order_details.tpl');
        }
    }

    public function hookDisplaySellerOrderCartRules($params) {
        $seller = $params['seller'];
        $order = $params['order'];
        $result = array();
        if ($seller->id && $order->id) {
            $result = $this->getCartRules($seller->id, $order->id);
        }
        return $result;
    }

    public function hookActionValidateOrder($params) {
        $order = $params['order'];
        $ids = array();
        foreach ($order->getCartRules() as $key => $value) {
            $ids[] = $value['id_cart_rule'];
        }
        if (!empty($ids)){
            $dbquery = new DbQuery();
            $dbquery->select('ocr.id_order_cart_rule, cr.reduction_product, scr.id_seller')
                ->from('order_cart_rule', 'ocr')
                ->leftJoin('seller_cart_rule', 'scr', 'ocr.id_cart_rule = scr.id_cart_rule')
                ->leftJoin('cart_rule', 'cr', 'ocr.id_cart_rule = cr.id_cart_rule')
                ->where('ocr.`id_cart_rule` IN('.implode(',', $ids).') AND ocr.`id_order` = '.$order->id);
            $result = Db::getInstance()->executeS($dbquery);
            foreach ($result as $key => $value) {
                $obj = new OrderSellerCartRule();
                $obj->id_seller = $value['id_seller'];
                $obj->id_product = $value['reduction_product'];
                $obj->id_order_cart_rule = $value['id_order_cart_rule'];
                $obj->add();
            }
        }
    }

    private function getCartRules($id_seller, $id_order) {
        $dbquery = new DbQuery();
        $dbquery->select('*')
            ->from('order_cart_rule', 'ocr')
            ->leftJoin('seller_cart_rule', 'scr', 'ocr.id_cart_rule = scr.id_cart_rule')
            ->where('scr.`id_seller` = '.$id_seller.' AND ocr.id_order = '.$id_order);
        return Db::getInstance()->executeS($dbquery);
    }
}
