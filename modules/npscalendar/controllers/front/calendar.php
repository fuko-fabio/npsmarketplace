<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class NpsCalendarCalendarModuleFrontController extends ModuleFrontController {

    public $page_name = 'events-calendar';

    public function setMedia() {
        parent::setMedia();
        $this->addCSS(_PS_THEME_DIR_.'css/modules/npscalendar/npscalendar.css');
        $js_dir =  _PS_MODULE_DIR_.'npscalendar/js/';
        $this->addJS(array(
            $js_dir.'underscore-min.js',
            $js_dir.'backbone-min.js',
            $js_dir.'backbone-associations-min.js',
            $js_dir.'calendar/template/monthCalendar.js',
            $js_dir.'calendar/model/event.js',
            $js_dir.'calendar/collection/events.js',
            $js_dir.'calendar/model/day.js',
            $js_dir.'calendar/collection/days.js',
            $js_dir.'calendar/model/week.js',
            $js_dir.'calendar/collection/weeks.js',
            $js_dir.'calendar/model/month.js',
            $js_dir.'calendar/view/monthCalendar.js',
            $js_dir.'calendar/monthRouter.js',
        ));
    }

    public function initContent() {
        parent::initContent();

        if (Tools::isSubmit('date')) {
            $date = Tools::getValue('date');
        } else {
            $date = date('Y-m-d');
        }
        $this->productSort();
        $this->n = abs((int)(Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'))));
        $this->p = abs((int)(Tools::getValue('p', 1)));
        $_GET['n'] = $this->n;
        $_GET['p'] = $this->p;
        $search = Search::find($this->context->language->id, $date, $this->p, $this->n, $this->orderBy, $this->orderWay);
        $nbProducts = $search['total'];
        $this->pagination($nbProducts);

        $this->context->smarty->assign(array(
            'products' => $search['result'],
            'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
            'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM'),
            'calendar_api_url' => $this->context->link->getModuleLink('npscalendar', 'api'),
            'current_calendar_date' => $date,
            'HOOK_CALENDAR' => $this->displayCalendar(),
        ));

        $this->setTemplate('calendar_page.tpl');
    }
    
    private function displayCalendar() {
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.'npscalendar/views/templates/front/calendar_left.tpl');
    }
}