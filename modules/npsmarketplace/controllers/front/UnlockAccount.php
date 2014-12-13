<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsMarketplaceUnlockAccountModuleFrontController extends ModuleFrontController {

    const __MA_MAIL_DELIMITOR__ = ',';
    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;

    public function setMedia() {
        parent::setMedia();
        $this->addjQueryPlugin('autosize');
        $this->addJS(_PS_JS_DIR_.'validate.js');
    }

    public function postProcess() {
        if (Tools::isSubmit('submitMessage')) {
            $title = trim(Tools::getValue('title'));
            $message = trim(Tools::getValue('message'));

            if (empty($title))
                $this->errors[] = $this->module->l('Message title is required', 'UnlockAccount');
            else if (!Validate::isMessage($title))
                $this->errors[] = $this->module->l('Invalid message title. Title contains not allowed characters', 'UnlockAccount');
            if (empty($message))
                $this->errors[] = $this->module->l('Message is required', 'UnlockAccount');
            else if (!Validate::isMessage($message))
                $this->errors[] = $this->module->l('Invalid message. Message contains not allowed characters', 'UnlockAccount');

            if (empty($this->errors)) {
                $merchant_emails = Configuration::get('NPS_MERCHANT_EMAILS');
                if (!is_null($merchant_emails) && !empty($merchant_emails)) 
                    $emails = $merchant_emails;
                else
                    $emails = Configuration::get('PS_SHOP_EMAIL');

                $seller = new Seller(null, $this->context->customer->id);

                $mail_params = array(
                    '{seller_name}' => $seller->name,
                    '{seller_email}' => $seller->email,
                    '{message}' => $message,
                    '{admin_link}' => Tools::getHttpHost(true).__PS_BASE_URI__.'backoffice/'.$this->context->link->getAdminLink('AdminSellersAccounts'),
                );

                Mail::Send($this->context->language->id,
                    'unlock_seller',
                    $title,
                    $mail_params,
                    explode(self::__MA_MAIL_DELIMITOR__, $emails),
                    null,
                    $seller->email,
                    $seller->name,
                    null,
                    null,
                    _NPS_MAILS_DIR_);
                Tools::redirect($this->context->link->getModuleLink('npsmarketplace', 'UnlockAccount', array('sent' => true)));
            }
        }
    }

    public function initContent() {
        parent::initContent();

        $seller = new Seller(null, $this->context->customer->id);
        if ($seller->id == null) 
            Tools::redirect('index.php?controller=my-account');
        $this -> context -> smarty -> assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'sent' => Tools::getValue('sent')
        ));
        $this->setTemplate('unlock_account.tpl');
    }
}
?>