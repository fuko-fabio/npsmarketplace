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

        $seller = new SellerCore(null, $this -> context -> customer -> id);
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
            if(isset($id_order)) {
                $order = new Order($id_order);
                $result[] = array(
                    'id_order' => $order->id,
                    'reference' => $order->reference,
                    'customer' => $order->getCustomer()->lastname,
                    'total_paid_tax_incl' => $order->total_paid_tax_incl,
                    'payment' => $order->payment,
                    'state' => $order-> getCurrentState(),
                    'date_add' => $order->date_add,
                );
            }
        }
        return $result;
    }
}
?>