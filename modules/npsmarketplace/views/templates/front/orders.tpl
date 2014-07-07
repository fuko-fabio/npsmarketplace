{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
		{l s='My account'}
	</a>
	<span class="navigation-pipe">{$navigationPipe}</span>
	<span class="navigation_page">{l s='Customers Orders'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<div class="box">
    <h1 class="page-heading bottom-indent">{l s='Customers Orders'}</h1>

    {if $orders}
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr class="active">
                    <td>ID</td>
                    <td>Reference</td>
                    <td>Customer</td>
                    <td>Total</td>
                    <td>Payment</td>
                    <td>Status</td>
                    <td>Date</td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$orders item=order}
                <tr class="active">
                    <td><img src="{$product['cover']}" class="imgm img-thumbnail" width="52"/></td>
                    <td>{$order['id_order']}</td>
                    <td>{$order['reference']}</td>
                    <td>{$order['customer']}</td>
                    <td>{$order['total_paid_tax_incl']}</td>
                    <td>{$order['payment']}</td>
                    <td>{$order['state']}</td>
                    <td>{$order['date_add']}</td>
                    <td>
                        <div class="btn-group pull-right">
                            <a href="{$view_order_link}" class="edit btn btn-default"><i class="icon-pencil"></i> View</a>
                        </div>
                    </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    {else}
        <p class="alert alert-info">{l s='You have no orders.' mod='npsmarketplace'}</p>
        </br>
        {l s='Click' mod='npsmarketplace'} <a href="{$add_product_link}">{l s='here'}</a> {l s='to add your first product.' mod='npsmarketplace'}
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