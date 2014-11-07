{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}

<!-- MODULE nps Home Featured Products -->
<div id="nps-featured-products_center">
    <h2>{l s='Popular' mod='npshomefeatured'}</h2>
    {if isset($products) AND $products}
        {include file="$tpl_dir./product-list.tpl" products=$products class='npsnewproducts' id='npsnewproducts'}
  	{else}
   		<p class="alert alert-info">{l s='No featured products' mod='npshomefeatured'}</p>
   	{/if}
</div>
<!-- /MODULE Home Featured Products -->
