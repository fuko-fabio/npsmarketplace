<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsPrzelewy24PaymentCancelModuleFrontController extends ModuleFrontController {

    public function init() {
        parent::init();
        
        d($_POST);
    }
}