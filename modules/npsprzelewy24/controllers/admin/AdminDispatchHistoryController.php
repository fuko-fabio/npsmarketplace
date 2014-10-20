<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24DispatchHistory.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24Payment.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24PaymentStatement.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24.php');
include_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/P24TransactionDispatcher.php');

class AdminDispatchHistoryController extends AdminController {
    protected $delete_mode;

    protected $_defaultOrderBy = 'date';
    protected $_defaultOrderWay = 'DESC';

    public function __construct() {
        $this->bootstrap = true;
        $this->required_database = true;
        $this->table = 'p24_dispatch_history';
        $this->className = 'P24DispatchHistory';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->addRowAction('view');
        $this->deleted = false;
        $this->base_tpl_view = 'dispatch_history_view.tpl';

        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;

        $this->fields_list = array(
            'id_p24_dispatch_history' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
            ),
            'id_payment' => array(
                'title' => $this->l('Payment ID'),
                'align' => 'text-center',
            ),
            'sellers_amount' => array(
                'title' => $this->l('Sellers Amount'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setCurrency',
                'badge_success' => true
            ),
            'sellers_number' => array(
                'title' => $this->l('Sellers Number'),
                'align' => 'text-center',
            ),
            'p24_amount' => array(
                'title' => $this->l('Przelewy24 Amount'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setCurrency',
                'badge_success' => true
            ),
            'merchant_amount' => array(
                'title' => $this->l('Merchant Amount'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setCurrency',
                'badge_success' => true
            ),
            'total_amount' => array(
                'title' => $this->l('Total Amount'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setCurrency',
                'badge_success' => true
            ),
            'status' => array(
                'title' => $this->l('Status'),
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

    public function renderView() {
        $obj = $this->loadObject(true);
        $payment_summary = P24PaymentStatement::getSummary($obj->id_payment);

        $order = new Order(Order::getOrderByCartId($payment_summary['id_cart']));
        if (!Validate::isLoadedObject($order))
            $this->errors[] = Tools::displayError('The order cannot be found within your database.');

        $customer = new Customer($order->id_customer);
        $products = $this->getProducts($order);
        $res = P24::checkFunds($payment_summary['order_id'], $payment_summary['session_id']);
        $this->tpl_view_vars = array(
            'available_funds' => $res->result,
            'order' => $order,
            'cart' => new Cart($order->id_cart),
            'customer' => $customer,
            'products' => $products,
            'currency' => new Currency($order->id_currency),
            'customer_addresses' => $customer->getAddresses($this->context->language->id),
            'display_warehouse' => (int)Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
            'dispatch_history' => $obj->getDetails(),
            'history' => $obj,
        );

        $helper = new HelperView($this);
        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->tpl_view_vars;
        $helper->base_folder = _PS_MODULE_DIR_.'npsprzelewy24/views/templates/admin/';
        if (!is_null($this->base_tpl_view))
            $helper->base_tpl = $this->base_tpl_view;
        $view = $helper->generateView();

        return $view;
    }

    protected function getProducts($order) {
        $products = $order->getProducts();

        foreach ($products as &$product) {
            if ($product['image'] != null) {
                $name = 'product_mini_'.(int)$product['product_id'].(isset($product['product_attribute_id']) ? '_'.(int)$product['product_attribute_id'] : '').'.jpg';
                // generate image cache, only for back office
                $product['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_.'p/'.$product['image']->getExistingImgPath().'.jpg', $name, 45, 'jpg');
                if (file_exists(_PS_TMP_IMG_DIR_.$name))
                    $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
                else
                    $product['image_size'] = false;
            }
        }
        return $products;
    }

    public function initProcess() {
        parent::initProcess();

        if (Tools::isSubmit('retryDispatch') && $this->id_object) {
            if ($this->tabAccess['edit'] === '1')
                $this->action = 'retry_dispatch';
            else
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        }
    }

    public function processRetryDispatch() {
        $id_cart = trim(Tools::getValue('id_cart'));
        $dispatcher = new P24TransactionDispatcher($id_cart);
        $dispatcher->dispatchMoney(true);
    }
}