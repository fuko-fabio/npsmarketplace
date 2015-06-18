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
                            <div class="name">{$ticket.name|truncate:25}</div>
                            {if $ticket.type == 'ticket'}
                                <div class="term"><span>{l s='Term' mod='npsticketdelivery'}:</span>{date_format(date_create($ticket.date), 'Y-m-d H:i')}</div>
                            {else}
                                <div class="term">{l s='Carnet' mod='npsticketdelivery'}</div>
                            {/if}
                            <div><span>{l s='Person' mod='npsticketdelivery'}:</span>{$ticket.person|truncate:30}</div>
                            <div><span>{l s='Combination' mod='npsticketdelivery'}:</span>{$ticket.combination_name|truncate:30}</div>
                            <div><span>{l s='Price' mod='npsticketdelivery'}:</span>{displayPrice price=$ticket.price currency=$ticket.id_currency}</div>
                            <div class="code">{$ticket.code}</div>
                        </div>
                        <a class="ticket-seller" href="{$ticket.seller_shop}">{$ticket.seller|truncate:20}</a>
                        {if $is_seller}
                        {assign var=url value=['id_ticket'=>$ticket.id_ticket]}
                        <span class="qs ticket-pdf">
                            <a href="{$link->getModuleLink('npsticketdelivery', 'Tickets', $url)|escape:'html'}"><i class="icon-download"></i></a>
                            <span class="popover above">
                                {l s="Click to download ticket as PDF file" mod='npsticketdelivery'}
                            </span>
                        </span>
                        {/if}
                        <span class="qs ticket-location">
                            <a href="http://maps.google.com/?q={$ticket.address}" target="_blank"><i class="icon-map-marker"></i></a>
                            <span class="popover above">
                                {$ticket.address}<br />
                                {l s="Click to view on Google maps" mod='npsticketdelivery'}</a>
                            </span>
                        </span>
                    </div>
                </li>
        {/foreach}
    </ul>
</div>
{/if}
