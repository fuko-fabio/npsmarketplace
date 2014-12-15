{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{if $tickets}
<h1 class="page-heading">{l s='Tickets' mod='npsticketdelivery'}</h1>
<div id="nps_tickets_block" class="box">
    <ul class="grid row">
        {foreach from=$tickets item=ticket}
            <li class="col-xs-12 col-sm-12 col-md-6">
                <div class="nps-ticket">
                    <div class="info">
                        <div class="name">{$ticket.name}</div>
                        {if $ticket.type == 0}
                            <div class="term"><span>{l s='Term' mod='npsticketdelivery'}:</span>{date_format(date_create($ticket.date), 'Y-m-d H:i')}</div>
                        {else if $ticket.type == 1}
                            {if isset($ticket.entries) && ! empty($ticket.entries)}
                                <div class="term"><span>{l s='Entries' mod='npsticketdelivery'}:</span>{$ticket.entries}</div>
                            {else if strtotime($ticket.from) > 0 && strtotime($ticket.to) > 0}
                                <div class="term"><span>{l s='Valid' mod='npsticketdelivery'}:</span>{date_format(date_create($ticket.from), 'Y-m-d')} - {date_format(date_create($ticket.to), 'Y-m-d')}</div>
                            {else}
                                <div class="term"><span></span>{l s='Validity not specified' mod='npsticketdelivery'}</div>
                            {/if}
                        {/if}
                        <div><span>{l s='Adress' mod='npsticketdelivery'}:</span>{$ticket.address}</div>
                        <div><span>{l s='District' mod='npsticketdelivery'}:</span>{$ticket.district}</div>
                        <div><span>{l s='Town' mod='npsticketdelivery'}:</span>{$ticket.town}</div>
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
                </div>
            </li>
        {/foreach}
    </ul>
</div>
{/if}
