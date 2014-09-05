<ul>
    {if $account_state == 'none'}
        <li><a href="{$seller_request_link}"><i class="icon-share"></i><span>{l s='Become a Seller' mod='npsmarketplace'}</span></a></li>
    {/if}
    {if $account_state == 'locked' || $account_state == 'active' || $account_state == 'requested'}
        <li><a href="{$seller_profile_link}"><i class="icon-th-large"></i><span>{l s='My Shop' mod='npsmarketplace'}</span></a></li>
    {/if}
    {if $account_state == 'requested' && $products_count == 0}
        <li><a href="{$add_product_link}"><i class="icon-plus"></i><span>{l s='Add Event' mod='npsmarketplace'}</span></a></li>
    {/if}
    {if $account_state == 'locked'}
        <li><a href="{$unlock_account_link}"><i class="icon-lock"></i><span>{l s='Unlock Account' mod='npsmarketplace'}</span></a></li>
    {/if}
    {if $account_state == 'requested'}
        <li><a href="{$products_list_link}"><i class="icon-th-list"></i><span>{l s='My Events' mod='npsmarketplace'}</span></a></li>
    {/if}
    {if $account_state == 'active'}
        <li><a href="{$add_product_link}"><i class="icon-plus"></i><span>{l s='Add Event' mod='npsmarketplace'}</span></a></li>
        <li><a href="{$products_list_link}"><i class="icon-th-list"></i><span>{l s='My Events' mod='npsmarketplace'}</span></a></li>
    {/if}
    {if $account_state == 'active' || $account_state == 'locked'}
        <li><a href="{$orders_link}"><i class="icon-gift"></i><span>{l s='Orders' mod='npsmarketplace'}</span></a></li>
    {/if}
</ul>