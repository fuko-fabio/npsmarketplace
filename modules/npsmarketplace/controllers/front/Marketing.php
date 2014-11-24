<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsMarketplaceMarketingModuleFrontController extends ModuleFrontController {

    public $auth = true;
    public $authRedirection = 'my-account';
    public $ssl = true;

    public function setMedia() {
        parent::setMedia();
        $this->addjQueryPlugin('autosize');
        $this->addJS (_PS_MODULE_DIR_.'npsmarketplace/js/marketing.js');
    }

    public function initContent() {
        parent::initContent();

        $seller = new Seller(null, $this->context->customer->id);
        if ($seller->id == null) 
            Tools::redirect('index.php?controller=my-account');
        $this -> context -> smarty -> assign(array(
            'HOOK_MY_ACCOUNT_COLUMN' => Hook::exec('displayMyAccountColumn'),
            'languages' => Language::getLanguages(),
            'current_lang_id' => $this->context->language->id,
            'towns' => Town::getActiveTowns((int)$this->context->language->id),
            'nps_ajax_url' => $this->context->link->getModuleLink('npsmarketplace', 'Ajax'),
        ));
        $this->setTemplate('marketing.tpl');
    }
}
?>