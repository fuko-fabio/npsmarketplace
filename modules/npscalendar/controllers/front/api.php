<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/
require_once(_PS_MODULE_DIR_.'npsmarketplace/npsmarketplace.php');

class NpsCalendarApiModuleFrontController extends ModuleFrontController {

    public $ssl = true;

    public function initContent() {
        parent::initContent();
        
        $events = $this->getEvents(Tools::getValue('start_date'), Tools::getValue('end_date'));

        die(Tools::jsonEncode($events));
    }

    public function getEvents($start_date = null, $end_date = null) {
        if ($start_date == null && $end_date == null) {
            $begin = new DateTime();
            $begin->setTimestamp(time());
        } else if ($start_date == null && $end_date != null) {
            $begin = new DateTime($end_date);
            $begin->modify('-1 week');
        } else {
            $begin = new DateTime($start_date);
        }

        if ($end_date == null) {
            $end = new DateTime();
            $timestamp =  strtotime("+1 week", $begin->getTimestamp());
            $end->setTimestamp($timestamp);
        } else
            $end = new DateTime($end_date);
        $interval = DateInterval::createFromDateString('1 day');
        $days = new DatePeriod($begin, $interval, $end);

        $result =array(
            'title' => $this->module->l('Check calendar', 'api'),
            'no_events' => $this->module->l('No events', 'api'),
            'month' => $this->getDisplayMonth($begin, $end),
            'year' => $this->getDisplayYear($begin, $end),
            'start_date' => date('Y-m-d', $begin->getTimestamp()),
            'end_date' => date('Y-m-d', $end->getTimestamp()),
            'days' => array()
        );
        $link = new Link();
        foreach ( $days as $day ) {
            $date = $day->format('Y-m-d');
            $events = $this->searchForDay($date, $link);
            $day_name = $this->day(date('w', strtotime($date)));
            $day_number = date('j', strtotime($date));
            $result['days'][] = array(
                'day' => $day_number,
                'name' => $day_name,
                'events' => $events
            );
        }
        return $result;
    }

    private function getDisplayYear($begin, $end) {
        $by = date("Y", $begin->getTimestamp());
        $ey = date("Y", strtotime("-1 day", $end->getTimestamp()));
        return $by == $ey ? $by : $by.'/'.$ey;
    }

    private function getDisplayMonth($begin, $end) {
        $bm = $this->month(date("m", $begin->getTimestamp()));
        $em = $this->month(date("m", strtotime("-1 day", $end->getTimestamp())));
        return $bm == $em ? $bm : $bm.'/'.$em;
    }

    private function searchForDay($day, $link) {
        $events = array();
        $max_search_events = Configuration::get('NPS_EVENTS_SEARCH');
        $res = Search::find(Context::getContext()->language->id, $day, 1, $max_search_events);
        if ($res['total'] == 0)
            return $events;
        $max_events = Configuration::get('NPS_EVENTS_PER_DAY');
        if ($res['total'] > $max_events) {
            $indexes = array_rand($res['result'] , $max_events);
            foreach ($indexes as $index) {
                $events[] = $this->buildCalendarEvent($res['result'][$index], $link, $day);
            }
        } else {
            foreach ($res['result'] as $product) {
                $events[] = $this->buildCalendarEvent($product, $link, $day);
            }
        }
        return $this->sortByTime($events);
    }

    private function buildCalendarEvent($product, $link, $day) {
        $combinations = Product::getStaticAttributeCombinations($product['id_product'], Context::getContext()->language->id);
        $id_product_attribute = $product['id_product_attribute'];
        foreach ($combinations as $key => $combination) {
            if ($combination['attribute_name'] == $day) {
                $id_product_attribute = $combination['id_product_attribute'];
                break;
            }
        }
        
        $image = '';
        if (!empty($product['id_image']))
            $image = $link->getImageLink($product['link_rewrite'], $product['id_image'], 'cart_default');
        return array(
            'name' => $product['name'],
            'time' => $this->getTime($combinations, $id_product_attribute),
            'link' => $link->getProductLink($product, null, null, null, null, null, $id_product_attribute),
            'image' => $image
        );
    }

    private function getTime($combinations, $id_product_attribute) {
        foreach($combinations as $key => $combination)
            if ($combination['id_product_attribute'] == $id_product_attribute
                && $combination['id_attribute_group'] == Configuration::get('NPS_ATTRIBUTE_TIME_ID'))
                return $combination['attribute_name'];
        return '';
    }

    private function sortByTime($events) {
        usort($events, function($a, $b) {
          $ad = strtotime($a['time']);
          $bd = strtotime($b['time']);

          if ($ad == $bd) {
            return 0;
          }

          return $ad > $bd ? 1 : -1;
        });
        return $events;
    }

    public function month($month_number) {
        $month = array(
            '01' => $this->module->l('January', 'api'),
            '02' => $this->module->l('February', 'api'),
            '03' => $this->module->l('March', 'api'),
            '04' => $this->module->l('April', 'api'),
            '05' => $this->module->l('May', 'api'),
            '06' => $this->module->l('Juni', 'api'),
            '07' => $this->module->l('July', 'api'),
            '08' => $this->module->l('August', 'api'),
            '09' => $this->module->l('September', 'api'),
            '10' => $this->module->l('October', 'api'),
            '11' => $this->module->l('November', 'api'),
            '12' => $this->module->l('December', 'api'),
        );
        return $month[$month_number];
    }

    public function day($day_number) {
        $days = array(
            '0' => $this->module->l('Sunday', 'api'),
            '1' => $this->module->l('Monday', 'api'),
            '2' => $this->module->l('Tuesday', 'api'),
            '3' => $this->module->l('Wednesday', 'api'),
            '4' => $this->module->l('Thursday', 'api'),
            '5' => $this->module->l('Friday', 'api'),
            '6' => $this->module->l('Saturday', 'api'),
        );
        return $days[$day_number];
    }
}