<?php
class NpsMyShopModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();
    $this->setTemplate('my_shop.tpl');
  }
}
?>