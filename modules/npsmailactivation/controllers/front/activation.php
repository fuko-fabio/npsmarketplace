<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsMailactivAtionActivationModuleFrontController extends ModuleFrontController {

    public function initContent() {
        parent::initContent();
        $customer = $this->module->execActivation();
        if ($customer) {
            $this->autoLogin($customer);
            $this->context->cookie->account_created = 1;
            Tools::redirect($this->context->link->getPageLink('my-account', true));
        } else {
            $this->setTemplate('fail.tpl' );
        }
    }

    private function autoLogin(Customer $customer) {
        if ($customer != null && $customer->id) {
            $this->context->cookie->id_compare = isset($this->context->cookie->id_compare)
                ? $this->context->cookie->id_compare : CompareProduct::getIdCompareByIdCustomer($customer->id);
            $this->context->cookie->id_customer = (int)($customer->id);
            $this->context->cookie->customer_lastname = $customer->lastname;
            $this->context->cookie->customer_firstname = $customer->firstname;
            $this->context->cookie->logged = 1;
            $customer->logged = 1;
            $this->context->cookie->is_guest = $customer->isGuest();
            $this->context->cookie->passwd = $customer->passwd;
            $this->context->cookie->email = $customer->email;

            // Add customer to the context
            $this->context->customer = $customer;

            if (Configuration::get('PS_CART_FOLLOWING')
                    && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0)
                    && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id)) {
                $this->context->cart = new Cart($id_cart);
            } else {
                $id_carrier = (int)$this->context->cart->id_carrier;
                $this->context->cart->id_carrier = 0;
                $this->context->cart->setDeliveryOption(null);
                $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
                $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)($customer->id));
            }
            $this->context->cart->id_customer = (int)$customer->id;
            $this->context->cart->secure_key = $customer->secure_key;

            if ($this->ajax && isset($id_carrier) && $id_carrier && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                $delivery_option = array($this->context->cart->id_address_delivery => $id_carrier.',');
                $this->context->cart->setDeliveryOption($delivery_option);
            }

            $this->context->cart->save();
            $this->context->cookie->id_cart = (int)$this->context->cart->id;
            $this->context->cookie->write();
            $this->context->cart->autosetProductAddress();

            Hook::exec('actionAuthentication');

            // Login information have changed, so we check if the cart rules still apply
            CartRule::autoRemoveFromCart($this->context);
            CartRule::autoAddToCart($this->context);
        }
    }
}