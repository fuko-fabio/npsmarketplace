<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include(dirname(__FILE__).'/../../../config/config.inc.php');
include dirname(__FILE__).'/../../../init.php';
include(_PS_MODULE_DIR_.'npscalendar/classes/EventsCollector.php');

$collector = new EventsCollector();

$events = $collector->getEvents(Tools::getValue('start_date'), Tools::getValue('end_date'));
header('Content-Type: application/json');
echo json_encode($events);