<?php

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/npsmarketplace.php');
$context = Context::getContext();
$marketplace = new NpsMarketplace();
echo json_encode($marketplace->hookIframe(array('cookie' => $context->cookie, 'cart' => $context->cart)));