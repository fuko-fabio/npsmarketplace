<?php
/*
*  @author Norbert Pabian
*  @copyright  
*  @license    
*/

class AdminStatsController extends AdminStatsControllerCore
{
    public function displayAjaxGetKpi()
    {
        d('dupa');
        switch (Tools::getValue('kpi'))
        {
            case 'registered_sellers':
                $value = AdminStatsController::getTotalSellers();
                ConfigurationKPI::updateValue('REGISTERED_SELLER_ACCOUNTS', $value);
                ConfigurationKPI::updateValue('REGISTERED_SELLER_ACCOUNTS_EXPIRE', strtotime('+2 min'));
                break;
            case 'active_sellers':
                $value = AdminStatsController::getActiveSellers();
                ConfigurationKPI::updateValue('ACTIVE_SELLER_ACCOUNTS', $value);
                ConfigurationKPI::updateValue('ACTIVE_SELLER_ACCOUNTS_EXPIRE', strtotime('+2 min'));
                break;
            case 'disabled_sellers':
                $value = AdminStatsController::getDisabledSellers();
                ConfigurationKPI::updateValue('DISABLED_SELLER_ACCOUNTS', $value);
                ConfigurationKPI::updateValue('DISABLED_SELLER_ACCOUNTS_EXPIRE', strtotime('+2 min'));
                break;
            case 'locked_sellers':
                $value = AdminStatsController::getLockedSellers();
                ConfigurationKPI::updateValue('LOCKED_SELLER_ACCOUNTS', $value);
                ConfigurationKPI::updateValue('LOCKED_SELLER_ACCOUNTS_EXPIRE', strtotime('+2 min'));
                break;
            default:
                $value = false;
        }
        if ($value !== false) {
            $array = array('value' => $value);
            if (isset($data))
                $array['data'] = $data;
            die(Tools::jsonEncode($array));
            die(Tools::jsonEncode(array('has_errors' => true)));
        } else {
            parent::displayAjaxGetKpi();
        }
    }

    public static function getLockedSellers()
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
        SELECT COUNT(*)
        FROM `'._DB_PREFIX_.'seller` s
        WHERE s.locked = 1');
    }

    public static function getActiveSellers()
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
        SELECT COUNT(*)
        FROM `'._DB_PREFIX_.'seller` s
        WHERE s.active = 0');
    }

    public static function getDisabledSellers()
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
        SELECT COUNT(*)
        FROM `'._DB_PREFIX_.'seller` s
        WHERE s.active = 1');
    }

    public static function getTotalSellers()
    {
        return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
        SELECT COUNT(*)
        FROM `'._DB_PREFIX_.'seller` s');
    }
}
?>