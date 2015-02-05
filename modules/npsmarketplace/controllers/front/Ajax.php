<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsmarketplace/npsmarketplace.php');
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class NpsMarketplaceAjaxModuleFrontController extends ModuleFrontController {

    public function __construct() {
        parent::__construct();
        $this->context = Context::getContext();
    }
    public function postProcess()  {
        $this->switchActions();
    }
    public function initContent()  {
        parent::initContent();
        $this->context = Context::getContext();
    }

    protected function switchActions() {
        if (Tools::isSubmit('action')) {
            switch(Tools::getValue('action')) {
                case 'sendToSeller':
                    $this->ajaxProcessSendToSeller();
                    break;
                case 'changeTown':
                    $this->ajaxProcessChangeTown();
                    break;
                case 'changeProvince':
                    $this->ajaxProcessChangeProvince();
                    break;
                case 'specialPrice':
                    $this->ajaxProcessSpecialPrice();
                    break;
                case 'removeSpecialPrice':
                    $this->ajaxProcessRemoveSpecialPrice();
                    break;
                case 'getTheCode':
                    $this->ajaxProcessGetTheCode();
                    break;
            }
        }
    }

    protected function ajaxProcessSendToSeller() {
        if (Tools::getValue('secure_key') == $this->module->secure_key) {
            $question = Tools::getValue('question');
            $email = Tools::getValue('email');
            $id_product = Tools::getValue('id_product');
        
            if (!$question || !$email || !$id_product || !Validate::isEmail($email))
                die('0');

            /* Email generation */
            $product = new Product((int)$id_product, false, $this->context->language->id);
            $productLink = $this->context->link->getProductLink($product);

            $seller = new Seller(Seller::getSellerByProduct($id_product));
            $customer = new Customer($seller->id_customer);
            $templateVars = array(
                '{seller_name}' => $seller->name,
                '{product_name}' => $product->name,
                '{product_link}' => $productLink,
                '{question}' => Tools::safeOutput($question),
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
            );

            /* Email sending */
            if (!Mail::Send((int)$this->context->language->id,
                    'question_to_seller',
                    sprintf($this->module->l('Question about %1$s', (int)$this->context->language->id), $product->name),
                    $templateVars,
                    $customer->email,
                    $seller->name,
                    $email,
                    ($this->context->cookie->customer_firstname ? $this->context->cookie->customer_firstname.' '.$this->context->cookie->customer_lastname : null),
                    null,
                    null,
                    _PS_MODULE_DIR_.'npsmarketplace/mails/'))
                die('0');
            die('1');
        }
        die('0');
    }

    protected function ajaxProcessChangeTown() {
        $town = new Town(Tools::getValue('id_town'));
        $this->context->cookie->__set('main_town', $town->id);
        $this->context->cookie->__set('main_province', $town->id_province);
        die('1');
    }

    protected function ajaxProcessChangeProvince() {
        $this->context->cookie->__set('main_province', Tools::getValue('id_province'));
        $this->context->cookie->__set('main_town', 0);
        die('1');
    }

    protected function ajaxProcessSpecialPrice() {
        die(Tools::jsonEncode(Product::addSpecialPrice(Tools::getValue('id_product'), Tools::getValue('reduction'))));
    }

    protected function ajaxProcessRemoveSpecialPrice() {
        die(Tools::jsonEncode(Product::removeSpecialPrice(Tools::getValue('id_product'))));
    }

    protected function ajaxProcessGetTheCode() {
       die(Tools::jsonEncode($this->module->getIframeCode()));
    }
}
