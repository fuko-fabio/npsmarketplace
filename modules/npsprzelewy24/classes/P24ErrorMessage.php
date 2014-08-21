<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class P24ErrorMessage {

    public static function get($errorCode) {
        $m = new NpsPrzelewy24();
        $messages = array(
            'err00' => $m->l('Incorrect call.'),
            'err01' => $m->l('Authorization answer confirmation was not received.'),
            'err02' => $m->l('Authorization answer was not received.'),
            'err03' => $m->l('This query has been already processed.'),
            'err04' => $m->l('Authorization query incomplete or incorrect.'),
            'err05' => $m->l('Store configuration cannot be read.'),
            'err06' => $m->l('Saving of authorization query failed.'),
            'err07' => $m->l('Another payment is being concluded.'),
            'err08' => $m->l('Undetermined store connection status.'),
            'err09' => $m->l('Permitted corrections amount has been exceeded.'),
            'err10' => $m->l('Incorrect transaction value!'),
            'err49' => $m->l('To high transaction risk factor.'),
            'err51' => $m->l('Incorrect reference method.'),
            'err52' => $m->l('Incorrect feedback on session information!'),
            'err53' => $m->l('Transaction error!'),
            'err54' => $m->l('Incorrect transaction value!'),
            'err55' => $m->l('Incorrect transaction id!'),
            'err56' => $m->l('Incorrect card.'),
            'err57' => $m->l('Incompatibility of TEST flag!'),
            'err58' => $m->l('Incorrect sequence number!'),
            'err101' => $m->l('Incorrect call.'),
            'err102' => $m->l('Allowed transaction time has expired.'),
            'err103' => $m->l('Incorrect transfer value.'),
            'err104' => $m->l('Transaction awaits confirmation.'),
            'err105' => $m->l('Transaction finished after allowed time.'),
            'err106' => $m->l('Transaction result verification error.'),
            'err161' => $m->l('Transaction request terminated by user.'),
            'err162' => $m->l('Transaction request terminated by user.'),

            // Web service errors
            '1' => $m->l('Access denied.'),
            '500' => $m->l('Missing required data: Company Name, City, PostCode, Street, E-mail or NIP.'),
            '501' => $m->l('Incorrect format for NIP.'),
            '502' => $m->l('Incorrect format for e-mail.'),
            '503' => $m->l('Incorrect IBAN number.'),
            '510' => $m->l('Company already exists!'),
            '511' => $m->l('Company already exists, but not active.'),
            '600' => $m->l('Repeated batch number!'),
            '601' => $m->l('Empty refund list!'),
            '610' => $m->l('Transaction not found.'),
            '699' => $m->l('Errors in refund list. Refunds rejected.'),
            '10000' => $m->l('Unknown error.'),
        );
        $result = $messages[$errorCode];
        return $result != null ? $result : $m->l('Unknown error.');
    }
}