<?php
class NpsMarketplaceOrdersModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();
    $this->setTemplate('Orders.tpl');
  }
}
?>