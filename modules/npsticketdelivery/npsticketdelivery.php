<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

if ( !defined( '_PS_VERSION_' ) )
    exit;

require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/CartTicket.php');
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/Ticket.php');
require_once(_PS_MODULE_DIR_.'npsticketdelivery/classes/TicketsGenerator.php');
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class NpsTicketDelivery extends Module {
        const INSTALL_SQL_FILE = 'install.sql';

    public function __construct() {
        $this->name = 'npsticketdelivery';
        $this->tab = 'shipping_logistics';
        $this->version = 1.0;
        $this->author = 'Norbert Pabian';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
        $this->bootstrap = true;
        $this->secure_key = Tools::encrypt($this->name);
        parent::__construct();
        $this->displayName = $this->l( 'nps Ticket Delivery' );
        $this->description = $this->l( 'Generates and sends tickets via e-mail.' );
    }

    public function install() {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        $sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", trim($sql));
        $shop_url = Tools::getHttpHost(true).__PS_BASE_URI__;

        if (!parent::install()
            || !$this->registerHook('actionOrderStatusPostUpdate')
            || !$this->registerHook('displayBeforeVirtualCarrier')
            || !$this->registerHook('actionPostProcessCarrier')
            || !$this->createTables($sql))
            return false;
        return true;
    }
    public function uninstall() {
        if (!parent::uninstall()
            || !$this->unregisterHook('actionOrderStatusPostUpdate')
            || !$this->unregisterHook('displayBeforeVirtualCarrier')
            || !$this->unregisterHook('actionPostProcessCarrier')
            || !$this->deleteTables())
            return false;
        return true;
    }

    public function hookActionOrderStatusPostUpdate($params) {
        $o_s = $params['newOrderStatus'];
        $id_order_state = Configuration::get('NPS_P24_ORDER_STATE_ACCEPTED');
        if ($id_order_state == $o_s->id) {
            $id_order = $params['id_order'];
            $cart = CartCore::getCartByOrderId($id_order);
            $c_t = CartTicket::getByCartId($cart->id);
            foreach ($cart->getProducts() as $product) {
                $id_seller = Seller::getSellerByProduct($product['id_product']);
                if (!$id_seller) {
                    PrestaShopLogger::addLog('Unable to find owner of product '.$product['name'].'with ID: '.$product['id_product'].' Ticket cannot be generated');
                    continue;
                }
                $qty = $product['cart_quantity'];
                $seller = new Seller($id_seller);
                $attrs = explode(',', $product['attributes_small']);
                $date = date_create($attrs[0].$attrs[1]);
                $date = date_format($date, 'Y-m-d H:i:s');
                $address = $this->getFeatureValue($product['features'], Configuration::get('NPS_FEATURE_ADDRESS_ID'));
                $town = $this->getFeatureValue($product['features'], Configuration::get('NPS_FEATURE_TOWN_ID'));
                $district = $this->getFeatureValue($product['features'], Configuration::get('NPS_FEATURE_DISTRICT_ID'));
                for ($x=1; $x<=$qty; $x++) {
                    $t = new Ticket();
                    $t->id_cart_ticket = $c_t->id;
                    $t->id_seller = $seller->id;
                    $t->date = $date;
                    $t->generated = date("Y-m-d H:i:s");
                    $t->price = $product['price'];
                    $t->name = $product['name'];
                    $t->address = $address;
                    $t->town = $town;
                    $t->district = $district;
                    $t->save();
                }
            }
            TicketsGenerator::generateAndSend($c_t->id);
        }
    }

    private function getFeatureValue($features, $id_feature) {
        foreach ($features as $key => $value) {
            if($value['id_feature'] == $id_feature) {
               $rows = FeatureValue::getFeatureValueLang($value['id_feature_value']);
                foreach ($rows as $row) {
                    if($row['id_lang'] == $this->context->language->id)
                        return $row['value'];
                }
            }
        }
        return null;
    }

    public function hookDisplayBeforeVirtualCarrier() {
        $this->context->controller->addJS(_PS_JS_DIR_.'validate.js');
        return $this->display(__FILE__, 'views/templates/hook/virtual_carrier.tpl');
    }

    public function hookActionPostProcessCarrier($params) {
        $ticket = new CartTicket(null, $params['id_cart']);
        $ticket->id_cart = $params['id_cart'];
        $ticket->id_customer =$this->context->customer->id;
        $ticket->email = $params['ticket_email'];
        $ticket->gift = $params['gift_ticket'];
        $ticket->save();
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
            `'._DB_PREFIX_.'cart_ticket`,
            `'._DB_PREFIX_.'ticket`');
    }
}
