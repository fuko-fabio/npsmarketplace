{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<div class="row">
    <div class="form-group col-sm-6">
        <label for="product_code">{l s='Delivery email address' mod='npsticketdelivery'}</label>
        <input class="is_required validate form-control" data-validate="isEmail" type="text" name="ticket_destination" value="{if isset($ticket_destination)}{$ticket_destination}{else}{$cookie->email}{/if}"/>
    </div>
</div>
<div class="row">
    <div class="form-group col-sm-6">
        <label for="product_code">{l s='Tickets personal data' mod='npsticketdelivery'}</label>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-bordered footab">
        <thead>
            <tr>
                <th class="first_item" data-sort-ignore="true">{l s='Event name' mod='npsmarketplace'}</th>
                <th class="item">{l s='Term' mod='npsmarketplace'}</th>
                <th class="last_item" data-sort-ignore="true">{l s='Person' mod='npsmarketplace'}</th>
            </tr>
        </thead>
        <tbody>
        {foreach $cart->getProducts() as $product}
            {for $x=1 to $product.quantity}
            <tr>
                <td>{$product.name}</td>
                <td>{$product.attributes}</td>
                <td><input class="is_required validate form-control" data-validate="isGenericName" type="text" name="ticket_person[{$product.id_product}][{$x}]" value="{$cookie->customer_firstname} {$cookie->customer_lastname}"/></td>
            </tr>
            {/for}
        {/foreach}
        </tbody>
    </table>
</div>
