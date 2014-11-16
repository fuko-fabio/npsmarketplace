{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<div class="row">
    <div class="form-group col-sm-6">
        <label for="product_code">{l s='Delivery email address' mod='npsticketdelivery'}</label>
        <input class="validate form-control" data-validate="isEmail" type="text" name="ticket_destination" value="{if isset($ticket_destination)}{$ticket_destination}{else}{$cookie->email}{/if}"/>
    </div>
</div>