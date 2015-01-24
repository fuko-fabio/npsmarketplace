{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div style="font-size: 9pt; color: #444">

<table style="width: 100%">
    <tr>
        <td style="width: 100%"><b>{l s='Order Number:' mod='npsticketdelivery'}</b>{$order->getUniqReference()}<br />
            <b>{l s='Order Date:' mod='npsticketdelivery'}</b>{dateFormat date=$order->date_add full=0}<br />
            {l s='Tax included if needed.' mod='npsticketdelivery'}<br />
            {l s='Payment finalized through the Przelewy 24 service.' mod='npsticketdelivery'}<br />
        </td>
    </tr>
</table>

<!-- ADDRESSES -->
			{if !empty($delivery_address)}
				<table style="width: 100%">
					<tr>
						<td style="width: 50%; background-color: #4D4D4D; color: #FFF;">
							<span style="font-weight: bold; font-size: 10pt;">{l s='Delivery Address' mod='npsticketdelivery'}</span><br />
						</td>
						<td style="width: 50%; background-color: #4D4D4D; color: #FFF;">
							<span style="font-weight: bold; font-size: 10pt;">{l s='Billing Address' mod='npsticketdelivery'}</span><br />
						</td>
					</tr>
					<tr>
                        <td style="width: 50%">{$delivery_address}</td>
                        <td style="width: 50%">{$invoice_address}</td>
                    </tr>
				</table>
			{else}
				<table style="width: 100%; border: solid 1px #4D4D4D;">
					<tr>
						<td style="width: 100%; background-color: #4D4D4D; color: #FFF;">
							<span style="font-weight: bold; font-size: 10pt;">{l s='Billing & Delivery Address.' mod='npsticketdelivery'}</span><br />
						</td>
					</tr>
					<tr>
                        <td style="width: 100%">{$invoice_address}</td>
                    </tr>
				</table>
			{/if}
<!-- / ADDRESSES -->

<div style="line-height: 1pt">&nbsp;</div>

{foreach $items as $item}
<!-- SELLER TAB -->
<table style="width: 100%; border: solid 1px #4D4D4D;">
    <tr>
        <td style="width: 100%; background-color: #4D4D4D; color: #FFF;"><span style="font-weight: bold; font-size: 10pt;">{l s='Seller' mod='npsticketdelivery'}: {$item.seller->name}</span><br />
        </td>
    </tr>
    <tr>
        <td style="width: 100%">{$item.address_html}<br />
            {$item.customer->email}<br />
            {if isset($item.seller->nip) && !empty($item.seller->nip)}{l s='NIP' mod='npsticketdelivery'}: {$item.seller->nip}<br />{/if}
            {if isset($item.seller->regon) && !empty($item.seller->regon)}{l s='REGON' mod='npsticketdelivery'}: {$item.seller->regon}<br />{/if}
            {if isset($item.seller->krs) && !empty($item.seller->krs)}{l s='KRS' mod='npsticketdelivery'}: {$item.seller->krs}<br />{/if}
            {if isset($item.seller->krs_reg) && !empty($item.seller->krs_reg)}{l s='KRS registered by' mod='npsticketdelivery'}: {$item.seller->krs_reg}<br />{/if}
        </td>
    </tr>
</table>
<!-- END SELLER TAB -->

<!-- PRODUCTS TAB -->
			<table style="width: 100%; font-size: 8pt; ">
				<tr style="line-height:4px;">
					<td style="text-align: left; background-color: #4D4D4D; color: #FFF; padding-left: 10px; font-weight: bold; width: {if !$tax_excluded_display}35%{else}45%{/if}">{l s='Product / Reference' mod='npsticketdelivery'}</td>
					<!-- unit price tax excluded is mandatory -->
					{if !$tax_excluded_display}
						<td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 20%">{l s='Unit Price' mod='npsticketdelivery'} <br />{l s='(Tax Excl.)' mod='npsticketdelivery'}</td>
					{/if}
					<td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 10%">
						{l s='Unit Price' mod='npsticketdelivery'}
						{if $tax_excluded_display}
							 {l s='(Tax Excl.)' mod='npsticketdelivery'}
						{else}
							 {l s='(Tax Incl.)' mod='npsticketdelivery'}
						{/if}
					</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: 10%; white-space: nowrap;">{l s='Discount' mod='npsticketdelivery'}</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: center; font-weight: bold; width: 10%">{l s='Qty' mod='npsticketdelivery'}</td>
					<td style="background-color: #4D4D4D; color: #FFF; text-align: right; font-weight: bold; width: {if !$tax_excluded_display}15%{else}25%{/if}">
						{l s='Total' mod='npsticketdelivery'}
						{if $tax_excluded_display}
							{l s='(Tax Excl.)' mod='npsticketdelivery'}
						{else}
							{l s='(Tax Incl.)' mod='npsticketdelivery'}
						{/if}
					</td>
				</tr>
				<!-- PRODUCTS -->
				{foreach $item.order_details as $order_detail}
				{cycle values='#EEE,#DDD' assign=bgcolor}
				<tr style="line-height:6px;background-color:{$bgcolor};">
					<td style="text-align: left; width: {if !$tax_excluded_display}35%{else}45%{/if}">{$order_detail.product_name}{if isset($order_detail.product_reference) && !empty($order_detail.product_reference)} ({l s='Reference:' mod='npsticketdelivery'} {$order_detail.product_reference}){/if}</td>
					<!-- unit price tax excluded is mandatory -->
					{if !$tax_excluded_display}
						<td style="text-align: right; width: 20%; white-space: nowrap;">
						{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl}
						</td>
					{/if}
					<td style="text-align: right; width: 10%; white-space: nowrap;">
					{if $tax_excluded_display}
						{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl}
					{else}
						{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_incl}
					{/if}
					</td>
					<td style="text-align: right; width: 10%">
					{if (isset($order_detail.reduction_amount) && $order_detail.reduction_amount > 0)}
						-{displayPrice currency=$order->id_currency price=$order_detail.reduction_amount}
					{elseif (isset($order_detail.reduction_percent) && $order_detail.reduction_percent > 0)}
						-{$order_detail.reduction_percent}%
					{else}
					--
					{/if}
					</td>
					<td style="text-align: center; width: 10%">{$order_detail.product_quantity}</td>
					<td style="text-align: right;  width: {if !$tax_excluded_display}15%{else}25%{/if}; white-space: nowrap;">
					{if $tax_excluded_display}
						{displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_excl}
					{else}
						{displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_incl}
					{/if}
					</td>
				</tr>
					{foreach $order_detail.customizedDatas as $customizationPerAddress}
						{foreach $customizationPerAddress as $customizationId => $customization}
							<tr style="line-height:6px;background-color:{$bgcolor};">
								<td style="line-height:3px; text-align: left; width: 45%; vertical-align: top">
										<blockquote>
											{if isset($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) && count($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) > 0}
												{foreach $customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_] as $customization_infos}
													{$customization_infos.name}: {$customization_infos.value}
													{if !$smarty.foreach.custo_foreach.last}<br />
													{else}
													<div style="line-height:0.4pt">&nbsp;</div>
													{/if}
												{/foreach}
											{/if}

											{if isset($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) && count($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) > 0}
												{count($customization.datas[$smarty.const._CUSTOMIZE_FILE_])} {l s='image(s)' mod='npsticketdelivery'}
											{/if}
										</blockquote>
								</td>
								{if !$tax_excluded_display}
									<td style="text-align: right;"></td>
								{/if}
								<td style="text-align: right; width: 10%"></td>
								<td style="text-align: center; width: 10%; vertical-align: top">({$customization.quantity})</td>
								<td style="width: 15%; text-align: right;"></td>
							</tr>
						{/foreach}
					{/foreach}
				{/foreach}
				<!-- END PRODUCTS -->

				<!-- CART RULES
				{assign var="shipping_discount_tax_incl" value="0"}
				{foreach $cart_rules as $cart_rule}
					{cycle values='#FFF,#DDD' assign=bgcolor}
					<tr style="line-height:6px;background-color:{$bgcolor};text-align:left;">
						<td style="line-height:3px;text-align:left;width:60%;vertical-align:top" colspan="{if !$tax_excluded_display}5{else}4{/if}">{$cart_rule.name}</td>
						<td>
							{if $tax_excluded_display}
								- {$cart_rule.value_tax_excl}
							{else}
								- {$cart_rule.value}
							{/if}
						</td>
					</tr>
				{/foreach}
				END CART RULES -->
			</table>

