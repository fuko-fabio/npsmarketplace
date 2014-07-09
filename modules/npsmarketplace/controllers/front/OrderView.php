<?php
class NpsMarketplaceOrderViewModuleFrontController extends ModuleFrontController
{
    public function setMedia()
    {
        parent::setMedia();
        $this -> addJS ("https://maps.googleapis.com/maps/api/js");
        $this -> addJS (_PS_MODULE_DIR_.'npsmarketplace/js/googlemap.js');
        
    }

    public function initContent()
    {
        parent::initContent();

        $seller = new SellerCore(Tools::getValue('id_seller'));
        $order = new Order(Tools::getValue('id_order'));

        $this -> context -> smarty -> assign(array(
            'order' => $this->orderForView($order),
            'address' => $this->deliveryAddressForView($order->id_address_delivery),
            'customer' => $this->customerForView($order->id_customer)
            )
        );

        $this->setTemplate('order_view.tpl');
    }

    private function orderForView($order) {
        return array(
            'customer' => $order->getCustomer()->firstname.' '.$order->getCustomer()->lastname,
            'price' => $order->total_paid_tax_incl,
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
}
?>