<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npscalendar/npscalendar.php');
include_once(_PS_MODULE_DIR_.'npscalendar/classes/MonthName.php');

class EventsCollector {

    public function getEvents($start_date, $end_date = null) {
        $module = new NpsCalendar();
        $begin = new DateTime($start_date);
        if ($end_date == null) {
            $end = new DateTime();
            $timestamp =  strtotime("+1 week", strtotime($start_date));
            $end->setTimestamp($timestamp);
        } else
            $end = new DateTime($end_date);
        $interval = DateInterval::createFromDateString('1 day');
        $days = new DatePeriod($begin, $interval, $end);

        $result =array(
            'title' => $module->l('Check calendar'),
            'no_events' => $module->l('No events'),
            'month' => MonthName::t(date("m", $begin->getTimestamp())),
            'year' => date("Y", $begin->getTimestamp()),
            'days' => array()
        );
        foreach ( $days as $day ) {
            $date = $day->format('Y-m-d');
            $events = $this->searchForDay($date);
            $day_name = date('l', strtotime($date));
            $day_number = date('j', strtotime($date));
            $result['days'][] = array(
                'day' => $day_number,
                'name' => $day_name,
                'events' => $events
            );
        }
        return $result;
    }

    public function searchForDay($day) {
        $events = array();
        $res = Search::find(Configuration::get('PS_LANG_DEFAULT'), $day);
        if (empty($res))
            return $events;
        if ($res['total'] > Configuration::get('NPS_EVENTS_PER_DAY')) {
            
        } else {
            foreach ($res['result'] as $product) {
                $events[] = $this->buildCalendarEvent($product);
            }
        }
        return $events;
    }

    public function buildCalendarEvent($product) {
        return array(
            'name' => $product['name'],
            'time' => '18:00',// TODO
            'link' => $product['link']
        );
    }

    public function mocked() {
        return array(
            'title' => 'Sprawdź kalendarz',
            'no_events' => 'Brak wydarzeń',
            'month' => 'Wrzesień',
            'year' => 2014,
            'days' => array(
                array(
                    'day' => 1,
                    'name' => 'Poniedziałek',
                    'events' => array(
                        array(
                            'name' => 'Joga',
                            'time' => '18:00'
                        ),
                        array(
                            'name' => 'Workshop',
                            'time' => '19:00'
                        )
                    )
                ),
                array(
                    'day' => 2,
                    'name' => 'Wtorek',
                    'events' => array(
                        array(
                            'name' => 'Workshop',
                            'time' => '15:00'
                        )
                    )
                ),
                array(
                    'day' => 3,
                    'name' => 'Środa',
                    'events' => array(
                        array(
                            'name' => 'Pool Dance',
                            'time' => '20:00'
                        ),
                        array(
                            'name' => 'Gym',
                            'time' => '21:00'
                        ),
                        array(
                            'name' => 'Night run',
                            'time' => '23:00'
                        )
                    )
                ),
                array(
                    'day' => 4,
                    'name' => 'Czwartek',
                    'events' => array()
                ),
                array(
                    'day' => 5,
                    'name' => 'Piątek',
                    'events' => array(
                        array(
                            'name' => 'Workshop',
                            'time' => '12:00'
                        ),
                        array(
                            'name' => 'Gym',
                            'time' => '16:00'
                        ),
                        array(
                            'name' => 'Pool Dance',
                            'time' => '23:00'
                        )
                    )
                ),
                array(
                    'day' => 6,
                    'name' => 'Sobota',
                    'events' => array(
                        array(
                            'name' => 'Workshop',
                            'time' => '11:00'
                        )
                    )
                ),
                array(
                    'day' => 7,
                    'name' => 'Niedziela',
                    'events' => array(
                        array(
                            'name' => 'Workshop',
                            'time' => '12:00'
                        ),
                        array(
                            'name' => 'Joga',
                            'time' => '16:00'
                        ),
                        array(
                            'name' => 'Gym',
                            'time' => '19:00'
                        ),
                        array(
                            'name' => 'Pool Dance',
                            'time' => '23:00'
                        ),
                        array(
                            'name' => 'Night run',
                            'time' => '23:00'
                        )
                    )
                )
            )
        );
    }
}
