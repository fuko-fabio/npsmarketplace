<?php
/**
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24Payment.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentStatement.php');

/**
 * Class allows to validate przelewy24 payment result
 */
class P24PaymentValodator {

    public $merchant_id;
    public $pos_id;
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
     * @param int $merchant_id przelewy24 merchant ID
     * @param int $pos_id przelewy24 pos ID (by default merchant ID)
     * @param String $session_id merchant generated session ID
     * @param String $amount merchant order amount
     * @param String $currency merchant order currency
     * @param String $order_id przelewy24 order ID
     * @param String $method przelewy24 payment method ID
     * @param String $statement przelewy24 payment statement
     * @param String $sign ID przelewy24 request sign
     */
    public function __construct($merchant_id, $pos_id, $session_id, $amount, $currency, $order_id, $method, $statement, $sign) {
        $this->merchant_id = $merchant_id;
        $this->pos_id = $pos_id;
        $this->session_id = $session_id;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->order_id = $order_id;
        $this->method = $method;
        $this->statement = $statement;
        $this->sign = $sign;
    }

    /** validate Validates przelewy24 payment result and verifies it
    *
    * @return Array result with error code and error message if needed
    */
    public function validate() {
        if (isset($this->session_id) && $this->verifySign()) {
            $session_id_array = explode('|', $this->session_id);
            $id_cart = $session_id_array[1];
            $timestamp = $session_id_array[1];
            $p24_payment = P24Payment::getBySessionId($this->session_id);

            if($p24_payment->id != null) {

                $result = $this->transactionVerify();
                if ($result['error'] == 0) {
                    $this->persistPaymentStatement($p24_payment->id);
                    $this->updateOrdetState($id_cart);
                    return array('error' => 0);
                } else {
                    PrestaShopLogger::addLog('P24PaymentValodator: Unabe to verify payment. Error code: '.$result['error'].' Received message: '.$result['errorMessage']);
                    return $result;
                }

             } else {
                PrestaShopLogger::addLog('P24PaymentValodator: Unabe to verify payment. Invalid Przelewy24 response data'.implode("&", $_POST));
                return array('error' => -1, 'errorMessage' => 'Invalid response from przelewy24');
             }
        } else {
            PrestaShopLogger::addLog('P24PaymentValodator: Unabe to verify payment. Invalid session ID');
            return array('error' => -1, 'errorMessage' => 'Invalid session ID');
        }
    }

    /** persistPaymentStatement Persists payment status returned from przelewy24
    * 
    * @param int $p_id merchant local payment ID
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

        $order_state = Configuration::get('NPS_P24_ORDER_STATE_2');
        $history->changeIdOrderState($order_state, intval($order_id));
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
        $url = Configuration::get('NPS_P24_URL');
        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
            $url = Configuration::get('NPS_P24_SANDBOX_URL');
        }

        $data = array(
            'p24_merchant_id' => $this->merchant_id,
            'p24_pos_id' => $this->pos_id,
            'p24_session_id' => $this->session_id,
            'p24_amount' => $this->amount,
            'p24_currency' => $this->currency,
            'p24_order_id' => $this->order_id,
            'p24_sign' => $this->generateSign(),
        );

        $ch = curl_init($url.'/trnVerify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,true);
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'SSLv3');
        $output=curl_exec($ch);
        curl_close($ch);

        parse_str($output, $result);
        return $result;
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