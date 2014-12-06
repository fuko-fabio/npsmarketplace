{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
        {l s='My account'}
    </a>
    <span class="navigation-pipe">{$navigationPipe}</span>
    <span class="navigation_page">{l s='New Event Term'}</span>
{/capture}
<h1 class="page-heading bottom-indent">{l s='New Event Term' mod='npsmarketplace'}</h1>
{include file="$tpl_dir./errors.tpl"}

<div class="block-center" id="block-seller-product-combination">
    <form enctype="multipart/form-data" role="form" action="{$request_uri}" method="post" id="formaddproduct">
        <fieldset>
            <div class="row">
                <div class="col-md-12">
                    <h4>{l s='Event' mod='npsmarketplace'}</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th scope="row">{l s='Name' mod='npsmarketplace'}</th>
                                <td>{$product['name'][$current_id_lang]}</td>
                            </tr>
                            <tr>
                                <th scope="row">{l s='Description' mod='npsmarketplace'}</th>
                                <td>{$product['description_short'][$current_id_lang]}</td>
                            </tr>
                            <tr>
                                <th scope="row">{l s='Price' mod='npsmarketplace'}</th>
                                <td>{$product['price']}</td>
                            </tr>
                            <tr>
                                <th scope="row">{l s='Town' mod='npsmarketplace'}</th>
                                <td>{$product['town']}</td>
                            </tr>
                            <tr>
                                <th scope="row">{l s='District' mod='npsmarketplace'}</th>
                                <td>{$product['district']}</td>
                            </tr>
                            <tr>
                                <th scope="row">{l s='Address' mod='npsmarketplace'}</th>
                                <td>{$product['address']}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div id="datePicker" class="form-group col-md-6 input-append">
                    <label class="required" for="date_input">{l s='Date' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isDate" id="date_input" name="date" data-format="yyyy-MM-dd" type="text" required=""
                        value="{if isset($smarty.post.date)}{$smarty.post.date}{else}{if isset($product['date'])}{$product['date']|escape:'html':'UTF-8'}{/if}{/if}"/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
                <div id="timePicker" class="form-group col-md-6 input-append">
                    <label class="required" for="time_input">{l s='Time' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isTime" id="time_input" name="time" data-format="hh:mm" type="text" required=""
                        value="{if isset($smarty.post.time)}{$smarty.post.time}{else}{if isset($product['time'])}{$product['time']|escape:'html':'UTF-8'}{/if}{/if}"/>
                    <span class="form_info">{l s='Format: HH:MM' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label class="required" for="product_amount">{l s='Quantity' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isInteger" type="number" id="product_amount" name="quantity" required=""
                        value="{if isset($smarty.post.quantity)}{$smarty.post.quantity}{else}{if isset($product['quantity'])}{$product['quantity']|escape:'html':'UTF-8'}{/if}{/if}"/>
                </div>
                <div id="availableDatePicker" class="form-group col-md-6 input-append">
                    <label class="required" for="date_input">{l s='Available Date' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isDate" id="expiry_date_input" name="expiry_date" data-format="yyyy-MM-dd" type="text" required=""
                        value="{if isset($smarty.post.expiry_date)}{$smarty.post.expiry_date}{else}{if isset($product['expiry_date'])}{$product['expiry_date']|escape:'html':'UTF-8'}{/if}{/if}"/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
        </fieldset>
        </br>
        <strong>{l s='By clicking "Add" I accept the' mod='npsmarketplace'} <a href="{$user_agreement_url}">{l s='User Agreement.' mod='npsmarketplace'}</a></strong>
        </br>
        <button type="submit" name="submitCombination" class="btn btn-default button button-medium pull-right" onclick="$.fancybox.showLoading();"><span>{l s='Add' mod='npsmarketplace'} <i class="icon-plus right"></i></span></button>
    </form>
</div>