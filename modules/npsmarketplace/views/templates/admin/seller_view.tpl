<!--
    @author Norbert Pabian <norbert.pabian@gmail.com>
    @copyright 2014 npsoftware
-->
<div id="container-seller">
    <div class="row">
        {*left*}
        <div class="col-lg-6">
            <div class="panel clearfix">
                <div class="panel-heading">
                    <i class="icon-user"></i>
                    {$seller->name}
                    [{$seller->id|string_format:"%06d"}]
                    -
                    <a href="mailto:{$seller->email}"><i class="icon-envelope"></i>
                        {$seller->email}
                    </a>
                    <div class="panel-heading-action">
                        <a class="btn btn-default" href="index.php?controller=AdminSellersAccounts&id_seller={$seller->id}&updateseller&token={$token}">
                            <i class="icon-edit"></i>
                            {l s='Edit' mod='npsmarketplace'}
                        </a>
                    </div>
                </div>
                <div class="form-horizontal">
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Company Name' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$seller->company_name}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Commision' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$seller->commision}%</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Phone' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$seller->phone}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Registration Date' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$seller->request_date}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='NIP' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$seller->nip}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='REGON' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$seller->regon}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Status' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                {if $seller->active}
                                    <span class="label label-success">
                                        <i class="icon-check"></i>
                                        {l s='Active' mod='npsmarketplace'}
                                    </span>
                                {else}
                                    <span class="label label-danger">
                                        <i class="icon-remove"></i>
                                        {l s='Inactive' mod='npsmarketplace'}
                                    </span>
                                {/if}
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Locked' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                {if !$seller->locked}
                                    <span class="label label-success">
                                        <i class="icon-check"></i>
                                        {l s='No' mod='npsmarketplace'}
                                    </span>
                                {else}
                                    <span class="label label-danger">
                                        <i class="icon-remove"></i>
                                        {l s='Yes' mod='npsmarketplace'}
                                    </span>
                                {/if}
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Show Company Regualtions' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                {if $seller->regulations_active}
                                    <span class="label label-success">
                                        <i class="icon-check"></i>
                                        {l s='Yes' mod='npsmarketplace'}
                                    </span>
                                {else}
                                    <span class="label label-danger">
                                        <i class="icon-remove"></i>
                                        {l s='No' mod='npsmarketplace'}
                                    </span>
                                {/if}
                            </p>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Company Description' mod='npsmarketplace'}</label>
                    </div>
                    <div class="row">
                        <p class="form-control-static">{$seller->company_description[$lang_id]}</p>
                    </div>
                </div>
            </div>
        </div>
        {*right*}
        <div class="col-lg-6">
            <div class="panel clearfix">
                <div class="panel-heading">
                    <i class="icon-money"></i>
                    {l s='Payment Settings' mod='npsmarketplace'}
                    -
                    <a href="mailto:{$seller->email}"><i class="icon-envelope"></i>
                        {$payment->email}
                    </a>
                    <div class="panel-heading-action">
                        <a class="btn btn-default" href="index.php?controller=AdminSellerCompany&id_p24_seller_company={$payment->id}&viewp24_seller_company&token={$payment_token}">
                            <i class="icon-edit"></i>
                            {l s='View' mod='npsmarketplace'}
                        </a>
                    </div>
                </div>
                <div class="form-horizontal">
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='SPID' mod='npsmarketplace'}<small>{l s='Seller Przelewy24 ID' mod='npsmarketplace'}</small></label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$payment->spid}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Registration Date' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$payment->registration_date}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Company Name' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$payment->company_name}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Person' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$payment->person}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='City'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$payment->city}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Street' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$payment->street}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Post Code' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$payment->post_code}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='NIP' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$payment->nip}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='REGON' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$payment->regon}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Bank Account' mod='npsmarketplace'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">{$payment->iban}</p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="control-label col-lg-3">{l s='Przelewy24 Regualtions'}</label>
                        <div class="col-lg-9">
                            <p class="form-control-static">
                                {if $payment->acceptance}
                                    <span class="label label-success">
                                        <i class="icon-check"></i>
                                        {l s='Accepted'}
                                    </span>
                                {else}
                                    <span class="label label-danger">
                                        <i class="icon-remove"></i>
                                        {l s='Not Accepted'}
                                    </span>
                                {/if}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="container-products">
    <div class="row">
        <div class="col-lg-12">
            <form class="container-command-top-spacing">
                <div class="panel">
                    <div class="panel-heading">
                        <i class="icon-shopping-cart"></i>
                        {l s='Products' mod=npsmarketplace} <span class="badge">{$products|@count}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="orderProducts">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th><span class="title_box ">{l s='Product' mod=npsmarketplace}</span></th>
                                    <th><span class="title_box ">{l s='Description' mod=npsmarketplace}</span></th>
                                    <th><span class="title_box ">{l s='Unit Price' mod=npsmarketplace}</th>
                                    <th class="text-center"><span class="title_box ">{l s='Qty' mod='npsmarketplace'}</span></th>
                                    <th class="text-center"><span class="title_box ">{l s='Active' mod='npsmarketplace'}</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$products item=product key=k}
                                <tr class="product-line-row">
                                    <td>
                                    {if $product['haveImage']}
                                        <img src="{$product['cover']}" class="imgm img-thumbnail" width="52"/>
                                    {else}
                                        <img src="{$img_prod_dir}{$lang_iso}-default-cart_default.jpg" class="imgm img-thumbnail" width="52"/>
                                    {/if}
                                    <td><a href="index.php?controller=adminproducts&amp;id_product={$product['id']}&amp;updateproduct&amp;token={getAdminToken tab='AdminProducts'}"> <span class="productName">{$product['name']}</span>
                                    <br />
                                    <td>{$product['description']}</td>
                                    <td>
                                    <span class="product_price_show">{displayPrice price=$product['price'] currency=$currency->id}</span></td>
                                    <td class="productQuantity text-center"><span class="product_quantity_show{if (int)$product['quantity'] > 1} badge{/if}">{$product['quantity']}</span></td>
                                    <td class="text-center">
                                        <a class="list-action-enable {if $product['active']}action-enabled{else}action-disabled{/if}" href="{$product['active_url']}">
                                        {if $product['active']}
                                            <i class="icon-check"></i>
                                        {else}
                                            <i class="icon-remove"></i>
                                        {/if}
                                        </a>
                                    </td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
