<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*/

class Validate extends ValidateCore
{
     /**
     * Check for NIP number validity
     *
     * @param string $name Name to validate
     * @return boolean Validity is ok or not
     */
    public static function isNip($nip)
    {
        return preg_match('/^[a-zA-Z0-9_.-]+$/', $nip);
    }

    /**
     * Check for REGON number validity
     *
     * @param string $name Name to validate
     * @return boolean Validity is ok or not
     */
    public static function isRegon($regon)
    {
        return preg_match('/^[0-9]{9}$/', $regon);
    }
    
    /* Check for time validity  HH:MM
     *
     * @param string $time time to validate
     * @return boolean Validity is ok or not
     */
    public static function isTime($time)
    {
        return preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/' ,$time);
    }

    function isNrb($nrb)
    {
        if (strlen($nrb)!=26)
            return 0;
        $w = array(1,10,3,30,9,90,27,76,81,34,49,5,50,15,53,45,62,38,89,17,73,51,25,56,75,71,31,19,93,57);

        $nrb .= "2521";
        $nrb = substr($nrb,2).substr($nrb,0,2);
        $z =0;
        for ($i=0;$i<30;$i++)
            $z += $nrb[29-$i] * $w[$i];
        if ($z % 97 == 1)
            return 1;
        else
            return 0;
    }
}
