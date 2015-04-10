{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}

<div style="display:none">
    <div id="ticket_combination" class="event-combination">
        <h2 class="page-subheading">{l s='New ticket' mod='npsmarketplace'}</h2>
        <form id="ticket_combination_form">
            <input type="hidden" name="type" value="0" />
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="required">{l s='Name' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isGenericName" type="text" name="name"/>
                </div>
            </div>
            <div class="row">
                <div id="date_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Event date' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isDate" name="date" data-format="yyyy-MM-dd" type="text"/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
                <div id="time_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Event hour' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isTime" name="time" data-format="hh:mm" type="text" required=""/>
                    <span class="form_info">{l s='Format: HH:MM' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
            <div class="row">
                <div id="t_expiry_date_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Expiration date of announcement' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isDate" name="expiry_date" data-format="yyyy-MM-dd" type="text" required=""/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
                <div id="t_expiry_time_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Expiration hour' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isTime" name="expiry_time" data-format="hh:mm" type="text" required=""/>
                    <span class="form_info">{l s='Format: HH:MM' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
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
        </form>

        <p class="submit">
            <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="$.fancybox.close();"/>
            <input class="button" onclick="addVariant('#ticket_combination_form');" value="{l s='Add' mod='npsmarketplace'}"/>
        </p>
    </div>
</div>

<div style="display:none">
    <div id="carnet_combination" class="event-combination">
        <h2 class="page-subheading">{l s='New carnet' mod='npsmarketplace'}</h2>
        <form id="carnet_combination_form">
            <input type="hidden" name="type" value="1" />
            <div class="row">
                <div class="col-md-6 form-group">
                    <label class="required">{l s='Name' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isGenericName" type="text" name="name"/>
                </div>
            </div>
            <div class="row">
                <div id="c_expiry_date_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Expiration date of announcement' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isDate" name="expiry_date" data-format="yyyy-MM-dd" type="text" required=""/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
                <div id="c_expiry_time_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Expiration hour' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isTime" name="expiry_time" data-format="hh:mm" type="text" required=""/>
                    <span class="form_info">{l s='Format: HH:MM' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
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
        </form>

        <p class="submit">
            <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="$.fancybox.close();"/>
            <input class="button" onclick="addVariant('#carnet_combination_form');" value="{l s='Add' mod='npsmarketplace'}"/>
        </p>
    </div>
</div>

<div style="display:none">
    <div id="ad_combination" class="event-combination">
        <h2 class="page-subheading">{l s='New advertisment' mod='npsmarketplace'}</h2>
        <form id="ad_combination_form">
            <input type="hidden" name="type" value="2" />
            <div class="row">
                <div id="a_date_picker" class="form-group col-md-6 input-append">
                    <label>{l s='Event date' mod='npsmarketplace'}</label>
                    <input class="validate form-control" data-validate="isDate" name="date" data-format="yyyy-MM-dd" type="text"/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
                <div id="a_time_picker" class="form-group col-md-6 input-append">
                    <label>{l s='Event hour' mod='npsmarketplace'}</label>
                    <input class="validate form-control" data-validate="isTime" name="time" data-format="hh:mm" type="text" required=""/>
                    <span class="form_info">{l s='Format: HH:MM' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
            <div class="row">
                <div id="a_expiry_date_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Expiration date of announcement' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isDate" name="expiry_date" data-format="yyyy-MM-dd" type="text" required=""/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
                <div id="a_expiry_time_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Expiration hour' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isTime" name="expiry_time" data-format="hh:mm" type="text" required=""/>
                    <span class="form_info">{l s='Format: HH:MM' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
        </form>

        <p class="submit">
            <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="$.fancybox.close();"/>
            <input class="button" onclick="addVariant('#ad_combination_form');" value="{l s='Add' mod='npsmarketplace'}"/>
        </p>
    </div>
</div>

<div style="display:none">
    <div id="outer_ad_combination" class="event-combination">
        <h2 class="page-subheading">{l s='New outer advertisment' mod='npsmarketplace'}</h2>
        <form id="outer_ad_combination_form">
            <input type="hidden" name="type" value="3" />
            <div class="row">
                <div id="oa_date_picker" class="form-group col-md-6 input-append">
                    <label>{l s='Event date' mod='npsmarketplace'}</label>
                    <input class="validate form-control" data-validate="isDate" name="date" data-format="yyyy-MM-dd" type="text"/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
                <div id="oa_time_picker" class="form-group col-md-6 input-append">
                    <label>{l s='Event hour' mod='npsmarketplace'}</label>
                    <input class="validate form-control" data-validate="isTime" name="time" data-format="hh:mm" type="text" required=""/>
                    <span class="form_info">{l s='Format: HH:MM' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
            <div class="row">
                <div id="oa_expiry_date_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Expiration date of announcement' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isDate" name="expiry_date" data-format="yyyy-MM-dd" type="text" required=""/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
                <div id="oa_expiry_time_picker" class="form-group col-md-6 input-append">
                    <label class="required">{l s='Expiration hour' mod='npsmarketplace'}</label>
                    <input class="is_required validate form-control" data-validate="isTime" name="expiry_time" data-format="hh:mm" type="text" required=""/>
                    <span class="form_info">{l s='Format: HH:MM' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
        </form>

        <p class="submit">
            <input class="button ccl" type="button" value="{l s='Cancel' mod='npsmarketplace'}" onclick="$.fancybox.close();"/>
            <input class="button" onclick="addVariant('#outer_ad_combination_form');" value="{l s='Add' mod='npsmarketplace'}"/>
        </p>
    </div>
</div>
