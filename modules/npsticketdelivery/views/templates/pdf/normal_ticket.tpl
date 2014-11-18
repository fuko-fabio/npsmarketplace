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
                        <div style="font-weight:700;font-size:22px;">{$name}</div>
                        <br />
                        <div style="width:100px;text-transform: uppercase;float: left;display: inline-block;">{l s='Term' pdf='true'}</div>: <span style="font-weight: 700;float: left">{$date}</span><br />
                        <br />
                        <div style="width:100px;text-transform: uppercase;float: left;display: inline-block;">{l s='Person' pdf='true'}</div>: <span style="float: left">{$person}</span><br />
                        <div style="width:100px;text-transform: uppercase;float: left;display: inline-block;">{l s='Address' pdf='true'}</div>: <span style="float: left">{$address}</span><br />
                        <div style="width:100px;text-transform: uppercase;float: left;display: inline-block;">{l s='District' pdf='true'}</div>: <span style="float: left">{$district}</span><br />
                        <div style="width:100px;text-transform: uppercase;float: left;display: inline-block;">{l s='Town' pdf='true'}</div>: <span style="float: left">{$town}</span><br />
                    </div>
                    <br />
                    {if $gift}
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;">{l s='Gift' pdf='true'}<br />{l s='Ticket' pdf='true'}</div>
                    {else}
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;">{l s='Price' pdf='true'}<br />{displayPrice price=$price currency=$id_currency}{if $tax}<br />({$tax}% {l s='tax incl.' pdf='true'}){/if}</div>
                    {/if}
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;border-left: 1px solid black;">{l s='Seller' pdf='true'}<br />{$id_seller}</div>
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;border-left: 1px solid black;">{l s='NO' pdf='true'}<br />{$id_ticket}</div>
                    <div style="display: inline-block;width:120px;float: left;text-transform: uppercase;text-align: center;border-left: 1px solid black;">{l s='Generated' pdf='true'}<br />{$generated}</div>
                </td>
            </tr>
        </table>
        <div style="transform: rotate(-90deg);font-weight:700;position: absolute;top:239px;left: 635px">
            <div style="width: 235px;text-align: center;background: gray;color: white;padding: 10px 5px;font-size: 18px;text-transform: uppercase;">{l s='Buy tickets at' pdf='true'}</div>
            <div style="width: 235px;text-align: center;background: black;color: white;padding: 10px 5px;font-size: 22px;">labsintown.pl</div>
        </div>
  </body>
</html>