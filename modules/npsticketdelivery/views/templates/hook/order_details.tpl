{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{if $tickets}
{if isset($invoice_url)}
<h1 class="page-heading">{l s='Invoice' mod='npsticketdelivery'}</h1>
<div class="box">
    <p>{l s='Transaction summary can be downloaded here.' mod='npsticketdelivery'}</p>
    <a class="btn btn-default button button-medium pull-right" href="{$invoice_url}">{l s='Download' mod='npsticketdelivery'} <i class="icon-file-text"></i></a>
    <br />
</div>
{/if}
<h1 class="page-heading">{l s='Tickets' mod='npsticketdelivery'}</h1>
<div id="nps_tickets_block" class="box">
    <ul class="grid row">
        {foreach from=$tickets item=ticket}
            <li class="col-xs-12 col-sm-12 col-md-6">
                <div class="nps-ticket">
                    <div class="info">
                        <div class="name">{$ticket.name}</div>
                        <div class="combination"><span>{l s='Combination' mod='npsticketdelivery'}:</span>{$ticket.combination_name}</div>
                        {if $ticket.type == 0}
                            <div class="term"><span>{l s='Term' mod='npsticketdelivery'}:</span>{date_format(date_create($ticket.date), 'Y-m-d H:i')}</div>
                        {/if}
                        <div><span>{l s='Price' mod='npsticketdelivery'}:</span>{displayPrice price=$ticket.price currency=$ticket.id_currency}</div>
                        {if $is_seller}
                        <div><span>{l s='Seller' mod='npsticketdelivery'}:</span><a href="{$ticket.seller_shop}">{$ticket.seller}</a></div>
                        {/if}
                        <div class="code">{$ticket.code}</div>
                    </div>
                    {if $is_seller}
                    <div class="buttons">
                        {assign var=url value=['id_ticket'=>$ticket.id_ticket]}
                        <a href="{$link->getModuleLink('npsticketdelivery', 'Tickets', $url)|escape:'html'}" class="btn btn-default button button-small pull-right"><i class="icon-download"></i> {l s='Download' mod='npsticketdelivery'}</a>
                    </div>
                    {/if}
                    <span class="qs"><i class="icon-map-marker"></i>
                        <span class="popover above">
                            {$ticket.address}<br />
                            <a href="http://maps.google.com/?q={$ticket.address}" target="_blank">{l s="View on Google maps"}</a>
                        </span>
                    </span>
                </div>
            </li>
        {/foreach}
    </ul>
</div>
{/if}
