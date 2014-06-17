<?php
class NpsMarketplacePaymentSettingsModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();
    $this->setTemplate('payentsettings.tpl');
  }
}
?>