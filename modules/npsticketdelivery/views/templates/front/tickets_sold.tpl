{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<script type="text/javascript">
    var exportEentsList = {json_encode($export_events_list)};
    var dictAll = '{l s='All' mod='npsticketdelivery'}';
    var dictCarnet = '{l s='Carnet' mod='npsticketdelivery'}';
</script>
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Tickets Sold' mod='npsticketdelivery'}</span>
{/capture}
<div class="block-center"  id="nps_tickets_block">
    <h1 class="page-heading with-button">{l s='Tickets Sold' mod='npsticketdelivery'}{if $tickets}<a href="#export_tickets" class="btn btn-default button button-small pull-right export-list-btn"><i class="icon-download"></i> {l s='Eksport list' mod='npsticketdelivery'}</a>{/if}</h1>
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
                    <th class="item">{l s='Combination' mod='npsticketdelivery'}</th>
                    <th class="item">{l s='Code' mod='npsticketdelivery'}</th>
                    <th class="item">{l s='Price' mod='npsticketdelivery'}</th>
                    <th class="item" data-hide="phone,tablet">{l s='Gift' mod='npsticketdelivery'}</th>
                    <th class="item">{l s='Person' mod='npsticketdelivery'}</th>
                    <th class="item" data-hide="phone,tablet">{l s='Address' mod='npsticketdelivery'}</th>
                    <th class="item" data-hide="phone,tablet">{l s='District' mod='npsticketdelivery'}</th>
                    <th class="item" data-hide="phone,tablet">{l s='Town' mod='npsticketdelivery'}</th>
                    <th class="item" data-hide="phone,tablet">{l s='Term' mod='npsticketdelivery'}</th>
                    <th class="item" data-hide="phone,tablet">{l s='Question/Answer' mod='npsticketdelivery'}</th>
                    <th class="item">{l s='Type' mod='npsticketdelivery'}</th>
                    <th class="last_item" data-sort-ignore="true" width="100px" >{l s='Action' mod='npsticketdelivery'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$tickets item=ticket}
                <tr>
                    <td>{$ticket.id_ticket}</td>
                    <td>{$ticket.name|escape:'html':'UTF-8'}</td>
                    <td>{$ticket.combination_name|escape:'html':'UTF-8'}</td>
                    <td>{$ticket.code}</td>
                    <td>{displayPrice price=$ticket.price currency=$ticket.id_currency}</td>
                    <td>{if $ticket.gift == 1}
                        {l s='Yes' mod='npsticketdelivery'}
                        {else}
                        {l s='No' mod='npsticketdelivery'}
                        {/if}
                    </td>
                    <td>{$ticket.person|escape:'html':'UTF-8'}</td>
                    <td>{$ticket.address|escape:'html':'UTF-8'}</td>
                    <td>{$ticket.district|escape:'html':'UTF-8'}</td>
                    <td>{$ticket.town|escape:'html':'UTF-8'}</td>
                    <td>
                    {if $ticket.type == 'ticket'}
                        {date_format(date_create($ticket.date), 'Y-m-d H:i')}
                    {/if}
                    </td>
                    <td>
                    {if isset($ticket.questions) && !empty($ticket.questions)}
                    {foreach from=$ticket.questions item=q}
                        <strong>{$q.question|escape:'html':'UTF-8'}</strong><br />{$q.answer|escape:'html':'UTF-8'}<br />
                    {/foreach}
                    {/if}
                    </td>
                    <td>
                        {if $ticket.type == 'ticket'}
                        {l s='Ticket' mod='npsticketdelivery'}
                        {else if $ticket.type == 'carnet'}
                        {l s='Carnet' mod='npsticketdelivery'}
                        {/if}
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="{$ticket.order_url}" class="edit btn btn-default"><i class="icon-search"></i> {l s='View' mod='npsticketdelivery'}</a>
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
    <div style="display:none">
      <div id="export_tickets">
        <h2 class="page-subheading">{l s='Export participants list' mod='npsticketdelivery'}</h2>
        <form id="export_tickets_form" action="{$request_uri}" method="post">
          <input type="hidden" name="action" value="export" />
          <div class="row">
            <div class="form-group col-md-6">
              <label class="required">{l s='Event' mod='npsticketdelivery'}</label>
              <select class="form-control" name="name">
              </select>
            </div>
            <div class="form-group col-md-6">
              <label class="required">{l s='Date' mod='npsticketdelivery'}</label>
              <select class="form-control" name="date">
              </select>
            </div>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label class="required">{l s='File type' mod='npsticketdelivery'}</label>
              <select class="form-control" name="filetype">
                  <option value="excel" selected="selected">{l s='Excel' mod='npsticketdelivery'}</option>
                  <option value="pdf">{l s='PDF' mod='npsticketdelivery'}</option>
              </select>
            </div>
            <div class="form-group col-md-6 checkbox">
              <label>
                <input type="checkbox" name="questions" value="1" />
                {l s='Include customers answers'  mod='npsticketdelivery'} </label>
            </div>
          </div>
          <p class="submit">
            <input class="button ccl" type="button" value="{l s='Cancel' mod='npsticketdelivery'}" onclick="$.fancybox.close();"/>
            <input class="button" type="submit" value="{l s='Export' mod='npsticketdelivery'}" onclick="$.fancybox.close();"/>
          </p>
        </form>
      </div>
    </div>
    {else}
        <p class="alert alert-info"><span class="alert-content">{l s='You have no tickets sold.' mod='npsticketdelivery'}</span></p>
    {/if}
</div>

