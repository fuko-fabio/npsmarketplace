{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Customers Orders' mod='npsmarketplace'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<div class="block-center" id="block-seller-orders">
    <h1 class="page-heading bottom-indent">{l s='Customers Orders' mod='npsmarketplace'}</h1>

    {if $orders}
    <div class="table-responsive">
        <table class="table table-bordered footab">
            <thead>
                <tr>
                    <th class="first_item" data-sort-ignore="true">{l s='Reference' mod='npsmarketplace'}</td>
                    <th class="item" data-hide="phone,tablet">{l s='Customer' mod='npsmarketplace'}</td>
                    <th class="item">{l s='Date' mod='npsmarketplace'}</td>
                    <th class="item">{l s='Total' mod='npsmarketplace'}</td>
                    <th class="item" data-hide="phone,tablet">{l s='Payment' mod='npsmarketplace'}</td>
                    <th class="item">{l s='State' mod='npsmarketplace'}</td>
                    <th class="last_item" data-sort-ignore="true" width="100px" >{l s='Action' mod='npsmarketplace'}</td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$orders item=order}
                <tr>
                    <td>{$order['reference']}</td>
                    <td>{$order['customer']}</td>
                    <td>{$order['date_add']}</td>
                    <td>{$order['total_paid_tax_incl']}</td>
                    <td>{$order['payment']}</td>
                    <td{if isset($order.order_state)} data-value="{$order.id_order_state}"{/if}>
                        {if isset($order.order_state)}
                            <span class="label{if isset($order.order_state_color) && Tools::getBrightness($order.order_state_color) > 128} dark{/if}"{if isset($order.order_state_color) && $order.order_state_color} style="background-color:{$order.order_state_color|escape:'html':'UTF-8'}; border-color:{$order.order_state_color|escape:'html':'UTF-8'};"{/if}>
                                {$order.order_state|escape:'html':'UTF-8'}
                            </span>
                        {/if}
                    </td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{$order['link']}" class="edit btn btn-default"><i class="icon-search"></i> {l s='View' mod='npsmarketplace'}</a>
                        </div>
                    </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {else}
        <p class="alert alert-info">{l s='You have no orders.' mod='npsmarketplace'}</p>
    {/if}
</div>