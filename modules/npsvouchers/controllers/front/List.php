<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsVouchersListModuleFrontController extends ModuleFrontController {

    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;
    private $seller;

    public function setMedia() {
        parent::setMedia();
        $this->addJqueryPlugin('footable');
        $this->addJqueryPlugin('footable-sort');
        $this->addJqueryPlugin('scrollTo');
        $this->addJS(_PS_JS_DIR_.'validate.js');
        $this->addJS(_PS_MODULE_DIR_.'npsvouchers/js/jquery.tagify.js');
        
        $this->addJS(_PS_MODULE_DIR_.'npsvouchers/js/voucher_list.js');
    }

    public function init() {
        $this->page_name = 'vouchers';
        parent::init();
        $this->seller = new Seller(null, $this->context->customer->id);
        if ($this->seller->id == null) 
            Tools::redirect('index.php?controller=my-account');
    }

    public function initContent() {
        $this->display_column_left = false;
        $this->display_column_right = false;
        parent::initContent();

        $nbVouchers = $this->getVouchers(0, 0, true);
        $this->pagination($nbVouchers);
        $this->context->smarty->assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'new_voucher_link' => $this->context->link->getModuleLink('npsvouchers', 'Voucher'),
            'vouchers' => $this->getVouchers($this->p, $this->n),
            'id_currency' => $this->context->currency->id,
            'nps_vouchers_ajax_url' => $this->context->link->getModuleLink('npsvouchers', 'Ajax'),
        ));

        $this -> setTemplate('list.tpl');
    }

    private function getVouchers($p = 0, $n = 0, $count = false) {
        if ($count)
            return Db::getInstance()->getValue('SELECT count(id_seller) FROM '._DB_PREFIX_.'seller_cart_rule WHERE id_seller = '.$this->seller->id);
        
        $dbquery = new DbQuery();
        $dbquery->select('*')
            ->from('cart_rule', 'cr')
            ->leftJoin('seller_cart_rule', 'scr', 'cr.id_cart_rule = scr.id_cart_rule')
            ->leftJoin('cart_rule_lang', 'crl', 'cr.id_cart_rule = crl.id_cart_rule')
            ->where('scr.`id_seller` = '.$this->seller->id.' AND crl.`id_lang` = '.$this->context->language->id)
            ->orderBy('cr.id_cart_rule DESC');
        if ($n > 0)
            $dbquery->limit($n, (((int)$p - 1) * (int)$n));
        $result = Db::getInstance()->executeS($dbquery);
        
        foreach ($result as $key => $value) {
            $result[$key]['edit_url'] = $this->context->link->getModuleLink('npsvouchers', 'Voucher', array(
                'id_voucher' => $result[$key]['id_cart_rule'])
            );
            $result[$key]['delete_url'] = $this->context->link->getModuleLink('npsvouchers', 'Voucher', array(
                'action' => 'delete',
                'id_voucher' => $result[$key]['id_cart_rule'])
            );
        }
        return $result;
    }
}
?>