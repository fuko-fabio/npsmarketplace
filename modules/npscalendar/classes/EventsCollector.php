<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npscalendar/npscalendar.php');
include_once(_PS_MODULE_DIR_.'npscalendar/classes/CalendarItem.php');

class EventsCollector {

    public function getEvents($start_date = null, $end_date = null) {
        $module = new NpsCalendar();
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
            'title' => $module->l('Check calendar'),
            'no_events' => $module->l('No events'),
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
            $day_name = CalendarItem::day(date('w', strtotime($date)));
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
        $bm = CalendarItem::month(date("m", $begin->getTimestamp()));
        $em = CalendarItem::month(date("m", strtotime("-1 day", $end->getTimestamp())));
        return $bm == $em ? $bm : $bm.'/'.$em;
    }

    private function searchForDay($day, $link) {
        $events = array();
        $max_search_events = Configuration::get('NPS_EVENTS_SEARCH');
        $res = Search::find(Context::getContext()->language->id, $day, 1, $max_search_events);
        if (empty($res))
            return $events;
        $max_events = Configuration::get('NPS_EVENTS_PER_DAY');
        if ($res['total'] > $max_events) {
            $indexes = array_rand($res['result'], $max_events);
            foreach ($indexes as $index) {
                $events[] = $this->buildCalendarEvent($res['result'][$index], $link);
            }
        } else {
            foreach ($res['result'] as $product) {
                $events[] = $this->buildCalendarEvent($product, $link);
            }
        }
        return $this->sortByTime($events);
    }

    private function buildCalendarEvent($product, $link) {
        $image = '';
        if (!empty($product['id_image']))
            $image = $link->getImageLink($product['link_rewrite'], $product['id_image'], 'cart_default');
        return array(
            'name' => $product['name'],
            'time' => $this->getTime($product),
            'link' => $product['link'],
            'image' => $image,
            'all' => $product,
        );
    }

    private function getTime($product) {
        $combination = new Combination($product['id_product_attribute']);
        $attrs = $combination->getAttributesName(Configuration::get('PS_LANG_DEFAULT'));
        foreach($attrs as $attr)
            if (preg_match('/^[0-9]{1,2}:[0-9]{2,2}$/', $attr['name']))
                return $attr['name'];
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
}
