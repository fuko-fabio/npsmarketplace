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

{capture name=path}{l s='My tickets'}{/capture}

<h1 class="page-heading">{l s='My tickets'}</h1>
{if isset($account_created)}
	<p class="alert alert-success">
		{l s='Your account has been created.'}
	</p>
{/if}
<div id="nps_tickets_block_account">
    {if $myTickets}
        <ul class="grid row">
            {foreach from=$myTickets item=ticket}
                <li class="col-xs-12 col-sm-12 col-md-6">
                    <div class="nps-ticket">
                        <div class="info">
                            <div class="name">{$ticket.name}</div>
                            <div class="term"><span>{l s='Term'}:</span>{$ticket.date}</div>
                            <div><span>{l s='Adress'}:</span>{$ticket.address}</div>
                            <div><span>{l s='District'}:</span>{$ticket.district}</div>
                            <div><span>{l s='Town'}:</span>{$ticket.town}</div>
                            <div><span>{l s='Price'}:</span>{displayPrice price=$ticket.price currency=$ticket.id_currency}</div>
                            <div class="code">{$ticket.code}</div>
                        </div>
                        <div class="buttons">
                            <a href="{$link->getModuleLink('npsticketdelivery', 'Tickets')|escape:'html'}&id_ticket={$ticket.id_ticket}" class="btn btn-default button button-small pull-right"><i class="icon-download"></i> {l s='Download'}</a>
                        </div>
                    </div>
                </li>
            {/foreach}
        </ul>
    {else}
        <p class="alert alert-info">{l s='You have not bought tickets yet.'}</p>
    {/if}
</div>