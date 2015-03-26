{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{addJsDefL name=tagifyBtnText}{l s='Add' mod='npsvouchers' js=1}{/addJsDefL}
{addJsDefL name=tagifyPlaceholderText}{l s='Type email and press enter' mod='npsvouchers' js=1}{/addJsDefL}
{addJsDefL name=npsVouchersAjaxUrl}{$nps_vouchers_ajax_url}{/addJsDefL}
{addJsDefL name=npsVouchersTitle}{l s='Send vouchers' mod='npsvouchers'}{/addJsDefL}
{addJsDefL name=npsVouchersMsgSuccess}{l s='Sucessfully sent: ' mod='npsvouchers'}{/addJsDefL}
{addJsDefL name=npsVouchersMsgFail}{l s='Failed: ' mod='npsvouchers'}{/addJsDefL}
{capture name=path}
<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='Vouchers' mod='npsvouchers'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
<span class="navigation_page">{l s='Vouchers' mod='npsvouchers'}</span>
{/capture}
<div class="block-center" id="block-seller-vouchers">
    <h1 class="page-heading with-button">{l s='Vouchers' mod='npsvouchers'}<a href="{$new_voucher_link}" onclick="$.fancybox.showLoading();" class="btn btn-default button button-small pull-right"><i class="icon-plus"></i> {l s='Add Voucher' mod='npsvouchers'}</a></h1>
    {include file="$tpl_dir./errors.tpl"}
    {if $vouchers}
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
                    <th class="first_item">{l s='ID' mod='npsvouchers'}</th>
                    <th class="item">{l s='Name' mod='npsvouchers'}</th>
                    <th class="item">{l s='Code' mod='npsvouchers'}</th>
                    <th class="item" data-hide="phone,tablet">{l s='Valid from' mod='npsvouchers'}</th>
                    <th class="item" data-hide="phone,tablet">{l s='Valid to' mod='npsvouchers'}</th>
                    <th class="item">{l s='Quantity' mod='npsvouchers'}</th>
                    <th class="item">{l s='Discount' mod='npsvouchers'}</th>
                    <th class="last_item" data-sort-ignore="true" width="150px" >{l s='Action' mod='npsvouchers'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$vouchers item=voucher}
                <tr>
                    <td>{$voucher.id_cart_rule}</td>
                    <td>{$voucher.name}</td>
                    <td>{$voucher.code}</td>
                    <td>{date('Y-m-d', strtotime($voucher.date_from))}</td>
                    <td>{date('Y-m-d', strtotime($voucher.date_to))}</td>
                    <td>{$voucher.quantity}</td>
                    <td>{if $voucher.reduction_percent > 0}
                            {$voucher.reduction_percent}%
                        {else if $voucher.reduction_amount > 0}
                            {displayPrice price=$voucher.reduction_amount currency=$id_currency}
                        {/if}
                    </td>
                    <td>
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="#" data-toggle="dropdown" class="dropdown-toggle"><i class="icon-list"></i> {l s='Options' mod='npsvouchers'}<b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="{$voucher.edit_url}" onclick="$.fancybox.showLoading();"> <i class="icon-pencil"></i> {l s='Edit' mod='npsvouchers'}</a></li>
                                    <li><a href="#send_vouchers_box_{$voucher.id_cart_rule}" class="send-voucher-btn"> <i class="icon-envelope"></i> {l s='Send' mod='npsvouchers'}</a></li>
                                    <li class="divider"></li>
                                    <li><a href="{$voucher.delete_url}" onclick="$.fancybox.showLoading();"> <i class="icon-trash"></i> {l s='Delete' mod='npsvouchers'}</a></li>
                                </ul>
                            </li>
                        </ul>
                        <!-- Fancybox -->
                        <div style="display:none">
                            <div id="send_vouchers_box_{$voucher.id_cart_rule}" class="send-voucher-box">
                                <h2 class="page-subheading">{l s='Send vouchers' mod='npsvouchers'}<button class="get-tour-button pull-right" type="button" onclick="startVoucherSendTour('.send_voucher_tour_{$voucher.id_cart_rule}');"><i class="icon-info"></i></button></h2>
                                <p id="send_vouchers_error_{$voucher.id_cart_rule}" class="alert alert-error voucher-error"><span class="alert-content">{l s='Unable to send vouchers. Try again or contact with customer support.' mod='npsvouchers'}</span></p>
                                <p id="send_vouchers_email_error_{$voucher.id_cart_rule}" class="alert alert-error voucher-error"><span class="alert-content">{l s='Please provide at least one email address or check (newsletter users)' mod='npsvouchers'}</span></p>
                                <p class="checkbox send_voucher_tour_{$voucher.id_cart_rule}" data-step="1" data-intro="{l s='Select this option if you want send voucher to all labsintown registered users. Only newsletter wanted users will recive an email with voucher code.' mod='npsvouchers'}">
                                    <input type="checkbox" name="allCustomers" id="send_vouchers_input_{$voucher.id_cart_rule}"/>
                                    <label for="send_vouchers_input_{$voucher.id_cart_rule}">{l s='Send to all labsintown registered users. (Newsletter wanted users only)' mod='npsvouchers'}</label>
                                </p>
                                <div class="form-group send_voucher_tour_{$voucher.id_cart_rule}" data-step="2" data-intro="{l s='Here you can provide any email adress to send voucher code. Simply type an email address and press space or enter to add next one.' mod='npsvouchers'}">
                                    <label>{l s='Email adresses' mod='npsvouchers'}</label>
                                    <textarea id="vouchers_emails_{$voucher.id_cart_rule}" class="emails"></textarea>
                                </div>
                                <div class="form-group send_voucher_tour_{$voucher.id_cart_rule}" data-step="3" data-intro="{l s='Here you can type custom message to customer.' mod='npsvouchers'}">
                                    <label for="vouchers_message_{$voucher.id_cart_rule}">{l s='Message to customer' mod='npsvouchers'}</label>
                                    <textarea id="vouchers_message_{$voucher.id_cart_rule}" class="validate form-control" data-validate="isMessage"></textarea>
                                </div>
                                <p class="submit">
                                    <input class="button ccl" type="button" value="{l s='Cancel' mod='npsvouchers'}" onclick="$.fancybox.close();"/>
                                    <input class="button send_voucher_tour_{$voucher.id_cart_rule}" onclick="sendVouchers({$voucher.id_cart_rule});" value="{l s='Send' mod='npsvouchers'}" data-step="4" data-intro="{l s='Thats it! Click this button to send vouchers via email.' mod='npsvouchers'}"/>
                                </p>
                            </div>
                        </div>
                        <!-- End fancybox -->
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
        <p class="alert alert-info"><span class="alert-content">{l s='You have no vouchers.' mod='npsvouchers'}</span></p>
    {/if}
</div>