<div style="line-height: 1pt">&nbsp;</div>

{/foreach}
<!-- / PRODUCTS TAB -->

            <table style="width: 100%">
                {if (($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0)}
                <tr style="line-height:5px;">
                    <td style="width: 83%; text-align: right; font-weight: bold">{l s='Product Total (Tax Excl.)' mod='npsticketdelivery'}</td>
                    <td style="width: 17%; text-align: right;">{displayPrice currency=$order->id_currency price=$order->total_products}</td>
                </tr>

                <tr style="line-height:5px;">
                    <td style="width: 83%; text-align: right; font-weight: bold">{l s='Product Total (Tax Incl.)' mod='npsticketdelivery'}</td>
                    <td style="width: 17%; text-align: right;">{displayPrice currency=$order->id_currency price=$order->total_products_wt}</td>
                </tr>
                {else}
                <tr style="line-height:5px;">
                    <td style="width: 83%; text-align: right; font-weight: bold">{l s='Product Total' mod='npsticketdelivery'}</td>
                    <td style="width: 17%; text-align: right;">{displayPrice currency=$order->id_currency price=$order->total_products}</td>
                </tr>
                {/if}

<!--
                {if $order->total_discount_tax_incl > 0}
                <tr style="line-height:5px;">
                    <td style="text-align: right; font-weight: bold">{l s='Total Vouchers' mod='npsticketdelivery'}</td>
                    <td style="width: 17%; text-align: right;">-{displayPrice currency=$order->id_currency price=($order->total_discount_tax_incl)}</td>
                </tr>
                {/if}-->

                {if $order->total_wrapping_tax_incl > 0}
                <tr style="line-height:5px;">
                    <td style="text-align: right; font-weight: bold">{l s='Wrapping Cost' mod='npsticketdelivery'}</td>
                    <td style="width: 17%; text-align: right;">
                    {if $tax_excluded_display}
                        {displayPrice currency=$order->id_currency price=$order->total_wrapping_tax_excl}
                    {else}
                        {displayPrice currency=$order->id_currency price=$order->total_wrapping_tax_incl}
                    {/if}
                    </td>
                </tr>
                {/if}

                {if $order->total_shipping_tax_incl > 0}
                <tr style="line-height:5px;">
                    <td style="text-align: right; font-weight: bold">{l s='Shipping Cost' mod='npsticketdelivery'}</td>
                    <td style="width: 17%; text-align: right;">
                        {if $tax_excluded_display}
                            {displayPrice currency=$order->id_currency price=$order->total_shipping_tax_excl}
                            {else}
                            {displayPrice currency=$order->id_currency price=$order->total_shipping_tax_incl}
                        {/if}
                    </td>
                </tr>
                {/if}

                {if ($order->total_paid_tax_incl - $order->total_paid_tax_excl) > 0}
                <tr style="line-height:5px;">
                    <td style="text-align: right; font-weight: bold">{l s='Total Tax' mod='npsticketdelivery'}</td>
                    <td style="width: 17%; text-align: right;">{displayPrice currency=$order->id_currency price=($order->total_paid_tax_incl - $order->total_paid_tax_excl)}</td>
                </tr>
                {/if}

                <tr style="line-height:5px;">
                    <td style="text-align: right; font-weight: bold">{l s='Total' mod='npsticketdelivery'}</td>
                    <td style="width: 17%; text-align: right;">{displayPrice currency=$order->id_currency price=$order->total_paid_tax_incl}</td>
                </tr>

            </table>

{if isset($HOOK_DISPLAY_PDF)}
<div style="line-height: 1pt">&nbsp;</div>
<table style="width: 100%">
	<tr>
		<td style="width: 100%">{$HOOK_DISPLAY_PDF}</td>
	</tr>
</table>
{/if}

</div>
