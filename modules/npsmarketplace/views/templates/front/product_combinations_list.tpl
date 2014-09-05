{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Event terms'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='Event Terms'}</h1>

<div class="block-center" id="block-seller-products-list">
    {if $comb_array}
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="active">
                    <td>{l s='Date & Time' mod='npsmarketplace'}</td>
                    <td>{l s='Quantity' mod='npsmarketplace'}</td>
                    <td>{l s='Action' mod='npsmarketplace'}</td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$comb_array item=comb}
                <tr class="active">
                    <td>{$comb['name']}</td>
                    <td>{$comb['quantity']}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{$comb.delete_url}" class="edit btn btn-default"><i class="icon-trash"></i> {l s='Delete' mod='npsmarketplace'}</a>
                        </div>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>
        <script>
            $('.dropdown-toggle').dropdown();
        </script>
    </div>
    {else}
        <p class="alert alert-info">{l s='No available terms for selected event.' mod='npsmarketplace'}</p>
    {/if}
</div>