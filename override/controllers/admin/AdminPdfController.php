<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class AdminPdfController extends AdminPdfControllerCore {

    public function processGenerateSalesReportPDF() {
        if (Tools::isSubmit('id_seller') && Tools::isSubmit('start') && Tools::isSubmit('end'))
            $this->generateSellerSalesReportPDF(Tools::getValue('id_seller'), Tools::getValue('start'), Tools::getValue('end'));
        else
            die (Tools::displayError('The seller ID or time period is missing.'));
    }

    private function generateSellerSalesReportPDF($id_seller, $start, $end) {
        require_once _PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php';
        require_once _PS_MODULE_DIR_.'npsprzelewy24/classes/HTMLTemplateSellerSalesReport.php';
        require_once _PS_MODULE_DIR_.'npsprzelewy24/classes/SellerReportDataCollector.php';
        $seller = new Seller($id_seller);
        $collector = new SellerReportDataCollector($seller, $start.' 00:00:00', $end.' 23:59:59');
        $report_data = $collector->collect();
        $this->generatePDF(array(array(
            'seller' => $seller,
            'report_data' => $report_data,
            'month_summary' => false
        )), 'SellerSalesReport');
    }
}
