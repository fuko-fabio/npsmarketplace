<li class="section"><p>{l s='Selling' mod='npsmarketplace'}</p></li>
{if $account_state == 'none'}
<li><a {if $page_name == 'seller-request'}class="active"{/if} href="{$seller_request_link}" onclick="$.fancybox.showLoading();"><i class="icon-share"></i><span>{l s='Become a Seller' mod='npsmarketplace'}</span></a></li>
{/if}
{if $account_state == 'locked' || $account_state == 'active' || $account_state == 'requested'}
<li><a {if $page_name == 'seller-account'}class="active"{/if} href="{$seller_profile_link}" onclick="$.fancybox.showLoading();"><i class="icon-th-large"></i><span>{l s='My Shop' mod='npsmarketplace'}</span></a></li>
{/if}
{if $account_state == 'requested' && $products_count == 0}
<li><a {if $page_name == 'add-product'}class="active"{/if} href="{$add_product_link}" onclick="$.fancybox.showLoading();"><i class="icon-plus"></i><span>{l s='Add First Event' mod='npsmarketplace'}</span></a></li>
{/if}
{if $account_state == 'locked'}
    <li><a {if $page_name == 'unlock-account'}class="active"{/if} href="{$unlock_account_link}" onclick="$.fancybox.showLoading();"><i class="icon-lock"></i><span>{l s='Unlock Account' mod='npsmarketplace'}</span></a></li>
{/if}
{if  $payment_configured == 1}
    {if $account_state == 'active' }
    <li><a {if $page_name == 'add-product'}class="active"{/if} href="{$add_product_link}" onclick="$.fancybox.showLoading();"><i class="icon-plus"></i><span>{l s='Add Event' mod='npsmarketplace'}</span></a></li>
    <li><a {if $page_name == 'products-list'}class="active"{/if} href="{$products_list_link}" onclick="$.fancybox.showLoading();"><i class="icon-th-list"></i><span>{l s='My Events' mod='npsmarketplace'}</span></a></li>
    <li><a {if $page_name == 'orders'}class="active"{/if} href="{$orders_link}" onclick="$.fancybox.showLoading();"><i class="icon-gift"></i><span>{l s='Customers Orders' mod='npsmarketplace'}</span></a></li>
    <li><a {if $page_name == 'marketing'}class="active"{/if} href="{$marketing_link}" onclick="$.fancybox.showLoading();"><i class="icon-puzzle-piece"></i><span>{l s='Marketing' mod='npsmarketplace'}</span></a></li>
    {/if}
    {if $account_state == 'locked'}
    <li><a {if $page_name == 'orders'}class="active"{/if} href="{$orders_link}" onclick="$.fancybox.showLoading();"><i class="icon-gift"></i><span>{l s='Customers Orders' mod='npsmarketplace'}</span></a></li>
    {/if}
{/if}