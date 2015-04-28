{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}

{addJsDef npsCombinations=$npscombinations}

<div class="tab-pane fade" id="product_combinations_tab">
  <div class="table combinations-list">
    <div class="row combinations-header">
      <span class="col-sm-1">{l s='Type' mod='npscombinations'}</span>
      <span class="col-sm-3">{l s='Name' mod='npscombinations'}</span>
      <span class="col-sm-2">{l s='Event date' mod='npscombinations'}</span>
      <span class="col-sm-2">{l s='Available' mod='npscombinations'}</span>
      <span class="col-sm-2">{l s='Price' mod='npscombinations'}</span>
      <span class="col-sm-2">{l s='Quantity' mod='npscombinations'}</span>
    </div>
    {foreach from=$npscombinations item=combination}
        <div class="row combinations-item" >
          <div class="col-sm-1">
          {if isset($combination.type)}
              {if $combination.type == 'ticket'}
                {l s='Ticket' mod='npscombinations'}
              {elseif $combination.type == 'carnet'}
                {l s='Carnet' mod='npscombinations'}
              {/if}
          {/if}
          </div>
          <div class="col-sm-3">{if isset($combination.name)}{$combination.name}{/if}</div>
          <div class="col-sm-2">{if isset($combination.type) && $combination.type == 'ticket'}{$combination.date} {$combination.time}{/if}</div>
          <div class="col-sm-2">{if isset($combination.expiry_date) && isset($combination.expiry_time)}{$combination.expiry_date} {$combination.expiry_time}{/if}</div>
          <div class="col-sm-2">
              {if isset($combination.price) && (!isset($combination.type) || $combination.type == 'ticket' || $combination.type == 'carnet')}
                {displayPrice price=$combination.price currency=$currency->id}
                {if isset($combination.full_price) && $combination.full_price != $combination.price}
                    <br /><span class="old-price">{displayPrice price=$combination.full_price currency=$currency->id}</span>
                {/if}
              {/if}
          </div>
          <div class="col-sm-2">
            {if isset($combination.type) && ($combination.type == 'ticket' || $combination.type == 'carnet') }
                {if $combination.quantity > 0}
                    <select class="form-control" data-target="{$combination.id_product_attribute}" name="qty[]" {if ($PS_CATALOG_MODE || !$product->available_for_order || $combination.quantity <=0)}disabled=""{/if}>
                       {for $i=0 to $combination.quantity}
                            <option value="{$i}">{$i}</option>
                            {if $i >= 10}
                              {break}
                            {/if}
                       {/for}
                    </select>
                {else}
                    {l s='Sold out!' mod='npscombinations'}
                {/if}
            {/if}
          </div>
        </div>
    {/foreach}
    <button type="button" onclick="addCombinationToCart({$product->id});" class="btn btn-default button button-small pull-right cart-add-combinations">
        <span><i class="icon-shopping-cart"></i> {l s='Add to cart' mod='npscombinations'}</span>
    </button>
  </div>
</div>

