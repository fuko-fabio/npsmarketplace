{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
<!doctype html>

<html lang="{$lang}">
    <head>
      <meta charset="utf-8">
    
      <title>{$title}</title>
      <meta name="description" content="{$description}">
      <meta name="author" content="LabsInTown">
    
      {foreach from=$css_urls item=css}
      <link rel="stylesheet" href="{$css}">
      {/foreach}

      <!--[if lt IE 9]>
      <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
      <![endif]-->
    </head>
	<body>
        <div class="lit-content-body">
			<div class="row">
				{foreach from=$products item=product name=myLoop}
				{if $in_row == 2}
				<div class="item col-xs-6 col-sm-6 col-md-6">
				{else if $in_row == 3}
				<div class="item col-xs-4 col-sm-4 col-md-4">
				{else if $in_row == 4}
				<div class="item col-xs-3 col-sm-3 col-md-3">
				{/if}
					<div class="item-content">
						<a href="{$product.link|escape:'html'}" title="{$product.name|escape:html:'UTF-8'}"> <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}" alt="{$product.name|escape:html:'UTF-8'}" /> </a>
					    {if isset($product.new) && $product.new == 1}
					    <span class="new-box"> <span class="new-label">{l s='New'}</span> </span>
						{/if}
						{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
						<span class="sale-box"> <span class="sale-label">{l s='Sale!'}</span> </span>
						{/if}
						<div class="content-price">
							{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
							<span itemprop="price" class="price product-price"> {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if} </span>
							<meta itemprop="priceCurrency" content="{$priceDisplay}" />
							{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
							<span class="old-price product-price"> {displayWtPrice p=$product.price_without_reduction} </span>
							{if $product.specific_prices.reduction_type == 'percentage'}
							<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
							{/if}
						    {/if}
							{/if}
						</div>
					   <h5 itemprop="name">{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if} <a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" > {$product.name|truncate:45:'...'|escape:'html':'UTF-8'} </a></h5>
					</div>
				</div>
				{/foreach}
			</div>
    	     <a href="{$shop_url}" class="pull-right shop"><span>Labs In</span> Town</a>
		</div>
	</body>
</html>