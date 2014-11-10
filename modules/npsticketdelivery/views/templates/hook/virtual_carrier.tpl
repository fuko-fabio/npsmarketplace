{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<div class="row">
    <div class="form-group col-sm-6">
        <label for="product_code">{l s='Delivery email address' mod='npsmarketplace'}</label>
        <input class="validate form-control" data-validate="isEmail" type="text" name="ticket_destination" value="{$cookie->email}"/>
        <div class="checkbox">
            <label for="recyclable">
                <input type="checkbox" name="gift_ticket"/>
                {l s='I would like my order to be gift wrapped.' mod='npsmarketplace'}
            </label>
        </div>
    </div>
</div>
