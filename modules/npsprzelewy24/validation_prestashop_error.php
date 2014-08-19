<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/przelewy24.php');
require_once(dirname(__FILE__).'/../../init.php');

$p24_order_id = strip_tags($_POST["p24_order_id"]);
$i_id_order = array_shift(explode('|', strip_tags($_POST["p24_session_id"])));
$o_przelewy24 = new Przelewy24();

$order_id = Order::getOrderByCartId($i_id_order);
$history = new OrderHistory();
$history->id_order = intval($order_id);
$history->changeIdOrderState(8, intval($order_id));
$history->addWithemail(true);

Tools::redirect('order-confirmation.php?orderid='.$i_id_order);
