<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsMailactivAtionActivationModuleFrontController extends ModuleFrontController {

    public function initContent() {
        parent::initContent();
        $module = new NpsMailactivAtion();
        $module->execActivation() ? $this->setTemplate('success.tpl') : $this->setTemplate('fail.tpl');
    }
}