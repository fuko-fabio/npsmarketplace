{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body style="font-family: arial, DejaVu;">
        <div style="transform: rotate(-90deg);position: absolute;top:227px;left: 10px">
            {$barcode}
            <div style="display: block;font-weight:700;font-size: 16px;">{$code}</div>
        </div>
        <table style="position: absolute; left: 0px">
            <tr>
                <td><div style="border-left: 1px dashed black;border-right: 1px solid black;padding: 0;width: 130px;height: 240px;"></div></td>
                <td style="padding-left:130px; font-size: 14px;border-top: 1px dashed black;border-bottom: 1px dashed black;width: 500px">
                    <div style="margin-left: 10px">
                        <div style="font-weight:700;text-transform: uppercase;position:absolute;top:5px;right:10px;padding:5px;border:1px solid black;background: white">
                            {if $type == 0}
                                {l s='Ticket' mod='npsticketdelivery'}
                            {else if $type == 1}
                                {l s='Carnet' mod='npsticketdelivery'}
                            {/if}
                        </div>
                        <div style="font-weight:700;font-size:22px;text-overflow: ellipsis;white-space: nowrap;">{$name|truncate:40}</div>
                        {if isset($combination_name) && !empty($combination_name)}
                        <div style="font-size: 10px;width:100px;text-transform: uppercase;float: left;display: inline-block;">{l s='Combination' mod='npsticketdelivery'}</div>: <span style="float: left;text-overflow: ellipsis;white-space: nowrap;">{$combination_name|truncate:60}</span><br />
                        {/if}
                        {if $type == 0 && isset($date)}
                        <div style="font-size: 10px;width:100px;text-transform: uppercase;float: left;display: inline-block;">{l s='Term' mod='npsticketdelivery'}</div>: <span style="font-weight: 700;float: left">{date_format(date_create($date), 'Y-m-d H:i')}</span><br />
                        {/if}
                        <div style="font-size: 10px;width:100px;text-transform: uppercase;float: left;display: inline-block;">{l s='Person' mod='npsticketdelivery'}</div>: <span style="float: left;text-overflow: ellipsis;white-space: nowrap;">{$person|truncate:60}</span><br />
                        <div style="font-size: 10px;width:100px;text-transform: uppercase;float: left;display: inline-block;">{l s='Address' mod='npsticketdelivery'}</div>: <span style="float: left;text-overflow: ellipsis;white-space: nowrap;">{$address|truncate:60}</span><br />
                        {if isset($district) && !empty($district)}
                        <div style="font-size: 10px;width:100px;text-transform: uppercase;float: left;display: inline-block;">{l s='District' mod='npsticketdelivery'}</div>: <span style="float: left;text-overflow: ellipsis;white-space: nowrap;">{$district|truncate:60}</span><br />
                        {/if}
                        {if isset($town) && !empty($town)}
                        <div style="font-size: 10px;width:100px;text-transform: uppercase;float: left;display: inline-block;">{l s='Town' mod='npsticketdelivery'}</div>: <span style="float: left;text-overflow: ellipsis;white-space: nowrap;">{$town|truncate:60}</span><br />
                        {/if}
                        {if isset($seller_name) && !empty($seller_name)}
                        <div style="font-size: 10px;width:100px;text-transform: uppercase;float: left;display: inline-block;">{l s='Seller' mod='npsticketdelivery'}</div>: <span style="float: left;text-overflow: ellipsis;white-space: nowrap;">{$seller_name|truncate:60}</span><br />
                        {/if}
                    </div>
                    <br />
                    {if $gift}
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;font-size: 10px;">{l s='Gift' mod='npsticketdelivery'}</div>
                    {else}
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;font-size: 10px;">{l s='Price' mod='npsticketdelivery'}<br />{displayPrice price=$price currency=$id_currency}{if $tax}<br />({$tax}% {l s='tax incl.' mod='npsticketdelivery'}){/if}</div>
                    {/if}
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;border-left: 1px solid black;font-size: 10px;">{l s='SID' mod='npsticketdelivery'}<br />{$id_seller}</div>
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;border-left: 1px solid black;font-size: 10px;">{l s='NO' mod='npsticketdelivery'}<br />{$id_ticket}</div>
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;border-left: 1px solid black;font-size: 10px;">{l s='GEN' mod='npsticketdelivery'}<br />{$generated}</div>
                </td>
            </tr>
        </table>
        <div style="transform: rotate(-90deg);font-weight:700;position: absolute;top:239px;left: 635px">
            <div style="width: 235px;text-align: center;background: black;color: white;padding: 10px 5px;font-size: 18px;text-transform: uppercase;">{l s='Buy tickets at' mod='npsticketdelivery'}</div>
            <div style="width: 235px;text-align: center;background: #ffaa00;color: black;padding: 10px 5px;font-size: 22px;">{l s='labsintown.pl' mod='npsticketdelivery'}</div>
        </div>
  </body>
</html>