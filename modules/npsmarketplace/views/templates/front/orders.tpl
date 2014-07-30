{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
		{l s='My account'}
	</a>
	<span class="navigation-pipe">{$navigationPipe}</span>
	<span class="navigation_page">{l s='Customers Orders' mod='npsmarketplace'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<div class="box">
    <h1 class="page-heading bottom-indent">{l s='Customers Orders' mod='npsmarketplace'}</h1>

    {if $orders}
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="active">
                    <td>{l s='Reference' mod='npsmarketplace'}</td>
                    <td>{l s='Customer' mod='npsmarketplace'}</td>
                    <td width="60px" >{l s='Total' mod='npsmarketplace'}</td>
                    <td>{l s='Payment' mod='npsmarketplace'}</td>
                    <td>{l s='State' mod='npsmarketplace'}</td>
                    <td width="100px">{l s='Date' mod='npsmarketplace'}</td>
                    <td width="100px" >{l s='Action' mod='npsmarketplace'}</td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$orders item=order}
                <tr class="active">
                    <td>{$order['reference']}</td>
                    <td>{$order['customer']}</td>
                    <td>{$order['total_paid_tax_incl']}</td>
                    <td>{$order['payment']}</td>
                    <td>{$order['state']}</td>
                    <td>{$order['date_add']}</td>
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
<ul class="footer_links clearfix">
	<li>
		<a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to Your Account'}
			</span>
		</a>
	</li>
	<li>
		<a class="btn btn-default button button-small" href="{$base_dir}">
			<span><i class="icon-chevron-left"></i> {l s='Home'}</span>
		</a>
	</li>
</ul>