{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body style="font-family: verdana, sans-serif;">
        <div style="transform: rotate(-90deg);position: absolute;top:227px;left: 10px">
            {$barcode}
            <div style="display: block;font-weight:700;font-size: 16px;">{$code}</div>
        </div>
        <table>
            <tr>
                <td style="border: 1px dashed black;border-right: 1px solid black;padding: 0;width: 130px;height: 240px;">
        
                </td>
                <td style="font-size: 14px;border-top: 1px dashed black;border-bottom: 1px dashed black;width: 500px">
                    <div style="margin-left: 10px">
                        <div style="font-weight:700;text-transform: uppercase;position:absolute;top:5px;right:90px;padding:5px;border:1px solid black;background: white">
                            {if $type == 0}
                            Bilet
                            {else if $type == 1}
                            Karnet
                            {/if}
                        </div>
                        <div style="font-weight:700;font-size:22px;text-overflow: ellipsis;white-space: nowrap;">{$name|truncate:40}</div>
                        <br />
                        {if $type == 0 && isset($date)}
                        <div style="width:100px;text-transform: uppercase;float: left;display: inline-block;">Termin</div>: <span style="font-weight: 700;float: left">{date_format(date_create($date), 'Y-m-d H:i')}</span><br />
                        {else if $type == 1 && isset($entries) && $entries > 0}
                        <div style="width:100px;text-transform: uppercase;float: left;display: inline-block;">Ilość wejść</div>: <span style="font-weight: 700;float: left">{$entries}</span><br />
                        {else if $type == 1 && strtotime($from) > 0 && strtotime($to) > 0}
                        <div style="width:100px;text-transform: uppercase;float: left;display: inline-block;">Ważność</div>: <span style="font-weight: 700;float: left">{date_format(date_create($from), 'Y-m-d')} - {date_format(date_create($to), 'Y-m-d')}</span><br />
                        {/if}
                        <br />
                        <div style="width:100px;text-transform: uppercase;float: left;display: inline-block;">Osoba</div>: <span style="float: left;text-overflow: ellipsis;white-space: nowrap;">{$person|truncate:60}</span><br />
                        <div style="width:100px;text-transform: uppercase;float: left;display: inline-block;">Adres</div>: <span style="float: left;text-overflow: ellipsis;white-space: nowrap;">{$address|truncate:60}</span><br />
                        <div style="width:100px;text-transform: uppercase;float: left;display: inline-block;">Dzielnica</div>: <span style="float: left;text-overflow: ellipsis;white-space: nowrap;">{$district|truncate:60}</span><br />
                        <div style="width:100px;text-transform: uppercase;float: left;display: inline-block;">Miasto</div>: <span style="float: left;text-overflow: ellipsis;white-space: nowrap;">{$town|truncate:60}</span><br />
                    </div>
                    <br />
                    {if $gift}
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;">Prezent</div>
                    {else}
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;">Cena<br />{displayPrice price=$price currency=$id_currency}{if $tax}<br />({$tax}% {l s='tax incl.' mod='npsticketdelivery'}){/if}</div>
                    {/if}
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;border-left: 1px solid black;">{l s='SID' mod='npsticketdelivery'}<br />{$id_seller}</div>
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;border-left: 1px solid black;">{l s='NO' mod='npsticketdelivery'}<br />{$id_ticket}</div>
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;border-left: 1px solid black;">{l s='GEN' mod='npsticketdelivery'}<br />{$generated}</div>
                </td>
            </tr>
        </table>
        <div style="transform: rotate(-90deg);font-weight:700;position: absolute;top:239px;left: 635px">
            <div style="width: 235px;text-align: center;background: black;color: white;padding: 10px 5px;font-size: 18px;text-transform: uppercase;">{l s='Buy tickets at' mod='npsticketdelivery'}</div>
            <div style="width: 235px;text-align: center;background: #ffaa00;color: black;padding: 10px 5px;font-size: 22px;">labsintown.pl</div>
        </div>
  </body>
</html>