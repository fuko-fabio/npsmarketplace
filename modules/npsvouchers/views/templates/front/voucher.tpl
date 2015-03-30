{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2015 npsoftware
*}
{addJsDefL name=voucherPrefix}{l s='Voucher: ' mod='npsvouchers' js=1}{/addJsDefL}
{capture name=path}

<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"> {l s='My account' mod='npsvouchers'} </a>
<span class="navigation-pipe">{$navigationPipe}</span>
{if isset($voucher.id)}
<span class="navigation_page">{l s='Edit Voucher' mod='npsvouchers'}</span>
{else}
<span class="navigation_page">{l s='Add Voucher' mod='npsvouchers'}</span>
{/if}
{/capture}
{if isset($voucher.id)}
<h1 class="page-heading bottom-indent">{l s='Edit Voucher' mod='npsvouchers'}{if isset($voucher.name)}: {$voucher.name}{/if}</h1>
{else}
<h1 class="page-heading with-button">{l s='Add Voucher' mod='npsvouchers'}<button class="get-tour-button pull-right" type="button" onclick="startVoucherTour();" data-step="10" data-intro="{l s='If something will be not clear you can run again this tour at any time. Good luck!' mod='npsvouchers'}"><i class="icon-info"></i></button></h1>
{/if}
{include file="$tpl_dir./errors.tpl"}
<div class="block-center" id="block-seller-voucher">
    <form role="form" action="{$request_uri}" method="post" id="edit-voucher-form">
        <div class="row">
            <div class="col-md-6" data-step="1" data-intro="{l s='First select product for vouchers.' mod='npsvouchers'}">
                <div class="form-group">
                    <label for="voucher_product" class="required">{l s='Product' mod='npsvouchers'}</label>
                    <select class="is_required validate form-control" id="voucher_product" name="id_product" {if isset($voucher.id)}disabled=""{/if}>
                        {foreach from=$products item=product}
                            <option value="{$product.id_product}" data-quantity="{$product.quantity}" data-date-to="{date('Y-m-d', strtotime($product.date_to))}" data-display-price="{displayPrice price=$product.price currency=$id_currency}" data-price="{$product.price}" {if isset($smarty.post.id_product) && $smarty.post.id_product eq $product.id_product}selected{else}{if isset($voucher.id_product) && $voucher.id_product eq $product.id_product}selected{/if}{/if}>{$product.name}</option> 
                        {/foreach}
                    </select>
                    <span class="form_info">{l s='Target product' mod='npsvouchers'}</span>
                </div>
            </div>
            <div class="col-md-6" data-step="2" data-intro="{l s='Define number of vouchers. By default value will be set to number of available products.' mod='npsvouchers'}">
                <div class="form-group">
                    <label class="required">{l s='Vouchers quantity' mod='npsvouchers'}</label>
                    <input class="is_required validate form-control" data-validate="isQuantity" type="number" name="quantity" required=""
                       value="{if isset($smarty.post.quantity)}{$smarty.post.quantity}{else}{if isset($voucher.quantity)}{$voucher.quantity}{/if}{/if}"/>
                    <span class="form_info">{l s='Number of available vouchers' mod='npsvouchers'}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6" data-step="3" data-intro="{l s='Give the voucher name. Name will be visible for customers. Example: Joga 20% off!' mod='npsvouchers'}">
                <div class="form-group">
                    <label class="required">{l s='Voucher name' mod='npsvouchers'}</label>
                    <input class="is_required validate form-control" data-validate="isGenericName" type="text" name="name" required=""
                       value="{if isset($smarty.post.name)}{$smarty.post.name}{else}{if isset($voucher.name)}{$voucher.name}{/if}{/if}"/>
                    <span class="form_info">{l s='Visible on cart summary and customer bill' mod='npsvouchers'}</span>
                </div>
            </div>
            <div class="col-md-6" data-step="4" data-intro="{l s='This is voucher code! Send this code later to users to give them discount. You can click \'Generate\' button to generate new code.' mod='npsvouchers'}">
                <label class="required">{l s='Code' mod='npsvouchers'}</label>
                {if !isset($voucher.id)}
                <div class="row">
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input class="is_required validate form-control" data-validate="isGenericName" type="text" name="code" required=""
                                value="{if isset($smarty.post.code)}{$smarty.post.code}{else}{if isset($voucher.code)}{$voucher.code}{/if}{/if}"/>
                            <span class="form_info">{l s='Voucher code to be used by customer' mod='npsvouchers'}</span>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <button type="button" class="btn btn-default button button-small pull-right" onclick="gencode();">{l s='Generate' mod='npsvouchers'}</button>
                    </div>
                </div>
                {else}
                    <div class="form-group">
                        <input class="form-control" type="text" name="code" readonly=""
                            value="{if isset($smarty.post.code)}{$smarty.post.code}{else}{if isset($voucher.code)}{$voucher.code}{/if}{/if}"/>
                        <span class="form_info">{l s='Voucher code to be used by customer' mod='npsvouchers'}</span>
                    </div>
                {/if}
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6 input-append datetimepicker-from" data-step="5" data-intro="{l s='Voucher validity start date.' mod='npsvouchers'}" >
                <label class="required">{l s='Valid from' mod='npsvouchers'}</label>
                <input class="is_required validate form-control" data-validate="isDate" name="from" data-format="yyyy-MM-dd" type="text" required=""
                    value="{if isset($smarty.post.from)}{$smarty.post.from}{else}{if isset($voucher.from)}{$voucher.from}{/if}{/if}"/>
                <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsvouchers'}</span>
                <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
            </div>
            <div class="form-group col-md-6 input-append datetimepicker-to" data-step="6" data-intro="{l s='Voucher validity end date' mod='npsvouchers'}">
                <label class="required">{l s='Valid to' mod='npsvouchers'}</label>
                <input class="is_required validate form-control" data-validate="isDate" name="to" data-format="yyyy-MM-dd" type="text" required=""
                    value="{if isset($smarty.post.to)}{$smarty.post.to}{else}{if isset($voucher.to)}{$voucher.to}{/if}{/if}"/>
                <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsvouchers'}</span>
                <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6" data-step="7" data-intro="{l s='Here you can select discount type. You can provide percentage discount or orice discount.' mod='npsvouchers'}">
                <label class="required">{l s='Applay discount' mod='npsvouchers'}</label>
                <ul>
                    <li>
                        <label class="top">
                            <input type="radio" name="type" value="percent" {if isset($smarty.post.type) && $smarty.post.type eq 'percent'}checked=""{else}{if !isset($voucher.type) || $voucher.type eq 'percent'}checked=""{/if}{/if}/>
                            {l s='Percentage' mod='npsmarketplace'}
                        </label>
                    </li>
                    <li>
                        <label class="top">
                            <input type="radio" name="type" value="price" {if isset($smarty.post.type) && $smarty.post.type eq 'price'}checked=""{else}{if isset($voucher.type) && $voucher.type eq 'price'}checked=""{/if}{/if}/>
                            {l s='Price' mod='npsvouchers'}
                        </label>
                    </li>
                </ul>
            </div>
            <div class="col-md-6" data-step="8" data-intro="{l s='Depending on previous selection provide here percentage discount value or price discount value.' mod='npsvouchers'}">
                <div class="form-group">
                    <label class="required">
                        <span class="percent">{l s='Percent discount' mod='npsvouchers'}</span>
                        <span class="price">{l s='Price discount' mod='npsvouchers'}</span>
                    </label>
                    <input class="is_required validate form-control" data-validate="isPercent" type="text" name="discount" required=""
                       value="{if isset($smarty.post.discount)}{$smarty.post.discount}{else}{if isset($voucher.discount)}{$voucher.discount}{/if}{/if}"/>
                    <span class="form_info percent">{l s='Product price will be reduced by provided percent value' mod='npsvouchers'}</span>
                    <span class="form_info price">{l s='Product price will be reduced by provided value' mod='npsvouchers'}</span>
                </div>
            </div>
        </div>
        <div class="hint" data-step="9" data-intro="{l s='Before you add new voucher here you can check if discount value is valid.' mod='npsvouchers'}">
            {l s='Orginal product price' mod='npsvouchers'}: <strong class="product-price"></strong><br />
            {l s='Price after discount' mod='npsvouchers'}: <strong class="discount-price"></strong>
        </div>
        {if !isset($voucher.id)}
        <div class="row">
            <div class="col-xs-12">
            {l s='By clicking "Add" I accept the' mod='npsvouchers'} <a href="{$user_agreement_url}">{l s='User Agreement.' mod='npsvouchers'}</a>
            </div>
        </div>
        {/if}
        <div class="form-buttons">
            {if isset($voucher.id)}
            <a href="{$delete_url}" class="btn btn-default button button-medium ccl pull-left"><span>{l s='Delete' mod='npsvouchers'} <i class="icon-trash right"></i></span></a>
            {/if}
            <button type="submit" class="btn btn-default button button-medium pull-right" name="saveVoucher" data-step="11" data-intro="{l s='Thats it! Your voucher is redy. Save it and send voucher code to customers.' mod='npsvouchers'}">
                <span>
                {if isset($voucher.id)}
                    {l s='Save' mod='npsvouchers'} <i class="icon-save right"></i>
                {else}
                    {l s='Add' mod='npsvouchers'} <i class="icon-plus right"></i>
                {/if}
                </span>
            </button>
            <a href="{$back_url}" class="btn btn-default button button-medium ccl pull-right"><span>{l s='Cancel' mod='npsvouchers'}</span></a>
        </div>
    </form>
</div>