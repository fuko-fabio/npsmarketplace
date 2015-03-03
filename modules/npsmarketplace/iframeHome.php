<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');
include_once(dirname(__FILE__).'/npsmarketplace.php');

$marketplace = new NpsMarketplace();
echo $marketplace->hookIframeHome();