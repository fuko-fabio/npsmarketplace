<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class P24 {

    public static function transactionRegister($data) {
        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
            $sandbox_descr = Configuration::get('NPS_P24_SANDBOX_ERROR');
            if(!empty($sandbox_descr)) {
                $data['p24_description'] = $sandbox_descr;
            }
        }
        return P24::callCurl('/trnRegister', $data);
    }

    public static function transactionVerify($data) {
        $result = P24::callCurl('/trnVerify', $data);
        if ($result['error'] != 0) {
            $module = new NpsPrzelewy24();
            $module->reportError(array(
               'Requested URL: '.P24::url().'/trnVerify',
               'Request params: '.implode(' | ', $data),
               'Response: '.implode(' | ', $result)
             ));
        }
        return $result;
    }

    private static function callCurl($method, $data) {
        $ch = curl_init(P24::url().$method);
        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $verbose = fopen('php://temp', 'rw+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,true);
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 0);
        curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        if ($output = curl_exec($ch)) {
            $INFO = curl_getinfo($ch);
            if($INFO["http_code"]!=200) {
                $result = array("error" => 200, "errorMessage" => "call:Page load error (".$INFO["http_code"].")");
            } else {
                parse_str($output, $result);
            }
        } else { 
            $result = array("error" => 203,"errorMessage" => "call:Curl exec error");
        }
        curl_close($ch);
        
        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
        }
        return $result;
    }

    public static function checkNIP($nip) {
        $soap = new SoapClient(P24::soapProductionUrl());
        return $soap->CheckNIP(
            P24::merchantId(),
            P24::apiKey(),
            $nip);
    }

    public static function companyRegister($company) {
        $soap = new SoapClient(P24::soapProductionUrl());
        return $soap->CompanyRegister(
            P24::merchantId(),
            P24::apiKey(),
            $company);
    }

    public static function dispatchMoney($batch_id, $dispatch_req) {
        $soap = new SoapClient(P24::soapUrl());
        return $soap->TrnDispatch(
            P24::merchantId(),
            P24::apiKey(),
            $batch_id,
            $dispatch_req);
    }

    public static function checkFunds($order_id, $session_id) {
        $soap = new SoapClient(P24::soapUrl());
        return $soap->TrnCheckFunds(
            P24::merchantId(),
            P24::apiKey(),
            $order_id,
            $session_id);
    }

    public static function merchantId() {
        return Configuration::get('NPS_P24_MERCHANT_ID');
    }

    public static function uniqueKey() {
        return Configuration::get('NPS_P24_UNIQUE_KEY');
    }

    public static function crcKey() {
        return Configuration::get('NPS_P24_CRC_KEY');
    }

    public static function apiKey() {
        return Configuration::get('NPS_P24_API_KEY');
    }

    public static function url() {
        $url = Configuration::get('NPS_P24_URL');
        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
            $url = Configuration::get('NPS_P24_SANDBOX_URL');
        }
        return $url;
    }

    private static function soapUrl() {
        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1) {
            return P24::soapSandboxUrl();
        }
        return P24::soapProductionUrl();
    }

    private static function soapProductionUrl() {
        return Configuration::get('NPS_P24_WEB_SERVICE_URL');
    }

    private static function soapSandboxUrl() {
        return Configuration::get('NPS_P24_SANDBOX_WEB_SERVICE_URL');
    }
}
