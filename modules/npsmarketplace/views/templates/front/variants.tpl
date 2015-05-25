{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}

<script type="text/x-tmpl" id="ticket-tmpl">
  <div class="item variant-index-{literal}{%=o.index%}{/literal}">
      <a href="javascript: void(0)" class="icon-btn pull-right" title="{l s='Delete'  mod='npsmarketplace'}" onclick="removeVariant({literal}{%=o.index%}{/literal});"><i class="icon-trash right"></i></a>
      <span class="name">
          {literal}{%=o.name%}{/literal}
          <span class="type">
              {literal}{% if (o.type == 'ticket') { %}{/literal}
                ({l s='Ticket'  mod='npsmarketplace'})
              {literal}{% } %}{/literal}
              {literal}{% if (o.type == 'carnet') { %}{/literal}
                ({l s='Carnet'  mod='npsmarketplace'})
              {literal}{% } %}{/literal}
              {literal}{% if (o.type == 'ad') { %}{/literal}
                ({l s='Ad'  mod='npsmarketplace'})
              {literal}{% } %}{/literal}
              {literal}{% if (o.type == 'externalad') { %}{/literal}
                ({l s='External ad'  mod='npsmarketplace'})
              {literal}{% } %}{/literal}
          </span>
      </span>
      {literal}{% if (o.date) { %}{/literal}
      <div>
        {l s='Event date'  mod='npsmarketplace'}
        <span class="date">{literal}{%=o.date%}{/literal}</span>
      </div>
      {literal}{% } %}{/literal}
      <div>
        {l s='Expiry date'  mod='npsmarketplace'}
        <span class="date">{literal}{%=o.expiry_date%}{/literal}</span>
      </div>
    
      {literal}{% if (o.type == 'ticket' || o.type == 'carnet') { %}{/literal}
      <div class="row">
          <div class="form-group col-sm-4">
            <label>{l s='Price' mod='npsmarketplace'}</label>
            {literal}<input type="text" class="validate form-control" data-validate="isPrice" name="combinations[{%=o.index%}][price]" value="{%=o.price%}" />{/literal}
          </div>
          <div class="form-group col-sm-4">
            <label>{l s='Quantity' mod='npsmarketplace'}</label>
            {literal}<input type="text" class="validate form-control" data-validate="isQuantity" name="combinations[{%=o.index%}][quantity]" value="{%=o.quantity%}" />{/literal}
          </div>
          <div class="form-group col-sm-4">
            <label>{l s='Default' mod='npsmarketplace'}</label>
            {literal}
            <input type="checkbox" class="form-control" name="combinations[{%=o.index%}][default]" value="1" {% if (o.default == 1) { %}checked=""{% } %}/>
            {/literal}
          </div>
      </div>
      <a class="btn special-prices-collapse" data-toggle="collapse" href="#collapse-{literal}{%=o.index%}{/literal}" aria-expanded="false">{l s='Specific Price' mod='npsmarketplace'} <i class="icon-arrow-down"></i></a>
      <div class="collapse" id="collapse-{literal}{%=o.index%}{/literal}">
          <div class="well special-prices-container">
              {literal}{% if (o.specific_prices && o.specific_prices.length > 0) { %}{/literal}
                  {literal}{% for (var i=0; i < o.specific_prices.length; i++) { %}{/literal}
                      <div class="special-item item-index-{literal}{%=i%}{/literal}">
                          <div class="row">
                              <div class="form-group col-sm-3">
                                  <label>{l s='Reduction' mod='npsmarketplace'}</label>
                                  {literal}
                                  <input type="text" class="validate form-control" data-validate="isPrice" name="combinations[{%=o.index%}][specific_prices][{%=i%}][reduction]" value="{%=o.specific_prices[i].reduction%}" />
                                  {/literal}
                              </div>
                              <div class="form-group col-sm-3">
                                  <label>{l s='From' mod='npsmarketplace'}</label>
                                  {literal}
                                  <input type="text" class="validate form-control" data-validate="isDateTime" name="combinations[{%=o.index%}][specific_prices][{%=i%}][from]" value="{%=o.specific_prices[i].from%}" readonly=""/>
                                  {/literal}
                              </div>
                              <div class="form-group col-sm-3">
                                  <label>{l s='To' mod='npsmarketplace'}</label>
                                  {literal}
                                  <input type="text" class="validate form-control" data-validate="isDateTime" name="combinations[{%=o.index%}][specific_prices][{%=i%}][to]" value="{%=o.specific_prices[i].to%}" readonly=""/>
                                  {/literal}
                              </div>
                              <div class="col-sm-3">
                                  <a href="javascript: void(0)" title="{l s='Delete'  mod='npsmarketplace'}" class="icon-btn pull-right" onclick="removeSpecialPrice({literal}{%=o.index%},{%=o.specific_prices[i].index%}{/literal});"><i class="icon-trash right"></i></a>
                              </div>
                          </div>
                      </div>
                      {literal}
                      {% if (o.specific_prices[i].id_specific_price) { %}
                        <input type="hidden" name="combinations[{%=o.index%}][specific_prices][{%=i%}][id_specific_price]" value="{%=o.specific_prices[i].id_specific_price%}" />
                      {% } %}
                  {% } %}
              {% } %}
              {/literal}
              <p class="alert alert-info no-specific-prices">
                  <span class="alert-content">{l s='No specific prices' mod='npsmarketplace'}</span>
              </p>
          </div>
      </div>
      {literal}{% } %}{/literal}
    
      {literal}
      <input type="hidden" name="combinations[{%=o.index%}][name]" value="{%=o.name%}" />
      <input type="hidden" name="combinations[{%=o.index%}][type]" value="{%=o.type%}" />
      {% if (o.type != 'carnet') { %}
      <input type="hidden" name="combinations[{%=o.index%}][date]" value="{%=o.date%}" />
      {% } %}
      <input type="hidden" name="combinations[{%=o.index%}][expiry_date]" value="{%=o.expiry_date%}" />
      {% if (o.id_product_attribute) { %}
      <input type="hidden" name="combinations[{%=o.index%}][id_product_attribute]" value="{%=o.id_product_attribute%}" />
      {% } %}
      {/literal}
  </div>
</script>

<div style="display:none">
  <div id="ticket_combination" class="event-combination">
    <h2 class="page-subheading">{l s='New ticket' mod='npsmarketplace'}</h2>
    <form id="ticket_combination_form">
      <input type="hidden" name="type" value="ticket" />
      <div class="row">
        <div class="col-md-6 form-group">
          <label class="required">{l s='Name' mod='npsmarketplace'}</label>
          <input class="is_required validate form-control" data-validate="isGenericName" type="text" name="name" placeholder="{l s='Normal, students, kids etc' mod='npsmarketplace'}"/>
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
      <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="closeVariantBox('#ticket_combination_form');"/>
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
          <input class="is_required validate form-control" data-validate="isGenericName" type="text" name="name" placeholder="{l s='For year, month, week etc' mod='npsmarketplace'}"/>
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
      <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="closeVariantBox('#carnet_combination_form');"/>
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
      <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="closeVariantBox('#ad_combination_form');"/>
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
      <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="closeVariantBox('#outer_ad_combination_form');"/>
      <input class="button" onclick="addVariant('#outer_ad_combination_form');" value="{l s='Add' mod='npsmarketplace'}"/>
    </p>
  </div>
</div>
