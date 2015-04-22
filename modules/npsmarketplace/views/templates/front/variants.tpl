{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}

<div style="display:none">
  <div id="ticket_combination" class="event-combination">
    <h2 class="page-subheading">{l s='New ticket' mod='npsmarketplace'}</h2>
    <form id="ticket_combination_form">
      <input type="hidden" name="type" value="ticket" />
      <div class="row">
        <div class="col-md-6 form-group">
          <label class="required">{l s='Name' mod='npsmarketplace'}</label>
          <input class="is_required validate form-control" data-validate="isGenericName" type="text" name="name"/>
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-6">
          <label class="required">{l s='Event date and time' mod='npsmarketplace'}</label>
          <input id="ticket_date" class="is_required validate form-control" data-validate="isDateTime" name="date" type="text"/>
          <span class="form_info">{l s='Format: YYYY-MM-DD HH:MM' mod='npsmarketplace'}</span>
        </div>
        <div class="form-group col-md-6">
          <label class="required">{l s='Expiration date end time' mod='npsmarketplace'}</label>
          <input id="ticket_expiry_date" class="is_required validate form-control" data-validate="isDateTime" name="expiry_date" type="text"/>
          <span class="form_info">{l s='Format: YYYY-MM-DD HH:MM' mod='npsmarketplace'}</span>
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-6">
          <label class="required">{l s='Price' mod='npsmarketplace'}</label>
          <input class="is_required validate form-control" data-validate="isPrice" type="text" name="price" required="" />
          <span class="form_info">{l s='Final price visible for customers. Example: 120.50' mod='npsmarketplace'}</span>
        </div>
        <div class="form-group col-md-6">
          <label class="required">{l s='Quantity' mod='npsmarketplace'}</label>
          <input class="is_required validate form-control" data-validate="isQuantity" type="number" name="quantity" required="" />
          <span class="form_info">{l s='Warning: Check quantity before submit' mod='npsmarketplace'}</span>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 checkbox">
          <label>
            <input type="checkbox" name="add_reduction" value="1" />
            {l s='I want add specific price for selected period of time'  mod='npsmarketplace'} </label>
        </div>
      </div>
      <div class="row reduction">
        <div class="form-group col-md-6">
          <label class="required">{l s='Reduction' mod='npsmarketplace'}</label>
          <input class="validate form-control" data-validate="isPrice" type="text" name="reduction"/>
          <span class="form_info">{l s='Example: 10.50' mod='npsmarketplace'}</span>
        </div>
      </div>
      <div class="row reduction">
        <div class="form-group col-md-6">
          <label class="required">{l s='Applay from' mod='npsmarketplace'}</label>
          <input id="ticket_from" class="validate form-control" data-validate="isDateTime" name="from" type="text"/>
          <span class="form_info">{l s='Format: YYYY-MM-DD HH:MM' mod='npsmarketplace'}</span>
        </div>
        <div class="form-group col-md-6">
          <label class="required">{l s='to' mod='npsmarketplace'}</label>
          <input id="ticket_to" class="validate form-control" data-validate="isDateTime" name="to" type="text"/>
          <span class="form_info">{l s='Format: YYYY-MM-DD HH:MM' mod='npsmarketplace'}</span>
        </div>
      </div>
    </form>

    <p class="submit">
      <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="closeVariantBox();"/>
      <input class="button" onclick="addVariant('#ticket_combination_form');" value="{l s='Add' mod='npsmarketplace'}"/>
    </p>
  </div>
</div>

