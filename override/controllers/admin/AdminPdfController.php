<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class AdminPdfController extends AdminPdfControllerCore {

    public function processGenerateSalesReportPDF() {
        if (Tools::isSubmit('id_seller') && Tools::isSubmit('start') && Tools::isSubmit('end'))
            $this->generateSalesReportPDF(Tools::getValue('id_seller'), Tools::getValue('start'), Tools::getValue('end'));
        else
            die (Tools::displayError('The seller ID or time period is missing.'));
    }

    private function generateSalesReportPDF($id_seller, $start, $end) {
        require_once _PS_MODULE_DIR_.'npsprzelewy24/classes/HTMLTemplateSalesReport.php';
        $this->generatePDF(array(array(
            'id_seller' => $id_seller,
            'start_date' => $start.' 00:00:00',
            'end_date' => $end.' 23:59:59'
        )), 'SalesReport');
    }
}
