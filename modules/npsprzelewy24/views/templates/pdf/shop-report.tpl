{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<div style="font-size: 8pt; color: #444">

<table>
    <tr><td>&nbsp;</td></tr>
</table>


<!-- SUMMARY TAB -->
<table style="width: 100%; font-size: 8pt;">
    <tr style="line-height:4px;">
        <td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: 10%">{l s='Seller ID' pdf='true'}</td>
        <td style="background-color: #4D4D4D; color: #FFF; text-align: left; font-weight: bold; width: 30%">{l s='Company Name' pdf='true'}</td>
        <td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 20%">{l s='Seller Turnover' pdf='true'}</td>
        <td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 20%">{l s='Seller Revenue' pdf='true'}</td>
        <td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 20%">{l s='Shop Revenue' pdf='true'}</td>
    </tr>
    <!-- SELLERS -->
    {foreach $items as $item}
    {cycle values='#FFF,#DDD' assign=bgcolor}
    <tr style="line-height:6px;background-color:{$bgcolor};">
        <td style="text-align: left; width: 10%">{$item.id_seller}</td>
        <td style="text-align: left; width: 30%">{$item.company_name}</td>
        <td style="text-align: right; width: 20%; white-space: nowrap;">
            {displayPrice currency=$id_currency price=$item.total}
        </td>
        <td style="text-align: right; width: 20%; white-space: nowrap;">
            {displayPrice currency=$id_currency price=$item.total_seller}
        </td>
        <td style="text-align: right; width: 20%; white-space: nowrap;">
            {displayPrice currency=$id_currency price=$item.total_commission}
        </td>
    </tr>
    {/foreach}
    <!-- END SELLERS --> 
    <hr />
    <table style="width: 100%">
        <tr style="line-height:5px;">
            <td style="width: 83%; text-align: right; font-weight: bold">{l s='Total sellers turnover' pdf='true'}</td>
            <td style="width: 17%; text-align: right;">{displayPrice currency=$id_currency price=$total}</td>
        </tr>
        <tr style="line-height:5px;">
            <td style="width: 83%; text-align: right; font-weight: bold">{l s='Total sellers revenue' pdf='true'}</td>
            <td style="width: 17%; text-align: right;">{displayPrice currency=$id_currency price=$total_seller}</td>
        </tr>
        <hr />
        <tr style="line-height:5px;">
            <td style="width: 83%; text-align: right; font-weight: bold">{l s='Total shop revenue' pdf='true'}</td>
            <td style="width: 17%; text-align: right;">{displayPrice currency=$id_currency price=$total_commission}</td>
        </tr>
    </table>          
</table>
<!-- / SUMMARY TAB -->

</div>
