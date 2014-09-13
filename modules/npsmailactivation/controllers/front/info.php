<?php

class NpsMailactivAtionInfoModuleFrontController extends ModuleFrontController {

    public function initContent() {
        parent::initContent();
        $this->setTemplate('info.tpl');
    }
}