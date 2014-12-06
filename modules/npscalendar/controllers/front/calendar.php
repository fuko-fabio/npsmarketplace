<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsCalendarCalendarModuleFrontController extends ModuleFrontController {
    
    public $display_column_left = true;
    public $page_name = 'events-calendar';

    public function __construct() {
        parent::__construct();

        $this->display_column_left = true;
    }

    public function setMedia() {
        parent::setMedia();
        $this->addCSS(_PS_THEME_DIR_.'css/modules/npscalendar/npscalendar.css');
    }

    public function initContent() {
        parent::initContent();

        $this->productSort();
        $nbProducts = Product::getPricesDrop($this->context->language->id, null, null, true);
        $this->pagination($nbProducts);

        $products = Product::getPricesDrop($this->context->language->id, (int)$this->p - 1, (int)$this->n, false, $this->orderBy, $this->orderWay);
        $this->addColorsToProductList($products);

        $this->context->smarty->assign(array(
            'products' => $products,
            'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'nbProducts' => $nbProducts,
            'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
            'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM'),
            'HOOK_LEFT_COLUMN' => $this->displayCalendar()
        ));

        $this->setTemplate('calendar_page.tpl');
    }
    
    private function displayCalendar() {
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.'npscalendar/views/templates/front/calendar_left.tpl');
    }
}