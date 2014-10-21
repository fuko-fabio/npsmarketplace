{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<div style="font-size: 8pt; color: #444">

<table>
    <tr><td>&nbsp;</td></tr>
</table>

<!-- ADDRESSES -->
<table style="width: 100%">
    <tr>
        <td style="width: 15%">
            <span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Seller' pdf='true'}:</span>
        </td>
	    <td style="width: 85%">
            {$company->company_name}<br />
            {$company->person}<br />
            {$company->street}<br />
            {$company->post_code} {$company->city}<br />
            NIP: {$company->nip}<br />
            REGON: {$company->regon}<br />
		</td>
	</tr>
</table>
<!-- / ADDRESSES -->

<div style="line-height: 1pt">&nbsp;</div>

<!-- SUMMARY TAB -->
<table style="width: 100%; font-size: 8pt;">
    <tr style="line-height:4px;">
        <td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: 35%">{l s='Product / Reference' pdf='true'}</td>
        <td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 15%">{l s='Unit Price' pdf='true'}</td>
        <td style="background-color: #4D4D4D; color: #FFF; text-align: center; font-weight: bold; width: 5%">{l s='Qty' pdf='true'}</td>
        <td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 15%">{l s='Total Turnover' pdf='true'}</td>
        <td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 15%">{l s='Seller Revenue' pdf='true'}</td>
        <td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 15%">{l s='Shop Commission' pdf='true'}</td>
    </tr>
    <!-- PRODUCTS -->
    {foreach $items as $item}
    {cycle values='#FFF,#DDD' assign=bgcolor}
    <tr style="line-height:6px;background-color:{$bgcolor};">
        <td style="text-align: left; width: 35%">{$item.product_name}{if isset($item.product_reference) && !empty($item.product_reference)} ({l s='Reference:' pdf='true'} {$item.product_reference}){/if}</td>
        <td style="text-align: right; width: 15%; white-space: nowrap;">
            {displayPrice currency=$item.id_currency price=$item.unit_price}
        </td>
        <td style="text-align: center; width: 5%">{$item.product_quantity}</td>
        <td style="text-align: right; width: 15%; white-space: nowrap;">
            {displayPrice currency=$item.id_currency price=$item.total_price}
        </td>
        <td style="text-align: right; width: 15%; white-space: nowrap;">
            {displayPrice currency=$item.id_currency price=$item.seller_price}
        </td>
        <td style="text-align: right; width: 15%; white-space: nowrap;">
            {displayPrice currency=$item.id_currency price=$item.commision_price}
        </td>
    </tr>
    {/foreach}
    <!-- END PRODUCTS --> 
    <hr />
    <table style="width: 100%">
        <tr style="line-height:5px;">
            <td style="width: 83%; text-align: right; font-weight: bold">{l s='Total seller turnover' pdf='true'}</td>
            <td style="width: 17%; text-align: right;">{displayPrice currency=$id_currency price=$total}</td>
        </tr>
        <tr style="line-height:5px;">
            <td style="width: 83%; text-align: right; font-weight: bold">{l s='Total shop commission' pdf='true'}</td>
            <td style="width: 17%; text-align: right;">{displayPrice currency=$id_currency price=$total_commission}</td>
        </tr>
        <hr />
        <tr style="line-height:5px;">
            <td style="width: 83%; text-align: right; font-weight: bold">{l s='Total seller revenue' pdf='true'}</td>
            <td style="width: 17%; text-align: right;">{displayPrice currency=$id_currency price=$total_seller}</td>
        </tr>
    </table>          
</table>
<!-- / SUMMARY TAB -->

</div>
