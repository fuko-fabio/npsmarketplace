{*
* @author Norbert Pabian <norbert.pabian@gmail.com>
* @copyright 2014 npsoftware
*}
{capture name=path}{l s='Events calendar' mod='npscalendar'}{/capture}

<h1 class="page-heading product-listing">{l s='Events' mod='npscalendar'}</h1>

{if $products}
    <div class="content_sortPagiBar">
        <div class="sortPagiBar clearfix">
            {include file="./product-sort.tpl"}
            {include file="./nbr-product-page.tpl"}
        </div>
        <div class="top-pagination-content clearfix">
            {include file="./product-compare.tpl"}
            {include file="$tpl_dir./pagination.tpl"}
        </div>
    </div>

    {include file="./product-list.tpl" products=$products}

    <div class="content_sortPagiBar">
        <div class="bottom-pagination-content clearfix">
            {include file="./product-compare.tpl"}
            {include file="./pagination.tpl" paginationId='bottom'}
        </div>
    </div>
    {else}
    <p class="alert alert-warning"><span class="alert-content">{l s='No events' mod='npscalendar'}</span></p>
{/if}
