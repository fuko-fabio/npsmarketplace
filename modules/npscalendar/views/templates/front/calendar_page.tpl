{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
{addJsDefL name=calendarApiUrl}{$calendar_api_url}{/addJsDefL}
{addJsDefL name=calendarCurrentDate}{$current_calendar_date}{/addJsDefL}

{capture name=path}{l s='Events calendar' mod='npscalendar'}{/capture}
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-5">{$HOOK_CALENDAR}</div>
    <div class="col-xs-12 col-sm-12 col-md-7">
        <h1 class="page-heading product-listing">{l s='Events' mod='npscalendar'}</h1>
        {if $products}
            <div class="content_sortPagiBar">
                <div class="sortPagiBar clearfix">
                    {include file="$tpl_dir./product-sort.tpl"}
                    {include file="$tpl_dir./nbr-product-page.tpl"}
                </div>
                <div class="top-pagination-content clearfix">
                    {include file="$tpl_dir./product-compare.tpl"}
                    {include file="$tpl_dir./pagination.tpl"}
                </div>
            </div>
            {include file="$tpl_dir./product-list.tpl" products=$products}
            <div class="content_sortPagiBar">
                <div class="bottom-pagination-content clearfix">
                    {include file="$tpl_dir./product-compare.tpl" paginationId='bottom'}
                    {include file="$tpl_dir./pagination.tpl" paginationId='bottom'}
                </div>
            </div>
            {else}
            <p class="alert alert-warning"><span class="alert-content">{l s='No events' mod='npscalendar'}</span></p>
        {/if}
    </div>
</div>
