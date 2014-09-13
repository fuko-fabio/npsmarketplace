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
        $ch = curl_init(P24::url().'/trnRegister');
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

    public static function transactionVerify($data) {
        $ch = curl_init(P24::url().'/trnVerify');
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

    public static function checkNIP($nip) {
        $soap = new SoapClient(P24::soapProductionUrl());
        return $soap->CheckNIP(
            P24::merchantId(),
            P24::apiKey(),
            $nip);
    }

    public static function companyRegister($company) {
        $soap = new SoapClient(P24::soapUrl());
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
        if (Configuration::get('NPS_P24_SANDBOX_MODE') == 1)
            return P24::soapSandboxUrl();
        return P24::soapProductionUrl();
    }

    private static function soapProductionUrl() {
        return Configuration::get('NPS_P24_WEB_SERVICE_URL');
    }

    private static function soapSandboxUrl() {
        return Configuration::get('NPS_P24_SANDBOX_WEB_SERVICE_URL');
    }
}
