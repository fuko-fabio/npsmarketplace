<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsVouchersVoucherModuleFrontController extends ModuleFrontController {
    
    const MAX_LENGTH = 250;
    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;
    private $seller;

    public function setMedia() {
        parent::setMedia();
        $this->addJS(_PS_JS_DIR_.'validate.js');
        $this->addJS(_PS_MODULE_DIR_.'npsvouchers/js/bootstrap-datetimepicker.min.js');
        $this->addJS(_PS_MODULE_DIR_.'npsvouchers/js/voucher.js');

        $this->addCSS(_PS_MODULE_DIR_.'npsvouchers/css/bootstrap-datetimepicker.min.css');
    }

    public function postProcess() {
        if (Tools::getValue('action') == 'delete') {
            $this->processDelete();
        }
        if (Tools::isSubmit('saveVoucher')) {
            $is_new = true;
            $id_voucher = trim(Tools::getValue('id_voucher'));
            $name = trim(Tools::getValue('name'));
            $code = trim(Tools::getValue('code'));
            $from = trim(Tools::getValue('from'));
            $to = trim(Tools::getValue('to'));
            $quantity = trim(Tools::getValue('quantity'));
            $id_product = trim(Tools::getValue('id_product'));
            $type = trim(Tools::getValue('type'));
            $discount = trim(Tools::getValue('discount'));
            $cart_rule = new CartRule($id_voucher);
            if ($cart_rule->id) {
                $is_new = false;
            }
            if (empty($name))
                $this->errors[] = $this->module->l('Voucher \'name\' is required', 'Voucher');
            else if (!Validate::isGenericName($name))
                $this->errors[] = $this->module->l('Invalid voucher \'name\'', 'Voucher');
            else if (strlen($name) > self::MAX_LENGTH)
                $this->errors[] = sprintf($this->module->l('Voucher \'name\' is too long. Max allowed characters %d', 'Voucher'), self::MAX_LENGTH);
            if (empty($code))
                $this->errors[] = $this->module->l('Voucher \'code\' is required', 'Voucher');
            else if (!Validate::isGenericName($code))
                $this->errors[] = $this->module->l('Invalid voucher \'code\'', 'Voucher');
            else if (strlen($code) > self::MAX_LENGTH)
                $this->errors[] = sprintf($this->module->l('Voucher \'code\' is too long. Max allowed characters 254', 'Voucher'), self::MAX_LENGTH);
            else if (!$cart_rule->id && CartRule::getIdByCode($code))
                $this->errors[] = $this->module->l('This voucher code is already used by other voucher. Please generate unique code', 'Voucher');
            if (empty($from))
                $this -> errors[] = $this->module->l('Voucher \'valid from\' time is required', 'Voucher');
            else if (!Validate::isDateFormat($from))
                $this -> errors[] = $this->module->l('Invalid \'valid from\' time format', 'Voucher');
            if (empty($to))
                $this -> errors[] = $this->module->l('Voucher \'valid to\' time is required', 'Voucher');
            else if (!Validate::isDateFormat($to))
                $this -> errors[] = $this->module->l('Invalid \'valid to\' time format', 'Voucher');
            if (strtotime($from) > strtotime($to))
                $this->errors[] = $this->module->l('The voucher cannot end before it begins.', 'Voucher');
            if (empty($quantity))
                $this -> errors[] = $this->module->l('Voucher \'quantity\' is required', 'Voucher');
            else if (!Validate::isInt($quantity) || $quantity < 1)
                $this -> errors[] = $this->module->l('Invalid voucher \'quantity\' format', 'Voucher');
            if (empty($id_product))
                $this -> errors[] = $this->module->l('Voucher \'product\' is required', 'Voucher');
            else if (!Validate::isInt($id_product))
                $this -> errors[] = $this->module->l('Invalid voucher \'product\'', 'Voucher');
    
            if ($type == 'percent') {
                if (empty($discount))
                    $this->errors[] = $this->module->l('Voucher discount value is required', 'Voucher');
                else if (!Validate::isPercentage($discount))
                    $this->errors[] = $this->module->l('Invalid voucher discount value. Accepted range from  0 to 100', 'Voucher');
            } else if ($type == 'price') {
                if (empty($discount))
                    $this->errors[] = $this->module->l('Voucher discount value is required', 'Voucher');
                else if (!Validate::isPrice($discount))
                    $this->errors[] = $this->module->l('Invalid voucher discount value. Accepted example format: 10.50', 'Voucher');
            } else {
                $this->errors[] = $this->module->l('Invalid voucher discount type', 'Voucher');
            }
    
            if (empty($this->errors)) {
                $cart_rule->code = $code;
                $cart_rule->name = array($this->context->language->id => $name);
                $cart_rule->quantity = $quantity;
                $cart_rule->reduction_product = $id_product;
                $cart_rule->date_from = $from.' 00:00:01';
                $cart_rule->date_to = $to.' 23:59:59';
                if ($type == 'percent') {
                    $cart_rule->reduction_percent = $discount;
                    $cart_rule->reduction_amount = null;
                } else if ($type == 'price') {
                    $cart_rule->reduction_amount = $discount;
                    $cart_rule->reduction_percent = null;
                }
                if ($is_new) {
                    $cart_rule->active = 1;
                    $cart_rule->partial_use = 0;
                    $cart_rule->quantity_per_user = 1;
                    $cart_rule->free_shipping = false;
                    $cart_rule->reduction_currency = (int)$this->context->cart->id_currency;
                }
                if (!$cart_rule->save()) {
                    $this->errors[] = $this->module->l('Unable to save voucher. Please contact with customer support.', 'Voucher');
                } else {
                    if ($is_new) {
                        Db::getInstance()->insert('seller_cart_rule', array(
                            'id_seller' => $this->seller->id,
                            'id_cart_rule' => $cart_rule->id
                        ));
                    }
                    Tools::redirect($this->context->link->getModuleLink('npsvouchers', 'List'));
                }
            }
        }
    }

    public function init() {
        $this->page_name = 'vouchers';
        parent::init();
        $this->seller = new Seller(null, $this->context->customer->id);
        if (!$this->seller->id) 
            Tools::redirect('index.php?controller=my-account');
    }

    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();
        $id_voucher = trim(Tools::getValue('id_voucher'));
        $products = $this->getProducts();

        $this->context->smarty->assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'user_agreement_url' => Configuration::get('NPS_SELLER_AGREEMENT_URL'),
            'products' => $products,
            'voucher' => $this->getVoucher($id_voucher),
            'id_currency' => $this->context->currency->id,
            'back_url' => $this->context->link->getModuleLink('npsvouchers', 'List'),
            'delete_url' => $this->context->link->getModuleLink('npsvouchers', 'Voucher', array(
                'action' => 'delete',
                'id_voucher' => $id_voucher)
            )
        ));

        if (empty($products)) {
            $this->errors[] = $this->module->l('You do not have any active product or for all products vouchers are already defined.', 'Voucher');
        }
        $this -> setTemplate('voucher.tpl');
    }

    private function getVoucher($id_voucher) {
        $cart_rule = new CartRule($id_voucher);
        if ($cart_rule->id) {
            if ($cart_rule->reduction_percent > 0) {
                $discount = $cart_rule->reduction_percent;
                $type = 'percent';
            } else if ($cart_rule->reduction_amount > 0) {
                $discount = $cart_rule->reduction_amount;
                $type = 'price';
            }
            return array(
                'id' => Tools::getValue('id_voucher'),
                'name' => $cart_rule->name[$this->context->language->id],
                'code' => $cart_rule->code,
                'from' => date('Y-m-d', strtotime($cart_rule->date_from)),
                'to' => date('Y-m-d', strtotime($cart_rule->date_to)),
                'quantity' => $cart_rule->quantity,
                'id_product' => $cart_rule->reduction_product,
                'discount' => $discount,
                'type' => $type
            );
        }
        return array();
    }

    private function getProducts() {
        $result = array();
        $ids = array_diff(
            Seller::getSellerProducts($this->seller->id, 0, 0, true),
            $this->getProductsIdsForSellerVouchers()
        );
        if (!empty($ids)) {
            $dbquery = new DbQuery();
            $dbquery->select('p.`id_product`, p.`price`, pl.`name`, MAX(paed.`expiry_date`) AS date_to')
                ->from('product', 'p')
                ->leftJoin('product_lang', 'pl', 'p.id_product = pl.id_product')
                ->leftJoin('product_attribute_expiry_date', 'paed', 'p.id_product = paed.id_product')
                ->where('p.`id_product` IN ('.implode(',', $ids).') AND pl.`id_lang` = '.$this->context->language->id)
                ->groupBy('p.id_product')
                ->orderBy('pl.name ASC');
            $result = Db::getInstance()->executeS($dbquery);
            
            foreach ($result as $key => $value) {
                $result[$key]['quantity'] = Product::getQuantity($result[$key]['id_product']);
            }
        }
        return $result;
    }

    private function getProductsIdsForSellerVouchers() {
        $dbquery = new DbQuery();
        $dbquery->select('reduction_product')
            ->from('cart_rule', 'cr')
            ->leftJoin('seller_cart_rule', 'scr', 'cr.id_cart_rule = scr.id_cart_rule')
            ->where('scr.`id_seller` = '.$this->seller->id);
        $result = Db::getInstance()->executeS($dbquery);
        $ids = array();
        foreach ($result as $key => $value) {
            $ids[] = $value['reduction_product'];
        }
        return $ids;
    }

    private function processDelete() {
        $id_cart_rule = trim(Tools::getValue('id_voucher'));
        $cart_rule = new CartRule($id_cart_rule);
        if ($cart_rule->id && $cart_rule->delete()) {
            Db::getInstance()->delete('seller_cart_rule', 'id_cart_rule = '.$id_cart_rule);
            Tools::redirect($this->context->link->getModuleLink('npsvouchers', 'List'));
        } else {
            $this->errors[] = $this->module->l('Unable to delete voucher. Please contact with customer support.', 'Voucher');
        }
    }
}
?>