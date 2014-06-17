<?php
class NpsMarketplaceAddProductModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();
    $this->setTemplate('addproduct.tpl');
  }
}
?>