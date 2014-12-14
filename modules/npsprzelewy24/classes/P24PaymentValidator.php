<?php
/**
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24Payment.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentStatement.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24.php');

/**
 * Class allows to validate przelewy24 payment result
 */
class P24PaymentValodator {

    public $session_id;
    public $amount;
    public $currency;
    public $order_id;
    public $method;
    public $statement;
    public $sign;

    /**
     * Build object
     *
     * @param String $session_id merchant generated session ID
     * @param String $amount merchant order amount
     * @param String $currency merchant order currency
     * @param String $order_id przelewy24 order ID
     * @param String $method przelewy24 payment method ID
     * @param String $statement przelewy24 payment statement
     * @param String $sign ID przelewy24 request sign
     */
    public function __construct($session_id, $amount, $currency, $order_id, $method, $statement, $sign) {
        $this->session_id = $session_id;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->order_id = $order_id;
        $this->method = $method;
        $this->statement = $statement;
        $this->sign = $sign;
    }

    /** validate Validates przelewy24 payment result and verifies it
    * @param String $p24_token token generated for Przelewy24 service
    *
    * @return Array result with error code and error message if needed
    */
    public function validate($p24_token, $background = false) {
        $prefix = '';
        if($background)
            $prefix = 'Background ';

        $module = new NpsPrzelewy24();
        if (isset($this->session_id) && $this->verifySign()) {
            $session_id_array = explode('|', $this->session_id);
            $id_cart = $session_id_array[1];
            $p24_payment = P24Payment::getBySessionId($this->session_id);
            if ($p24_payment == null) {
                 $module->reportError(array(
                    $prefix.'P24PaymentValodator:',
                    ' Unabe to verify payment. Could not find database payment entry for session ID: '.$this->session_id
                ));
                return array('error' => 1, 'errorMessage' => $module->errorMsg('intErr01'));
            }

            if($p24_payment->id != null) {
                if($p24_payment->token != $p24_token) {
                    $module->reportError(array(
                        $prefix.'P24PaymentValodator:',
                        'Unabe to verify payment. Invalid p24 token value',
                        'Expected: '.$p24_payment->token,
                        'Current: '.$p24_token
                    ));
                    return array('error' => 1, 'errorMessage' => $module->errorMsg('intErr02'));
                }
                $ps = new P24PaymentStatement(null, $p24_payment->id);
                if($ps->id != null) {
                    return array('error' => -1, 'errorMessage' => $module->errorMsg('intErr03'));
                }
                
                $result = $this->transactionVerify();
                if ($result['error'] == 0) {
                    $this->persistPaymentStatement($p24_payment->id);
                    $this->updateOrdetState($id_cart);
                    return array('error' => 0);
                } else {
                    $module->reportError(array(
                        $prefix.'P24PaymentValodator:',
                        'Unabe to verify payment. Error code: '.$result['error'],
                        'Received message: '.$result['errorMessage']
                    ));
                    return $result;
                }
            } else {
                $module->reportError(array(
                    $prefix.'P24PaymentValodator:',
                    ' Unabe to verify payment. Invalid Przelewy24 response data',
                    implode("&", $_POST)
                ));
                return array('error' => 1, 'errorMessage' => $module->errorMsg('intErr01'));
            }
        } else {
            $module->reportError(array(
                $prefix.'P24PaymentValodator:',
                'Unabe to verify payment. Invalid session ID',
                'Current: '.$this->session_id,
                'Expected: '.$this->generateSign()
            ));
            return array('error' => 1, 'errorMessage' => $module->errorMsg('intErr04'));
        }
    }

    /** persistPaymentStatement Persists payment status returned from przelewy24
    * 
    * @return boolean true if status has been successful stored
    */
    private function persistPaymentStatement($p_id) {
        $ps = new P24PaymentStatement();
        $ps->id_payment = $p_id;
        $ps->order_id = $this->order_id;
        $ps->payment_method = $this->method;
        $ps->statement = $this->statement;
        return $ps->save();
    }

    /** updateOrdetState Updates order state if payment was verified by przelewy24
    *
    * @param int $id_cart Cart ID
    *
    * @return void
    */
    private function updateOrdetState($id_cart) {
        $order_id = Order::getOrderByCartId(intval($id_cart));
        $order = new Order($order_id);

        $history = new OrderHistory();
        $history->id_order = intval($order_id);

        $order_state = Configuration::get('NPS_P24_ORDER_STATE_ACCEPTED');
        $history->changeIdOrderState($order_state, intval($order_id), true);
        $history->addWithemail(true);

        $payments = $order->getOrderPaymentCollection();
        if (count($payments) > 0) {
            $payments[0]->transaction_id = $this->order_id;
            $payments[0]->update();
        }
    }

    /** transactionVerify Makes POST request 'trnVerify' to verify payment
    *
    * @return Array result from przelewy24
    */
    private function transactionVerify() {
        $data = array(
            'p24_merchant_id' => P24::merchantId(),
            'p24_pos_id' => P24::merchantId(),
            'p24_session_id' => $this->session_id,
            'p24_amount' => $this->amount,
            'p24_currency' => $this->currency,
            'p24_order_id' => $this->order_id,
            'p24_sign' => $this->generateSign(),
        );
        return P24::transactionVerify($data);
    }

    /** verifySign Verify incoming sign from przelewy24 with local stored sign
    *
    * @return boolean true if signs are equal 
    */
    private function verifySign() {
        return $this->sign == $this->generateSign();
    }

    /** generateSign Generates sign for przelewy24 requests
    *
    * @return String encoded sign
    */
    private function generateSign() {
        return md5($this->session_id.'|'.$this->order_id.'|'.$this->amount.'|'.$this->currency.'|'.Configuration::get('NPS_P24_CRC_KEY'));
    }
}