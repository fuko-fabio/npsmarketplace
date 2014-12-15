{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Tickets Sold' mod='npsmarketplace'}</span>
{/capture}
<div class="block-center"  id="nps_tickets_block">
    <h1 class="page-heading bottom-indent">{l s='Tickets Sold' mod='npsticketdelivery'}</h1>
    {include file="$tpl_dir./errors.tpl"}
    {if $tickets}
    <div class="content_sortPagiBar">
        <div class="sortPagiBar clearfix">
            {include file="$tpl_dir./nbr-product-page.tpl"}
        </div>
        <div class="top-pagination-content clearfix">
            {include file="$tpl_dir./pagination.tpl"}
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered footab table-hover">
            <thead>
                <tr>
                    <th class="first_item" data-sort-ignore="true">{l s='ID' mod='npsticketdelivery'}</th>
                    <th class="item">{l s='Name' mod='npsticketdelivery'}</th>
                    <th class="item">{l s='Code' mod='npsticketdelivery'}</th>
                    <th class="item">{l s='Price' mod='npsticketdelivery'}</th>
                    <th class="item">{l s='Gift' mod='npsticketdelivery'}</th>
                    <th class="item">{l s='Person' mod='npsticketdelivery'}</th>
                    <th class="item" data-hide="phone,tablet">{l s='Address' mod='npsticketdelivery'}</th>
                    <th class="item" data-hide="phone,tablet">{l s='District' mod='npsticketdelivery'}</th>
                    <th class="item" data-hide="phone,tablet">{l s='Town' mod='npsticketdelivery'}</th>
                    <th class="item" data-hide="phone,tablet">{l s='Info' mod='npsticketdelivery'}</th>
                    <th class="item">{l s='Type' mod='npsticketdelivery'}</th>
                    <th class="last_item" data-sort-ignore="true" width="100px" >{l s='Action' mod='npsmarketplace'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$tickets item=ticket}
                <tr>
                    <td>{$ticket.id_ticket}</td>
                    <td>{$ticket.name}</td>
                    <td>{$ticket.code}</td>
                    <td>{displayPrice price=$ticket.price currency=$ticket.id_currency}</td>
                    <td>{if $ticket.gift == 1}
                        <i class="icon-ok"></i>
                        {else}
                        <i class="icon-remove"></i>
                        {/if}
                    </td>
                    <td>{$ticket.person}</td>
                    <td>{$ticket.address}</td>
                    <td>{$ticket.district}</td>
                    <td>{$ticket.town}</td>
                    {if $ticket.type == 0}
                        <td>{l s='Term' mod='npsticketdelivery'}: {date_format(date_create($ticket.date), 'Y-m-d H:i')}</td>
                    {else if $ticket.type == 1}
                        {if isset($ticket.entries) && ! empty($ticket.entries)}
                            <td>{l s='Entries' mod='npsticketdelivery'}: {$ticket.entries}</td>
                        {else if strtotime($ticket.from) > 0 && strtotime($ticket.to) > 0}
                            <td>{l s='Valid' mod='npsticketdelivery'}: {date_format(date_create($ticket.from), 'Y-m-d')} - {date_format(date_create($ticket.to), 'Y-m-d')}</td>
                        {/if}
                    {/if}
                    <td>
                        {if $ticket.type == 0}
                        {l s='Ticket' mod='npsticketdelivery'}
                        {else if $ticket.type == 1}
                        {l s='Carnet' mod='npsticketdelivery'}
                        {/if}
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="{$ticket.order_url}" class="edit btn btn-default"><i class="icon-search"></i> {l s='View' mod='npsmarketplace'}</a>
                        </div>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    <div class="content_sortPagiBar">
        <div class="bottom-pagination-content clearfix">
            {include file="$tpl_dir./pagination.tpl" paginationId='bottom'}
        </div>
    </div>
    {else}
        <p class="alert alert-info"><span class="alert-content">{l s='You have no tickets sold.' mod='npsticketdelivery'}</span></p>
    {/if}
</div>

