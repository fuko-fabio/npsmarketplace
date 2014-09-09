<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24DispatchHistory.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24Payment.php');

class AdminDispatchHistoryController extends AdminController {
    protected $delete_mode;

    protected $_defaultOrderBy = 'date';
    protected $_defaultOrderWay = 'DESC';

    public function __construct() {
        $this->bootstrap = true;
        $this->required_database = true;
        $this->table = 'p24_dispatch_history';
        $this->list_id = 'id_dispatch_history';
        $this->className = 'P24DispatchHistory';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->allow_export = true;

        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;

        $this->fields_list = array(
            'id_payment' => array(
                'title' => $this->l('Payment ID'),
                'align' => 'text-center',
            ),
            'order_id' => array(
                'title' => $this->l('Order ID'),
                'align' => 'text-center',
            ),
            'session_id' => array(
                'title' => $this->l('Session ID'),
                'align' => 'text-center',
            ),
            'spid' => array(
                'title' => $this->l('Seller ID'),
                'align' => 'text-center',
            ),
            'amount' => array(
                'title' => $this->l('Total Amount'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setCurrency',
                'badge_success' => true
            ),
            'error' => array(
                'title' => $this->l('Error'),
                'align' => 'text-center',
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printIcon',
                'orderby' => false
            ),
            'merchant' => array(
                'title' => $this->l('Is Merchant'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printIcon',
                'orderby' => false
            ),
            'date' => array(
                'title' => $this->l('Transaction Date'),
                'type' => 'date',
                'align' => 'text-right'
            ),
        );

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_CUSTOMER;

        parent::__construct();
    }

    public function initToolbarTitle() {
        parent::initToolbarTitle();

        switch ($this->display) {
            case '':
                $this->toolbar_title[] = $this->l('Przelewy24 Dispatch History');
                break;
        }
    }

    public function printIcon($value) {
        return $value ? '<i class="icon-check"></i>' : '<i class="icon-remove"></i>';
    }

    public static function setCurrency($echo, $tr) {
        $payment = new P24Payment((int)$tr['id_payment']);
        return Tools::displayPrice($echo / 100, (int)Currency::getIdByIsoCode($payment->currency_iso));
    }
}