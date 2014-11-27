<?php
/*
 *  @author Norbert Pabian <norbert.pabian@gmail.com>
 *  @copyright 2014 npsoftware
 */
require_once (_PS_TOOL_DIR_ . 'facebook_sdk/autoload.php');

class Customer extends CustomerCore {

    public function logout() {
        $this->facebookLogout();
        parent::logout();
    }

    public function mylogout() {
        $this->facebookLogout();
        parent::mylogout();
    }

    private function facebookLogout() {
        if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
            unset($_SESSION['fb_token']);
        }
        Context::getContext()->cookie->__unset('fb_img_url');
    }

}
