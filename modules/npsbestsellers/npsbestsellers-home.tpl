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

<!-- MODULE Home Block best sellers -->
<div id="best-sellers_block_center" class="block products_block">
	<h2>{l s='Best Sellers' mod='npsbestsellers'}</h2>
	{if isset($best_sellers) AND $best_sellers}
		<div class="clearfix">
			{assign var='liHeight' value=320}
			{assign var='nbItemsPerLine' value=4}
			{assign var='nbLi' value=$best_sellers|@count}
			{math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
			{math equation="nbLines*liHeight" nbLines=$nbLines|ceil liHeight=$liHeight assign=ulHeight}
            <div class="row">
			{foreach from=$best_sellers item=product name=myLoop}
                <div class="item col-xs-6 col-sm-4 col-md-2">
                    <a href="{$product.link|escape:'html'}" title="{$product.name|escape:html:'UTF-8'}">
                        <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" alt="{$product.name|escape:html:'UTF-8'}" />
                        {if isset($product.new) && $product.new == 1}<span class="new-box"><span class="new-label">{l s='New!' mod='npshomefeatured'}</span></span>{/if}
                        {if isset($product.on_sale) && $product.on_sale == 1}<span class="sale-box"><span class="sale-label">{l s='Sale!' mod='npshomefeatured'}</span></span>{/if}
                        <span class="name">{$product.name|escape:'html':'UTF-8'}</span>
<!--
                        <span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>
-->
                    </a>
                </div>
			{/foreach}
		</div>
	{else}
		<p class="alert alert-info">{l s='No best sellers' mod='npsbestsellers'}</p>
	{/if}
</div>
<!-- /MODULE Home Block best sellers -->
