{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<h1 class="page-heading">{l s='My tickets'}</h1>
{if isset($account_created)}
	<p class="alert alert-success">
	    <span class="alert-content">
		{l s='Your account has been created.'}
		</span>
	</p>
{/if}

<div id="nps_tickets_block">
    {if $myTickets}
        <div class="content_sortPagiBar">
            <div class="sortPagiBar clearfix">
                {include file="$tpl_dir./nbr-product-page.tpl"}
            </div>
            <div class="top-pagination-content clearfix">
                {include file="$tpl_dir./pagination.tpl"}
            </div>
        </div>
        <ul class="grid row">
            {foreach from=$myTickets item=ticket}
                <li class="col-xs-12 col-sm-12 col-md-6">
                    <div class="nps-ticket">
                        <div class="info">
                            <div class="name">{$ticket.name|truncate:25}</div>
                            {if $ticket.type == 0}
                                <div class="term"><span>{l s='Term'}:</span>{date_format(date_create($ticket.date), 'Y-m-d H:i')}</div>
                            {else}
                                <div class="term">{l s='Carnet'}</div>
                            {/if}
                            <div><span>{l s='Combination'}:</span>{$ticket.combination_name|truncate:30}</div>
                            <div><span>{l s='Price'}:</span>{displayPrice price=$ticket.price currency=$ticket.id_currency}</div>
                            <div class="code">{$ticket.code}</div>
                        </div>
                        <a class="ticket-seller" href="{$ticket.seller_shop}">{$ticket.seller|truncate:20}</a>
                        {assign var=url value=['id_ticket'=>$ticket.id_ticket]}
                        <span class="qs ticket-pdf">
                            <a href="{$link->getModuleLink('npsticketdelivery', 'Tickets', $url)|escape:'html'}"><i class="icon-download"></i></a>
                            <span class="popover above">
                                {l s="Click to download ticket as PDF file"}
                            </span>
                        </span>
                        {if $ticket.type == 0}
                        <span class="qs ticket-calendar">
                            <span class="addtocalendar atc-style-blue">
                                <var class="atc_event">
                                    <var class="atc_date_start">{date_format(date_create($ticket.date), 'Y-m-d H:i')}</var>
                                    <var class="atc_date_end">{date_format(date_modify(date_create($ticket.date), '+ 1 hour'), 'Y-m-d H:i')}</var>
                                    <var class="atc_timezone">Europe/Warsaw</var>
                                    <var class="atc_title">{$ticket.name}</var>
                                    <var class="atc_description">{$ticket.name}</var>
                                    <var class="atc_location">{$ticket.address}</var>
                                    <var class="atc_organizer">{$ticket.seller}</var>
                                </var>
                            </span>
                            <span class="popover above">
                                {l s="Add event to your calendar. Click and select calendar"}</a>
                            </span>
                        </span>
                        {/if}
                        <span class="qs ticket-location">
                            <a href="http://maps.google.com/?q={$ticket.address}" target="_blank"><i class="icon-map-marker"></i></a>
                            <span class="popover above">
                                {$ticket.address}<br />
                                {l s="Click to view on Google maps"}</a>
                            </span>
                        </span>
                    </div>
                </li>
            {/foreach}
        </ul>
        <div class="content_sortPagiBar">
            <div class="bottom-pagination-content clearfix">
                {include file="$tpl_dir./pagination.tpl" paginationId='bottom'}
            </div>
        </div>
    {else}
        <p class="alert alert-info"><span class="alert-content">{l s='You have not bought tickets yet.'}</span></p>
    {/if}
</div>