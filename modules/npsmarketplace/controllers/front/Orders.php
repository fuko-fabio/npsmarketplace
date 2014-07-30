<?php
class NpsMarketplaceOrdersModuleFrontController extends ModuleFrontController
{
    
    public function setMedia() {
        parent::setMedia();
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/bootstrap.min.js');
        $this -> addCSS (_PS_MODULE_DIR_.'npsmarketplace/css/bootstrap.css');
    }
    
    public function initContent() {
        parent::initContent();

        $seller = new Seller(null, $this -> context -> customer -> id);
        $orders = $this -> getOrders($seller);

        $this -> context -> smarty -> assign(array(
            'view_order_link' => $this->context->link->getModuleLink('npsmarketplace', 'Order'),
            'orders' => $orders));

        $this -> setTemplate('orders.tpl');
    }

    private function getOrders($seller = null) {
        $result = array();
        $products = $seller -> getProducts();
        foreach ($products as $product) {
            $id_order = Db::getInstance()->getValue('SELECT `id_order` FROM `'._DB_PREFIX_.'order_detail` WHERE `product_id` = '.(int)$product->id);
            if(isset($id_order) && !empty($id_order)) {
                $order = new Order($id_order);
                $result[] = array(
                    'reference' => $order->reference,
                    'customer' => $order->getCustomer()->firstname.' '.$order->getCustomer()->lastname,
                    //TODO Total price must be without items from other users
                    'total_paid_tax_incl' => $order->total_paid_tax_incl,
                    'payment' => $order->payment,
                    'state' => $order-> getCurrentOrderState()->name[$this->context->language->id],
                    'date_add' => $order->date_add,
                    'link' => $this->context->link->getModuleLink('npsmarketplace', 'OrderView', array('id_order' => $id_order, 'id_seller' => $seller->id))
                );
            }
        }
        return $result;
    }
}
?>