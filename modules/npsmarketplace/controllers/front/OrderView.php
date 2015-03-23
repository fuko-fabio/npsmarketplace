<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsMarketplaceOrderViewModuleFrontController extends ModuleFrontController {

    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;

    public function setMedia() {
        parent::setMedia();
        $this->addJS ("https://maps.googleapis.com/maps/api/js");
        $this->addJS (_PS_MODULE_DIR_.'npsmarketplace/js/order_map.js');
        
    }

    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();

        $seller = new Seller(null, $this->context->customer->id);
        if ($seller->id == null) 
            Tools::redirect('index.php?controller=my-account');

        $order = new Order(Tools::getValue('id_order'));
        $currency = new Currency($order->id_currency);
        $products = $this->productsForView($seller, $order);
        $this -> context -> smarty -> assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'currency' => $currency,
            'order' => $this->orderForView($order, $products),
            'address' => $this->deliveryAddressForView($order->id_address_delivery),
            'customer' => $this->customerForView($order->id_customer),
            'products' => $products,
            'HOOK_ORDERDETAILDISPLAYED' => Hook::exec('displaySellerOrderDetail', array('seller' => $seller, 'order' => $order))
        ));

        $this->setTemplate('order_view.tpl');
    }

    private function getTotalPriceForSeller($products) {
        $result = 0;
        foreach($products as $product) {
            $result = $result + $product['total_price_tax_incl'];
        }
        return $result;
    }

    private function orderForView($order, $products) {
        return array(
            'customer' => $order->getCustomer()->firstname.' '.$order->getCustomer()->lastname,
            'price' => $this->getTotalPriceForSeller($products),
            'payment' => $order->payment,
            'state' => $order-> getCurrentOrderState()->name[$this->context->language->id],
            'date_add' => $order->date_add,
        );
    }

    private function deliveryAddressForView($id_address_delivery) {
        $address = new Address($id_address_delivery);
        return array(
            'firstname' => $address->firstname,
            'lastname' => $address->lastname,
            'address1' => $address->address1,
            'address2' => $address->address2,
            'postcode' => $address->postcode,
            'city' => $address->city,
            'phone' => $address->phone,
            'phone_mobile' => $address->phone_mobile,
            'message' => $address->other,
        );
    }

    private function customerForView($id_customer) {
        $customer = new Customer($id_customer);
        return array(
            'firstname' => $customer->firstname,
            'lastname' => $customer->lastname,
            'email' => $customer->email,
        );
    }

    private function productsForView($seller, $order) {
        $result = array();
        $pd = $order->getProductsDetail();

        foreach (Seller::getSellerProducts($seller->id) as $product_id) {
            foreach ($pd as $product_detail) {
                if($product_detail['id_product'] == $product_id) {
                    $cover = Product::getCover($product_id);
                    $product_detail['current_stock'] = StockAvailable::getQuantityAvailableByProduct($product_id);
                    $product_detail['cover'] = $this->context->link->getImageLink(Tools::link_rewrite($product_detail['product_name']), $cover['id_image'], 'cart_default');
                    $product_detail['has_image'] = !empty($cover);
                    $result[] = $product_detail;
                }
            }
        }
        return $result;
    }
}
?>