<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include(dirname(__FILE__).'/../../../config/config.inc.php');
include dirname(__FILE__).'/../../../init.php';
require_once _PS_MODULE_DIR_.'npsprzelewy24/classes/HTMLTemplateSalesReport.php';
require_once _PS_MODULE_DIR_.'npsprzelewy24/classes/SellerInvoice.php';
require_once _PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php';


    define('_NPS_REPORTS_DIR_', '/sales_reports/');
    @mkdir(_PS_ROOT_DIR_._NPS_REPORTS_DIR_);

    define('_NPS_SELLER_REPORTS_DIR_', _NPS_REPORTS_DIR_.'sellers/');
    @mkdir(_PS_ROOT_DIR_._NPS_SELLER_REPORTS_DIR_);


$sql = 'SELECT `id_seller` FROM `'._DB_PREFIX_.'seller`';
$rows = Db::getInstance()->executeS($sql);

$first_day_of_previous_month = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1, date("Y"))).' 00:00:00';
$last_day_of_previous_month = date("Y-m-d", mktime(0, 0, 0, date("m"), 0, date("Y" ))).' 23:59:59';

$ctx = Context::getContext();
foreach ($rows as $row) {
    $seller = new Seller((int)$row['id_seller']);
    
    $s_i = new SellerInvoice();
    $s_i->id_seller = $seller->id;
    $s_i->start_date = $first_day_of_previous_month;
    $s_i->end_date = $last_day_of_previous_month;
    if ($s_i->isGenerated())
        continue;

    $count = Db::getInstance()->getValue(
        'SELECT count(*) FROM `'._DB_PREFIX_.'seller_invoice_data`
        WHERE (date BETWEEN \''.$first_day_of_previous_month.'\' AND \''.$last_day_of_previous_month.'\') AND `id_seller` = '.$seller->id
    );

    if($count > 0) {
        $object =array(
            'id_seller' => $seller->id,
            'start_date' => $first_day_of_previous_month,
            'end_date' => $last_day_of_previous_month,
            'month_summary' => true
        );
        // save as local file
        $pdf = new PDF(array($object), 'SalesReport', $ctx->smarty);
        $pdf->render('F');

        // send as email attachment
        $object['month_summary'] = false;
        $pdf = new PDF(array($object), 'SalesReport', $ctx->smarty);
        $file_attachement['content'] = $pdf->render(false);
        $file_attachement['name'] = $pdf->filename;
        $file_attachement['mime'] = 'application/pdf';
        
        // save info to database
        $s_i->filename = $pdf->filename;
        $s_i->generated_date = date("Y-m-d H:i:s");
        $s_i->save();

        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        
        $customer = new Customer($seller->id_customer);
        $mail_params = array(
            '{lastname}' => $customer->lastname,
            '{firstname}' => $customer->firstname,
            '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
            '{shop_url}' => Tools::getHttpHost(true).__PS_BASE_URI__,
        );
        Mail::Send($id_lang,
            'sales_report',
            Mail::l('Monthly sales report'),
            $mail_params,
            $seller->email,
            $seller->name,
            strval(Configuration::get('PS_SHOP_EMAIL')),
            strval(Configuration::get('PS_SHOP_NAME')),
            $file_attachement,
            null,
            _PS_MODULE_DIR_.'/npsprzelewy24/mails/');
    }
}

exit(0);
