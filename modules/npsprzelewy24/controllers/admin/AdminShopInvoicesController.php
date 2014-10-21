<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/ShopInvoice.php');
require_once(_PS_MODULE_DIR_.'npsprzelewy24/classes/SellerReportDataCollector.php');
require_once _PS_MODULE_DIR_.'npsprzelewy24/classes/HTMLTemplateShopSalesReport.php';
require_once(_PS_MODULE_DIR_.'npsmarketplace/classes/Seller.php');

class AdminShopInvoicesController extends AdminController {

    protected $_defaultOrderBy = 'generated_date';
    protected $_defaultOrderWay = 'DESC';

    public function __construct() {
        $this->bootstrap = true;
        $this->required_database = true;
        $this->table = 'shop_invoice';
        $this->className = 'ShopInvoice';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->allow_export = false;
        $this->base_tpl_view = 'generate_report.tpl';

        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;
        
        $this->fields_list = array(
            'id_shop_invoice' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'start_date' => array(
                'title' => $this->l('From Date'),
                    'type' => 'date',
                    'align' => 'text-right'
            ),
            'end_date' => array(
                'title' => $this->l('To Date'),
                'type' => 'date',
                'align' => 'text-right'
            ),
            'generated_date' => array(
                'title' => $this->l('Generated on'),
                'type' => 'date',
                'align' => 'text-right'
            ),
            'filename' => array(
                'title' => $this->l('PDF'),
                'align' => 'text-center',
                'callback' => 'printPDFIcons',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true
            )
        );

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_CUSTOMER;

        parent::__construct();
    }

    public function printPDFIcons($filename, $tr) {
        return
        '<span class="btn-group-action">
            <span class="btn-group">
                <a class="btn btn-default" target="_blank" href="'._NPS_REPORTS_DIR_.$filename.'"><i class="icon-file-text"></i></a>
            </span>
        </span>';
    }

    public function initProcess() {
        parent::initProcess();

        if (Tools::isSubmit('generateShopReport')) {
            if ($this->tabAccess['edit'] === '1')
                $this->action = 'generate_shop_report';
            else
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
        }
    }

    public function processGenerateShopReport() {
        if (Tools::isSubmit('start') && Tools::isSubmit('end')) {
            $start = Tools::getValue('start').' 00:00:00';
            $end =  Tools::getValue('end').' 23:59:59';
            
            $sql = 'SELECT `id_seller` FROM `'._DB_PREFIX_.'seller`';
            $rows = Db::getInstance()->executeS($sql);
            $summary_report_data = array();
            
            foreach ($rows as $row) {
                $seller = new Seller((int)$row['id_seller']);
                $collector = new SellerReportDataCollector($seller, $start, $end);
                $report_data = $collector->collect();
                $summary_report_data[] = $report_data;
            }
        
            $pdf = new PDF(array(array(
                    'report_data' => $summary_report_data,
                    'month_summary' => false
                )), 'ShopSalesReport', $this->context->smarty);
            $pdf->render();
        }
    }

    public function initPageHeaderToolbar() {
        parent::initPageHeaderToolbar();

        if ($this->display != 'edit' && $this->display != 'add' && $this->display != 'view') {

            $this->page_header_toolbar_btn['generate_report'] = array(
                'href' => self::$currentIndex.'&viewshop_invoice&token='.$this->token,
                'desc' => $this->l('Generate Report'),
                'icon' => 'process-icon-cogs'
            );
        }
    }

    public function renderView() {
        $this->tpl_view_vars = array(
            'generate_url' => self::$currentIndex.'&generateShopReport&token='.$this->token
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
}