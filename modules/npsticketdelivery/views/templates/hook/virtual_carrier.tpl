{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<div class="row">
    <div class="form-group col-sm-6">
        <label for="product_code">{l s='Delivery email address' mod='npsticketdelivery'}</label>
        <input class="is_required validate form-control" data-validate="isEmail" type="text" name="ticket_destination" value="{if isset($ticket_destination)}{$ticket_destination}{else}{$cookie->email}{/if}"/>
        <span class="form_info">{l s='You can send the tickets to a different address. Suitable for gifts. Tickets also will be sent to your e-mail address.' mod='npsticketdelivery'} <a target="_blank" href="$send_tickets_info_url">{l s='See more informations' mod='npsticketdelivery'}</a></span>
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
                <th class="first_item" data-sort-ignore="true">{l s='Event name' mod='npsticketdelivery'}</th>
                <th class="item">{l s='Additional information' mod='npsticketdelivery'}</th>
                <th class="last_item" data-sort-ignore="true">{l s='Person' mod='npsticketdelivery'} <a class="link" target="_blank" href="$send_tickets_info_url">{l s='See more informations' mod='npsticketdelivery'}</a></th>
            </tr>
        </thead>
        <tbody>
        {foreach $cart->getProducts() as $product}
            {for $x=1 to $product.quantity}
            <tr>
                <td>{$product.name}</td>
                <td>{if isset($product.attributes)}{$product.attributes}{/if}</td>
                <td><input class="is_required validate form-control" data-validate="isGenericName" type="text" name="ticket_person[{$product.id_product}][{$x}]" value="{$cookie->customer_firstname} {$cookie->customer_lastname}"/></td>
            </tr>
            {/for}
        {/foreach}
        </tbody>
    </table>
</div>
