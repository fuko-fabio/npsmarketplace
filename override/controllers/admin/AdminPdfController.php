<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
include_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class AdminPdfController extends AdminPdfControllerCore {

    const TEMPLATE_SALES_REPORT = 'SalesReport';

    public function processGenerateSalesReportPDF() {
        if (Tools::isSubmit('id_seller') && Tools::isSubmit('start') && Tools::isSubmit('end'))
            $this->generateSalesReportPDF(Tools::getValue('id_seller'), Tools::getValue('start'), Tools::getValue('end'));
        else
            die (Tools::displayError('The seller ID or time period is missing.'));
    }

    private function generateSalesReportPDF($id_seller, $start, $end) {
        $seller = new Seller($id_seller);
        $this->generatePDF(array(
            'seller' => $seller,
            'start_date' => $start,
            'end_date' => $end
        ), self::TEMPLATE_SALES_REPORT);
    }
}
