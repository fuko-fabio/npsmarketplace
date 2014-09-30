<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npscalendar/npscalendar.php');

class CalendarItem {

    public static function month($month_number) {
        $m = new NpsCalendar();
        $month = array(
            '01' => $m->l('January'),
            '02' => $m->l('February'),
            '03' => $m->l('March'),
            '04' => $m->l('April'),
            '05' => $m->l('May'),
            '06' => $m->l('Juni'),
            '07' => $m->l('July'),
            '08' => $m->l('August'),
            '09' => $m->l('September'),
            '10' => $m->l('October'),
            '11' => $m->l('November'),
            '12' => $m->l('December'),
        );
        return $month[$month_number];
    }

    public static function day($day_number) {
        $m = new NpsCalendar();
        $days = array(
            '0' => $m->l('Sunday'),
            '1' => $m->l('Monday'),
            '2' => $m->l('Tuesday'),
            '3' => $m->l('Wednesday'),
            '4' => $m->l('Thursday'),
            '5' => $m->l('Friday'),
            '6' => $m->l('Saturday'),
        );
        return $days[$day_number];
    }

}