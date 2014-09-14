<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(_PS_MODULE_DIR_.'npscalendar/npscalendar.php');

class MonthName {
    
    public static function t($month_number) {
        $m = new NpsCalendar();
        $month = array(
            '01' => $m->l('January'),
            '02' => $m->l('February'),
            '03' => $m->l('March'),
            '04' => $m->l('April'),
            '05' => $m->l('May'),
            '06' => $m->l('Juli'),
            '07' => $m->l('Julay'),
            '08' => $m->l('August'),
            '09' => $m->l('September'),
            '10' => $m->l('October'),
            '11' => $m->l('November'),
            '12' => $m->l('December'),
        );
        return $month[$month_number];
    }

}