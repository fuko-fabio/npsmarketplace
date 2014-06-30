<?php
class NpsMarketplacePaymentSettingsModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();
    $this->setTemplate('PayentSettings.tpl');
  }
}
?>