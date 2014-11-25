{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}

<!-- MODULE nps Home Featured Products -->
<div id="nps-featured-products_center">
    <h2>{l s='Popular' mod='npsfeatured'}</h2>
    {if isset($products) AND $products}
        {include file="$tpl_dir./product-list.tpl" products=$products class='npsfeatured' id='npsfeatured'}
  	{else}
   		<p class="alert alert-info"><span class="alert-content">{l s='No featured products' mod='npsfeatured'}</span></p>
   	{/if}
</div>
<!-- /MODULE Home Featured Products -->
