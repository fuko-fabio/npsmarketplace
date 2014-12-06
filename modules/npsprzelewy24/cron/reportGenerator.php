<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include(dirname(__FILE__).'/../../../config/config.inc.php');
include dirname(__FILE__).'/../../../init.php';
require_once _PS_MODULE_DIR_.'npsprzelewy24/classes/HTMLTemplateSellerSalesReport.php';
require_once _PS_MODULE_DIR_.'npsprzelewy24/classes/HTMLTemplateShopSalesReport.php';
require_once _PS_MODULE_DIR_.'npsprzelewy24/classes/SellerInvoice.php';
require_once _PS_MODULE_DIR_.'npsprzelewy24/classes/ShopInvoice.php';
require_once _PS_MODULE_DIR_.'npsprzelewy24/classes/SellerReportDataCollector.php';
require_once _PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php';

if ( !defined( '_NPS_REPORTS_DIR_' ) )
    define('_NPS_REPORTS_DIR_', '/sales_reports/');
if ( !defined( '_NPS_SELLER_REPORTS_DIR_' ) )
    define('_NPS_SELLER_REPORTS_DIR_', _NPS_REPORTS_DIR_.'sellers/');

$token = Tools::getValue('token');

if ($token != '733acb9920b35800545d7d3e9c2e9e21')
    exit(1);

$sql = 'SELECT `id_seller` FROM `'._DB_PREFIX_.'seller`';
$rows = Db::getInstance()->executeS($sql);

$first_day_of_previous_month = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1, date("Y"))).' 00:00:00';
$last_day_of_previous_month = date("Y-m-d", mktime(0, 0, 0, date("m"), 0, date("Y"))).' 23:59:59';

$ctx = Context::getContext();
$summary_report_data = array();

foreach ($rows as $row) {
    $seller = new Seller((int)$row['id_seller']);
    $collector = new SellerReportDataCollector($seller, $first_day_of_previous_month, $last_day_of_previous_month);
    $report_data = $collector->collect();
    $summary_report_data[] = $report_data;

    $s_i = new SellerInvoice();
    $s_i->id_seller = $seller->id;
    $s_i->start_date = $first_day_of_previous_month;
    $s_i->end_date = $last_day_of_previous_month;
    if ($s_i->isGenerated())
        continue;
        
    $object =array(
        'seller' => $seller,
        'report_data' => $report_data,
        'month_summary' => true
    );
    // save as local file
    $pdf = new PDF(array($object), 'SellerSalesReport', $ctx->smarty);
    $pdf->render('F');
                
    // save info to database
    $parts = explode('/', $pdf->filename);
    $s_i->filename = end($parts);
    $s_i->generated_date = date("Y-m-d H:i:s");
    $s_i->empty = $report_data['empty'];
    $s_i->save();

    if(!$report_data['empty']) {
        // send as email attachment
        $object['month_summary'] = false;
        $pdf = new PDF(array($object), 'SellerSalesReport', $ctx->smarty);
        $file_attachement['content'] = $pdf->render(false);
        $file_attachement['name'] = $pdf->filename;
        $file_attachement['mime'] = 'application/pdf';

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
            _PS_MODULE_DIR_.'npsprzelewy24/mails/');
    }
}

if(count($summary_report_data) > 0) {
    $s_i = new ShopInvoice();
    $s_i->start_date = $first_day_of_previous_month;
    $s_i->end_date = $last_day_of_previous_month;
    if (!$s_i->isGenerated()) {
        // save as local file
        $pdf = new PDF(array(array(
                'report_data' => $summary_report_data,
                'month_summary' => true
            )), 'ShopSalesReport', $ctx->smarty);
        $pdf->render('F');
        $parts = explode('/', $pdf->filename);
        $s_i->filename = end($parts);
        $s_i->generated_date = date("Y-m-d H:i:s");
        $s_i->save();
    }
}

exit(0);
