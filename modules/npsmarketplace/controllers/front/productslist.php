<?php
class NpsMarketplaceProductsListModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();
    $this->setTemplate('productslist.tpl');
  }
}
?>