<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsMarketplaceOrdersModuleFrontController extends ModuleFrontController {

    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;

    public function setMedia() {
        parent::setMedia();
        $this->addJqueryPlugin('footable');
        $this->addJqueryPlugin('footable-sort');
        $this->addJqueryPlugin('scrollTo');
    }

    public function init() {
        $this->page_name = 'orders';
        parent::init();
    }

    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();

        $seller = new Seller(null, $this->context->customer->id);
        if ($seller->id == null) 
            Tools::redirect('index.php?controller=my-account');

        $orders = $this->getOrders($seller);
        $this->context->smarty->assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'view_order_link' => $this->context->link->getModuleLink('npsmarketplace', 'Order'),
            'orders' => $orders
        ));

        $this -> setTemplate('orders.tpl');
    }

    private function getOrders($seller, $showHiddenStatus = false) {
        $result = array();
        $products = $seller -> getProducts();
        $sql = 'SELECT DISTINCT `id_order`
                FROM `'._DB_PREFIX_.'order_detail`
                WHERE `product_id` IN 
                    (SELECT DISTINCT id_product FROM `'._DB_PREFIX_.'seller_product` WHERE `id_seller` = '.$seller->id.')
                ORDER BY id_order DESC';
        $rows = Db::getInstance()->executeS($sql);

        if($rows && !empty($rows)) {
            foreach ($rows as $row) {
                $id_order = $row['id_order'];
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT o.*, (SELECT SUM(od.`product_quantity`) FROM `'._DB_PREFIX_.'order_detail` od WHERE od.`id_order` = o.`id_order`) nb_products
                FROM `'._DB_PREFIX_.'orders` o
                WHERE o.`id_order` = '.(int)$id_order.'
                GROUP BY o.`id_order`
                ORDER BY o.`date_add` DESC');
                if (!$res)
                    return array();

                $res2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT os.`id_order_state`, osl.`name` AS order_state, os.`invoice`, os.`color` as order_state_color
                    FROM `'._DB_PREFIX_.'order_history` oh
                    LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
                    INNER JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$this->context->language->id.')
                    WHERE oh.`id_order` = '.(int)($res[0]['id_order']).(!$showHiddenStatus ? ' AND os.`hidden` != 1' : '').'
                    ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC
                    LIMIT 1');

                if ($res2)
                    $res[0] = array_merge($res[0], $res2[0]);
                $customer = new Customer($res[0]['id_customer']);

                $seller_total = 0;
                $order = new Order($id_order);
                $pd = $order->getProductsDetail();
                foreach ($products as $product) {
                    foreach ($pd as $product_detail) {
                        if($product_detail['id_product'] == $product->id) {
                            $seller_total = $seller_total + $product_detail['total_price_tax_incl'];
                        }
                    }
                }
                $result[] = array_merge($res[0], array(
                    'customer' => $customer->firstname.' '.$customer->lastname,
                    'link' => $this->context->link->getModuleLink('npsmarketplace', 'OrderView', array('id_order' => $id_order, 'id_seller' => $seller->id)),
                    'order_id_currency' => $order->id_currency,
                    'total_seller_tax_incl' => $seller_total
                ));
            }

        }
        return $result;
    }
}
?>