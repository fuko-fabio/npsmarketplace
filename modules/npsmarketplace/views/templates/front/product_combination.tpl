{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
        {l s='My account'}
    </a>
    <span class="navigation-pipe">{$navigationPipe}</span>
    <span class="navigation_page">{l s='New Event Term'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='New Event Term' mod='npsmarketplace'}</h1>

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
                <div class="form-group col-md-6">
                    <label class="required" for="date_time_input">{l s='New Date & Time' mod='npsmarketplace'}</label>
                    <div id="datePicker" class="input-append">
                        <input class="is_required form-control" id="date_time_input" name="date_time" data-format="yyyy-MM-dd hh:mm" type="text" readonly="" required="" value="{if isset($product['date_time'])}{$product['date_time']|escape:'html':'UTF-8'}{/if}"/>
                        <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label class="required" for="product_amount">{l s='Quantity' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isNumber" type="number" id="product_amount" name="quantity" required=""/>
                </div>
            </div>
        </fieldset>
        </br>
        <strong>{l s='By clicking "Add" I accept the' mod='npsmarketplace'} <a href="{$user_agreement_url}">{l s='User Agreement.' mod='npsmarketplace'}</a></strong>
        </br>
        <button type="submit" class="btn btn-primary btn-lg pull-right"><span>{l s='Add' mod='npsmarketplace'} <i class="icon-plus right"></i></span></button>
    </form>
</div>