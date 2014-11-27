<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_TOOL_DIR_.'facebook_sdk/autoload.php');

use Facebook\FacebookResponse;
use Facebook\FacebookRequest;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\GraphLocation;
use Facebook\GraphSessionInfo;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookHttpable;
use Facebook\HttpClients\FacebookStream;
use Facebook\HttpClients\FacebookStreamHttpClient;

class NpsFacebookLoginauthModuleFrontController extends ModuleFrontController {

    public $ssl = true;

    public function postProcess() {
        session_start();
        FacebookSession::setDefaultApplication(Configuration::get('NPS_FB_APP_ID'), Configuration::get('NPS_FB_APP_SECRET'));
        $helper = new FacebookRedirectLoginHelper($this->context->link->getModuleLink('npsfacebooklogin', 'auth'));
        //check if a session already exists
        if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
            // create new session from the stored access_token
            $session = new FacebookSession( $_SESSION['fb_token'] );
            // validate the access_token and ensure its validity
            try {
                if ( !$session->validate() ) {
                    $session = null;
                }
            } catch ( Exception $e ) {
                // catch any exceptions
                $session = null;
            }
        }

        // if no session is found
        if ( !isset( $session ) || $session === null ) {
            try {
                $session = $helper->getSessionFromRedirect();}
            catch(FacebookRequestException $ex) {
                // When Facebook returns an error
                $session = null;
                #error_log("Log in with facebook. SDK exception: ".$ex->getMessage(), 0);
            } catch( Exception $ex ) {
                // When validation fails or other local issues
                $session = null;
                #error_log("Log in with facebook. SDK exception: ".$ex->getMessage(), 0);
            }
        }

        // if it were a new session or the session got created as a result of "if no session found" either way
        // set the tokens to bring about session management in terms of remembering and validating the token
        if(isset($session)) { 
            // storing or remembering the session
            $_SESSION['fb_token'] = $session->getToken();
            // create a session using the stored token or the new one we generated at login
            
            $request = (new FacebookRequest($session, 'GET', '/me' ));
            $response = $request->execute();
            $object = $response->getGraphObject();
            $graph_user = $response->getGraphObject(GraphUser::className());
            // getting the profile picture
            $request = new FacebookRequest(
                $session,
                'GET',
                '/me/picture',
                array (
                    'redirect' => false,
                    'height' => '20',
                    'type' => 'normal',
                    'width' => '20',
                )
            );

            $response = $request->execute();
            $graph_user_pic = $response->getGraphObject(GraphUser::className());
            $this->context->cookie->__set('fb_img_url', $graph_user_pic->getProperty('url'));
            $customer = new Customer();
            $email = trim($graph_user->getProperty('email'));
            if (!isset($email) || empty($email)) {
                Tools::redirect($this->context->link->getModuleLink('npsfacebooklogin', 'autherror', array('error' => 'permisions')));
            }
            $authentication = $customer->getByEmail($email);
            if (!$authentication || !$customer->id) {
                $passwd = md5(time());#$this->rand_string(10);
                $customer->email = $graph_user->getProperty('email');
                $customer->firstname = $graph_user->getFirstName();
                $customer->lastname = $graph_user->getLastName();
                $customer->passwd = $passwd;
                $this->processNewUserAuth($customer, $passwd);
            } else {
                $this->processExistingUserAuth($customer);
            }
        } else {
            Tools::redirect($this->context->link->getModuleLink('npsfacebooklogin', 'autherror', array('error' => 'general')));
        }
        
    }

    protected function rand_string( $length ) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars),0,$length);
    }

    protected function processNewUserAuth($customer, $passwd) {
        Hook::exec('actionBeforeSubmitAccount');
        $customer->active = 1;
        $customer->is_guest = 0;
        $customer->add();
        $this->context->customer = $customer;
        $customer->cleanGroups();
        // we add the guest customer in the default customer group
        $customer->addGroups(array((int)Configuration::get('PS_CUSTOMER_GROUP')));
        $this->sendConfirmationMail($customer, $passwd);

        // Update context
        $this->context->customer = $customer;
        $this->context->smarty->assign('confirmation', 1);
        $this->context->cookie->id_customer = (int)$customer->id;
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->logged = 1;
        $this->context->cookie->account_created = 1;
        $customer->logged = 1;
        $this->context->cookie->email = $customer->email;
        $this->context->cookie->is_guest = $customer->is_guest;
        // Update cart address
        $this->context->cart->secure_key = $customer->secure_key;

        // If a logged guest logs in as a customer, the cart secure key was already set and needs to be updated
        $this->context->cart->update();

        // Avoid articles without delivery address on the cart
        $this->context->cart->autosetProductAddress();

        //Hook::exec('actionCustomerAccountAdd', array(
        //    '_POST' => $_POST,
        //    'newCustomer' => $customer
        //));
        // redirect to register address
        Tools::redirect('index.php?controller=address');

/*
        // redirection: if cart is not empty : redirection to the cart
        if (count($this->context->cart->getProducts(true)) > 0)
            Tools::redirect('index.php?controller=order&multi-shipping='.(int)Tools::getValue('multi-shipping'));
        // else : redirection to the account
        else
            Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : 'my-account'));*/

    }

    protected function processExistingUserAuth($customer) {
        $this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
        $this->context->cookie->id_customer = (int)($customer->id);
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->logged = 1;
        $customer->logged = 1;
        $this->context->cookie->is_guest = $customer->isGuest();
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->email = $customer->email;

        // Add customer to the context
        $this->context->customer = $customer;

        if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id))
            $this->context->cart = new Cart($id_cart);
        else {
            $id_carrier = (int)$this->context->cart->id_carrier;
            $this->context->cart->id_carrier = 0;
            $this->context->cart->setDeliveryOption(null);
            $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
            $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)($customer->id));
        }
        $this->context->cart->id_customer = (int)$customer->id;
        $this->context->cart->secure_key = $customer->secure_key;

        $this->context->cart->save();
        $this->context->cookie->id_cart = (int)$this->context->cart->id;
        $this->context->cookie->write();
        $this->context->cart->autosetProductAddress();

        Hook::exec('actionAuthentication');

        // Login information have changed, so we check if the cart rules still apply
        CartRule::autoRemoveFromCart($this->context);
        CartRule::autoAddToCart($this->context);

        Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : 'my-account'));
    }

    protected function sendConfirmationMail(Customer $customer, $passwd) {
        return Mail::Send(
            $this->context->language->id,
            'account',
            Mail::l('Welcome!'),
            array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{passwd}' => $passwd),
            $customer->email,
            $customer->firstname.' '.$customer->lastname
        );
    }
}