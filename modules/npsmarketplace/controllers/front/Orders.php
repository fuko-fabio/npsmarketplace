<?php
class NpsOrdersModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {
    parent::initContent();
    $this->setTemplate('orders.tpl');
  }
}
?>