<?php
class NpsMarketplaceUnlockAccountModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();
    $this->setTemplate('UnlockAccount.tpl');
  }
}
?>