<div style="display:none">
  <div id="carnet_combination" class="event-combination">
    <h2 class="page-subheading">{l s='New carnet' mod='npsmarketplace'}</h2>
    <form id="carnet_combination_form">
      <input type="hidden" name="type" value="carnet" />
      <div class="row">
        <div class="col-md-6 form-group">
          <label class="required">{l s='Name' mod='npsmarketplace'}</label>
          <input class="is_required validate form-control" data-validate="isGenericName" type="text" name="name"/>
        </div>
        <div class="form-group col-md-6">
          <label class="required">{l s='Expiration date end time' mod='npsmarketplace'}</label>
          <input id="carnet_expiry_date" class="is_required validate form-control" data-validate="isDateTime" name="expiry_date" type="text"/>
          <span class="form_info">{l s='Format: YYYY-MM-DD HH:MM' mod='npsmarketplace'}</span>
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-6">
          <label class="required">{l s='Price' mod='npsmarketplace'}</label>
          <input class="is_required validate form-control" data-validate="isPrice" type="text" name="price" required="" />
          <span class="form_info">{l s='Final price visible for customers. Example: 120.50' mod='npsmarketplace'}</span>
        </div>
        <div class="form-group col-md-6">
          <label class="required">{l s='Quantity' mod='npsmarketplace'}</label>
          <input class="is_required validate form-control" data-validate="isQuantity" type="number" name="quantity" required="" />
          <span class="form_info">{l s='Warning: Check quantity before submit' mod='npsmarketplace'}</span>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 checkbox">
          <label>
            <input type="checkbox" name="add_reduction" value="1" />
            {l s='I want add specific price for selected period of time'  mod='npsmarketplace'} </label>
        </div>
      </div>
      <div class="row reduction">
        <div class="form-group col-md-6">
          <label class="required">{l s='Reduction' mod='npsmarketplace'}</label>
          <input class="validate form-control" data-validate="isPrice" type="text" name="reduction"/>
          <span class="form_info">{l s='Example: 10.50' mod='npsmarketplace'}</span>
        </div>
      </div>
      <div class="row reduction">
        <div class="form-group col-md-6">
          <label class="required">{l s='Applay from' mod='npsmarketplace'}</label>
          <input id="carnet_from" class="validate form-control" data-validate="isDateTime" name="from" type="text"/>
          <span class="form_info">{l s='Format: YYYY-MM-DD HH:MM' mod='npsmarketplace'}</span>
        </div>
        <div class="form-group col-md-6">
          <label class="required">{l s='to' mod='npsmarketplace'}</label>
          <input id="carnet_to" class="validate form-control" data-validate="isDateTime" name="to" type="text"/>
          <span class="form_info">{l s='Format: YYYY-MM-DD HH:MM' mod='npsmarketplace'}</span>
        </div>
      </div>
    </form>

    <p class="submit">
      <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="closeVariantBox();"/>
      <input class="button" onclick="addVariant('#carnet_combination_form');" value="{l s='Add' mod='npsmarketplace'}"/>
    </p>
  </div>
</div>

<div style="display:none">
  <div id="ad_combination" class="event-combination">
    <h2 class="page-subheading">{l s='New advertisment' mod='npsmarketplace'}</h2>
    <form id="ad_combination_form">
      <input type="hidden" name="type" value="ad" />
      <div class="row">
        <div class="form-group col-md-6">
          <label class="required">{l s='Event date and time' mod='npsmarketplace'}</label>
          <input id="ad_date" class="is_required validate form-control" data-validate="isDateTime" name="date" type="text"/>
          <span class="form_info">{l s='Format: YYYY-MM-DD HH:MM' mod='npsmarketplace'}</span>
        </div>
        <div class="form-group col-md-6">
          <label class="required">{l s='Expiration date end time' mod='npsmarketplace'}</label>
          <input id="ad_expiry_date" class="is_required validate form-control" data-validate="isDateTime" name="expiry_date" type="text"/>
          <span class="form_info">{l s='Format: YYYY-MM-DD HH:MM' mod='npsmarketplace'}</span>
        </div>
      </div>
    </form>

    <p class="submit">
      <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="closeVariantBox();"/>
      <input class="button" onclick="addVariant('#ad_combination_form');" value="{l s='Add' mod='npsmarketplace'}"/>
    </p>
  </div>
</div>

<div style="display:none">
  <div id="outer_ad_combination" class="event-combination">
    <h2 class="page-subheading">{l s='New outer advertisment' mod='npsmarketplace'}</h2>
    <form id="outer_ad_combination_form">
      <input type="hidden" name="type" value="externalad" />
      <div class="row">
        <div class="form-group col-md-6">
          <label class="required">{l s='Event date and time' mod='npsmarketplace'}</label>
          <input id="ead_date" class="is_required validate form-control" data-validate="isDateTime" name="date" type="text"/>
          <span class="form_info">{l s='Format: YYYY-MM-DD HH:MM' mod='npsmarketplace'}</span>
        </div>
        <div class="form-group col-md-6">
          <label class="required">{l s='Expiration date end time' mod='npsmarketplace'}</label>
          <input id="ead_expiry_date" class="is_required validate form-control" data-validate="isDateTime" name="expiry_date" type="text"/>
          <span class="form_info">{l s='Format: YYYY-MM-DD HH:MM' mod='npsmarketplace'}</span>
        </div>
      </div>
    </form>

    <p class="submit">
      <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="closeVariantBox();"/>
      <input class="button" onclick="addVariant('#outer_ad_combination_form');" value="{l s='Add' mod='npsmarketplace'}"/>
    </p>
  </div>
</div>
