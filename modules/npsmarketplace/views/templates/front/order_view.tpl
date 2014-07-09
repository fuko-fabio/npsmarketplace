{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
        {l s='My account'}
    </a>
    <span class="navigation-pipe">{$navigationPipe}</span>
    <span class="navigation_page">{l s='Order Details' mod='npsmarketplace'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<div class="box">
    <h1 class="page-heading bottom-indent">{l s='Order Details' mod='npsmarketplace'}</h1>
    <div class="row">
        <div class="col-md-6">
            <h4>{l s='Payment state' mod='npsmarketplace'}</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th scope="row">{l s='State' mod='npsmarketplace'}</th>
                        <td>{$order['state']}</td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Payment Method' mod='npsmarketplace'}</th>
                        <td>{$order['payment']}</td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Date & Time' mod='npsmarketplace'}</th>
                        <td>{$order['date_add']}</td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Price' mod='npsmarketplace'}</th>
                        <td>{$order['price']}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <h4>{l s='Delivery Address' mod='npsmarketplace'}</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <td>
                            {$address['firstname']} {$address['lastname']}</br>
                            {if $address['address1']}{$address['address1']}</br>{/if}
                            {if $address['address2']}{$address['address2']}</br>{/if}
                            {$address['postcode']} {$address['city']}</br>
                            {if $address['phone']}{$address['phone']}</br>{/if}
                            {if $address['phone_mobile']}{$address['phone_mobile']}</br>{/if}
                            </br>
                            {if $address['message']}
                            <p class="info-title">{l s='Message' mod='npsmarketplace'}</p>
                            <textarea class="form-control" readonly="">{$address['message']}</textarea>
                            </br>
                            {/if}
                            <p class="info-title">{l s='Map' mod='npsmarketplace'}</p>
                            <div id="map_canvas" style="min-height: 250px;" data-target="{$address['address1']} {$address['address2']} {$address['city']}"></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <h4>{l s='Customer' mod='npsmarketplace'}</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th scope="row">{l s='Firstname' mod='npsmarketplace'}</th>
                        <td>{$customer['firstname']}</td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Payment Method' mod='npsmarketplace'}</th>
                        <td>{$customer['lastname']}</td>
                    </tr>
                    <tr>
                        <th scope="row">{l s='Date & Time' mod='npsmarketplace'}</th>
                        <td><a href="mailto:{$customer['email']}"><i class="icon-envelope"></i> {$customer['email']}</a></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-md-6">
            <h4>{l s='Product' mod='npsmarketplace'}</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <td>

                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<ul class="footer_links clearfix">
    <li>
        <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
            <span>
                <i class="icon-chevron-left"></i> {l s='Back to Your Account'}
            </span>
        </a>
    </li>
    <li>
        <a class="btn btn-default button button-small" href="{$base_dir}">
            <span><i class="icon-chevron-left"></i> {l s='Home'}</span>
        </a>
    </li>
</ul>