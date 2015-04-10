{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}

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
          {if $combination.type == 0}
            {l s='Ticket' mod='npscombinations'}
          {elseif $combination.type == 1}
            {l s='Carnet' mod='npscombinations'}
          {/if}
          </div>
          <div class="col-sm-3">{$combination.name}</div>
          <div class="col-sm-2">{if $combination.type == 0}{$combination.date} {$combination.time}{/if}</div>
          <div class="col-sm-2">{$combination.expiry_date} {$combination.expiry_time}</div>
          <div class="col-sm-2">{if $combination.type < 2}{displayPrice price=$combination.price currency=$currency->id}{/if}</div>
          <div class="col-sm-2">
            {if $combination.type < 2}
            <select class="form-control" data-target="{$combination.id_product_attribute}" name="qty[]" {if ($PS_CATALOG_MODE || !$product->available_for_order || $combination.quantity <=0)}disabled=""{/if}>
               {for $i=0 to $combination.quantity}
                    <option value="{$i}">{$i}</option>
                    {if $i >= 10}
                      {break}
                    {/if}
               {/for}
            </select>
            {/if}
          </div>
        </div>
    {/foreach}
    <button type="button" onclick="addCombinationToCart({$product->id});" class="btn btn-default button button-small pull-right">
        <span><i class="icon-shopping-cart"></i> {l s='Add to cart'}</span>
    </button>
  </div>
</div>

