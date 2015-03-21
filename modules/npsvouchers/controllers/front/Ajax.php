<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsmarketplace/npsmarketplace.php');
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class NpsVouchersAjaxModuleFrontController extends ModuleFrontController {

    public function postProcess()  {
        if (Tools::isSubmit('action')) {
            if ($this->context == null) {
                $this->context = Context::getContext();
            }
            switch(Tools::getValue('action')) {
                case 'sendVouchers':
                    $this->ajaxProcessSendVouchers();
                    break;
            }
        }
    }

    protected function ajaxProcessSendVouchers() {
        $emails = trim(Tools::getValue('emails'));
        $emails_array = array();
        if (!empty($emails)) {
            $emails_array = explode(',', $emails);
        }
        $id_voucher = Tools::getValue('id_voucher');
        $newsletter_users = Tools::getValue('newsletter_users');
        
        if ($newsletter_users) {
            $dbquery = new DbQuery();
            $dbquery->select('email')
                ->from('customer')
                ->where('newsletter = 1');
            $result = Db::getInstance()->executeS($dbquery);
            foreach ($result as $key => $value) {
                if (!empty($value['email'])) {
                    $emails_array[] = $value['email'];
                }
            }
        }
        if (!$id_voucher || empty($emails_array))
            die(Tools::jsonEncode(array('error' => 1)));

        $emails_array = array_unique($emails_array);
        $cart_rule = new CartRule($id_voucher);
        $seller = new Seller(null, $this->context->customer->id);
        $seller_customer = new Customer($seller->id_customer);
        $product = new Product($cart_rule->reduction_product, false, $this->context->language->id);
        $product_link = $this->context->link->getProductLink($product);
        $success = 0;
        $fail = 0;
        foreach ($emails_array as $key => $email) {
            if (!Validate::isEmail($email)) {
                $fail = $fail + 1;
                continue;
            }
            $templateVars = array(
                '{seller_name}' => $seller->name,
                '{seller_link}' => $this->context->link->getModuleLink('npsmarketplace', 'SellerShop', array('id_seller' => $seller->id)),
                '{product_name}' => $product->name,
                '{product_link}' => $product_link,
                '{voucher_name}' => $cart_rule->name,
                '{voucher_code}' => $cart_rule->code,
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
            );

            if (Mail::Send((int)$this->context->language->id,
                    'voucher_promotion',
                    $this->module->l('Vouchers!', 'Ajax'),
                    $templateVars,
                    $email,
                    null,
                    $seller_customer->email,
                    $seller->name[$this->context->language->id],
                    null,
                    null,
                    _PS_MODULE_DIR_.'npsvouchers/mails/')) {
                $success = $success + 1;
            } else {
                $fail = $fail + 1;
            }
        }
        die(Tools::jsonEncode(array('error' => 0, 'success' => $success, 'fail' => $fail)));
    }
}
