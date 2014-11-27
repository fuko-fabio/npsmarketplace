{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
<fieldset>
    {if $edit_product == 1}
    <div class="alert alert-info">
        <span class="alert-content">
        <a class="alert-link" href="{$new_tem_link}">{l s='Click here' mod='npsmarketplace'}</a> {l s='to add new event term' mod='npsprzelewy24'}
        </span>
    </div>
    {/if}
    <div class="box">
        <h3 class="page-heading">{l s='Informations' mod='npsmarketplace'}</h3>
        {if $edit_product == 0}
        <label>{l s='Type of advertisement' mod='npsmarketplace'}</label>
        <div>
            <div class="radio-inline">
                <label class="top">
                    <input type="radio" name="product_type" {if $edit_product == 1}disabled=""{/if} value="0"{if (isset($smarty.post.product_type) && $smarty.post.product_type == 0) || empty($smarty.post.product_type)} checked="checked" {/if}/>
                    {l s='Ticket' mod='npsmarketplace'}
                </label>
            </div>
            <div class="radio-inline">
                <label class="top">
                    <input type="radio" name="product_type" {if $edit_product == 1}disabled=""{/if} value="1"{if isset($smarty.post.product_type) && $smarty.post.product_type == 1} checked="checked" {/if}/>
                    {l s='Carnet' mod='npsmarketplace'}
                </label>
            </div>
            <div class="radio-inline">
                <label class="top">
                    <input type="radio" name="product_type" {if $edit_product == 1}disabled=""{/if} value="2"{if isset($smarty.post.product_type) && $smarty.post.product_type == 2} checked="checked" {/if}/>
                    {l s='Advertisement' mod='npsmarketplace'}
                </label>
            </div>
        </div>
        {else}
            <input type="hidden" name="product_type" value="{if isset($smarty.post.product_type)}{$smarty.post.product_type}{else}{if isset($product['type'])}{$product['type']|escape:'html':'UTF-8'}{/if}{/if}"/>
        {/if}
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
          {foreach from=$languages item=lang}
              <li {if $lang.id_lang == $current_id_lang}class="active"{/if}><a href="#lang{$lang.id_lang}" role="tab" data-toggle="tab">{$lang.iso_code}</a></li>
          {/foreach}
        </ul>
    
        <!-- Tab panes -->
        <div class="tab-content">
            {foreach from=$languages item=lang}
                <div class="tab-pane {if $lang.id_lang == $current_id_lang}active{/if}" id="lang{$lang.id_lang}">
                    <div class="form-group">
                        <label class="required" for="product_name_{$lang.id_lang}">{l s='Event name' mod='npsmarketplace'}</label>
                        <input class="validate form-control" data-validate="isGenericName" type="text" id="product_name_{$lang.id_lang}" name="name[{$lang.id_lang}]"
                        value="{if isset($smarty.post.name[{$lang.id_lang}])}{$smarty.post.name[{$lang.id_lang}]}{else}{if isset($product['name'][{$lang.id_lang}])}{$product['name'][{$lang.id_lang}]}{/if}{/if}"/>
                    </div>
                    <div class="form-group">
                        <label for="product_short_description_{$lang.id_lang}">{l s='Short Description' mod='npsmarketplace'}</label>
                        <textarea class="tinymce form-control" id="product_short_description_{$lang.id_lang}" name="description_short[{$lang.id_lang}]">{if isset($smarty.post.description_short[{$lang.id_lang}])}{$smarty.post.description_short[{$lang.id_lang}]}{else}{if isset($product['description_short'][{$lang.id_lang}])}{$product['description_short'][{$lang.id_lang}]}{/if}{/if}</textarea>
                        <span class="form_info">{l s='Short description will be visible on list items' mod='npsmarketplace'}</span>
                    </div>
                    <div class="form-group">
                        <label for="product_description_{$lang.id_lang}">{l s='Description' mod='npsmarketplace'}</label>
                        <textarea class="tinymce form-control" id="product_description_{$lang.id_lang}" name="description[{$lang.id_lang}]">{if isset($smarty.post.description[{$lang.id_lang}])}{$smarty.post.description[{$lang.id_lang}]}{else}{if isset($product['description'][{$lang.id_lang}])}{$product['description'][{$lang.id_lang}]}{/if}{/if}</textarea>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
    {if $edit_product == 0}
    <div class="box">
        <h3 class="page-heading ticket-attributes">{l s='Ticket' mod='npsmarketplace'}</h3>
        <h3 class="page-heading carnet-attributes">{l s='Carnet' mod='npsmarketplace'}</h3>
        <h3 class="page-heading ad-attributes">{l s='Advertisment' mod='npsmarketplace'}</h3>

        <div class="carnet-attributes">
            <p class="alert alert-info"><span class="alert-content">{l s='You will not be able to change the type of carnet and the specific values for selection.' mod='npsmarketplace'}</span></p>
            <label>{l s='Carnet type' mod='npsmarketplace'}</label>
            <div>
                <div class="radio-inline">
                    <label class="top">
                        <input type="radio" name="carnet_type" value="0"{if (isset($smarty.post.carnet_type) && $smarty.post.carnet_type == 0) || empty($smarty.post.carnet_type)} checked="checked" {/if}/>
                        {l s='Number of entries' mod='npsmarketplace'}
                    </label>
                </div>
                <div class="radio-inline">
                    <label class="top">
                        <input type="radio" name="carnet_type" value="1"{if isset($smarty.post.carnet_type) && $smarty.post.carnet_type == 1} checked="checked" {/if}/>
                        {l s='Validity time period' mod='npsmarketplace'}
                    </label>
                </div>
            </div>
        </div>
        <div class="row carnet-attributes number-entries">
            <div class="form-group col-md-6">
                <label for="entries">{l s='Number of entries' mod='npsmarketplace'}</label>
                <input class="validate form-control" data-validate="isNumber" type="number" id="entries" name="entries"
                    value="{if isset($smarty.post.entries)}{$smarty.post.entries}{else}{if isset($product['entries'])}{$product['entries']|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
        </div>
        <div class="row carnet-attributes time-period">
            <div class="form-group col-md-6">
                <label for="date_from">{l s='Valid fom' mod='npsmarketplace'}</label>
                <div id="fromDatePicker" class="input-append">
                    <input class="form-control" id="date_from" name="from" data-format="yyyy-MM-dd" type="text"
                        value="{if isset($smarty.post.from)}{$smarty.post.from}{else}{if isset($product['from'])}{$product['from']|escape:'html':'UTF-8'}{/if}{/if}"/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="date_to">{l s='To' mod='npsmarketplace'}</label>
                <div id="toDatePicker" class="input-append">
                    <input class="form-control" id="date_to" name="to" data-format="yyyy-MM-dd" type="text"
                        value="{if isset($smarty.post.to)}{$smarty.post.to}{else}{if isset($product['to'])}{$product['to']|escape:'html':'UTF-8'}{/if}{/if}"/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
        </div>
        <hr class="carnet-attributes"/>
        
        <div class="row">
            <div class="form-group col-md-6">
                <label class="required" for="product_price">{l s='Price' mod='npsmarketplace'}</label>
                <input class="is_required validate form-control" data-validate="isPrice" type="text" id="product_price" name="price" required=""
                    value="{if isset($smarty.post.price)}{$smarty.post.price}{else}{if isset($product['price'])}{$product['price']|escape:'html':'UTF-8'}{/if}{/if}"/>
                <span class="form_info">{l s='Example: 120.50' mod='npsmarketplace'}</span>
            </div>
            <div class="form-group col-md-6">
                <label class="required" for="product_amount">{l s='Quantity' mod='npsmarketplace'}</label>
                <input class="is_required validate form-control" data-validate="isNumber" type="number" id="product_amount" name="quantity" required=""
                    value="{if isset($smarty.post.quantity)}{$smarty.post.quantity}{else}{if isset($product['quantity'])}{$product['quantity']|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
        </div>
        <div class="row ticket-attributes">
            <div class="form-group col-md-6">
                <label class="required" for="date_input">{l s='Event date' mod='npsmarketplace'}</label>
                <div id="datePicker" class="input-append">
                    <input class="is_required form-control" id="date_input" name="date" data-format="yyyy-MM-dd" type="text" required=""
                        value="{if isset($smarty.post.date)}{$smarty.post.date}{else}{if isset($product['date'])}{$product['date']|escape:'html':'UTF-8'}{/if}{/if}"/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label class="required" for="time_input">{l s='Event hour' mod='npsmarketplace'}</label>
                <div id="timePicker" class="input-append">
                    <input class="is_required form-control" id="time_input" name="time" data-format="hh:mm" type="text" required=""
                        value="{if isset($smarty.post.time)}{$smarty.post.time}{else}{if isset($product['time'])}{$product['time']|escape:'html':'UTF-8'}{/if}{/if}"/>
                    <span class="form_info">{l s='Format: HH:MM' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label class="required" for="date_input">{l s='Expiration date of announcement' mod='npsmarketplace'}</label>
                <div id="availableDatePicker" class="input-append">
                    <input class="is_required form-control" id="expiry_date_input" name="expiry_date" data-format="yyyy-MM-dd" type="text" required=""
                        value="{if isset($smarty.post.expiry_date)}{$smarty.post.expiry_date}{else}{if isset($product['expiry_date'])}{$product['expiry_date']|escape:'html':'UTF-8'}{/if}{/if}"/>
                    <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                    <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="product_code">{l s='Ticket reference' mod='npsmarketplace'}</label>
                <input class="validate form-control" data-validate="isMessage" type="text" id="product_code" name="reference"
                    value="{if isset($smarty.post.reference)}{$smarty.post.reference}{else}{if isset($product['reference'])}{$product['reference']|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
        </div>
    </div>

    {else}
    <div class="box">
        <h3 class="page-heading">{l s='Price' mod='npsmarketplace'}</h3>
        <div class="row">
            <div class="form-group col-md-6">
                <label class="required" for="product_price">{l s='Price' mod='npsmarketplace'}</label>
                <input class="is_required validate form-control" data-validate="isPrice" type="text" id="product_price" name="price" required=""
                    value="{if isset($smarty.post.price)}{$smarty.post.price}{else}{if isset($product['price'])}{$product['price']|escape:'html':'UTF-8'}{/if}{/if}"/>
                <span class="form_info">{l s='Example: 120.50' mod='npsmarketplace'}</span>
            </div>
            <div class="form-group col-md-6">
                <label for="product_code">{l s='Ticket reference' mod='npsmarketplace'}</label>
                <input class="validate form-control" data-validate="isMessage" type="text" id="product_code" name="reference"
                    value="{if isset($smarty.post.reference)}{$smarty.post.reference}{else}{if isset($product['reference'])}{$product['reference']|escape:'html':'UTF-8'}{/if}{/if}"/>
            </div>
        </div>
     </div>
    {/if}
    <div class="box">
        <h3 class="page-heading">{l s='Event location' mod='npsmarketplace'}</h3>
        <div class="row">
            <div class="form-group col-md-6">
                <label class="required" for="product_town">{l s='Town' mod='npsmarketplace'}</label>
                <select class="form-control" id="product_town" name="town">
                    {foreach from=$towns item=town}
                        <option value="{$town['id_feature_value']}" {if $product['town'] eq $town['name']}selected{/if}>{$town['name']}</option> 
                    {/foreach}
                </select>
            </div>
            <div class="form-group col-md-6">
                <label class="required" for="product_district">{l s='District' mod='npsmarketplace'}</label>
                <input list="districts_list" class="is_required validate form-control" data-validate="isGenericName" type="text" id="product_district" name="district" required=""
                    value="{if isset($smarty.post.district)}{$smarty.post.district}{else}{if isset($product['district'])}{$product['district']|escape:'html':'UTF-8'}{/if}{/if}"/>
                <datalist id="districts_list">
                    {foreach from=$districts item=district}
                        <option value="{$district['name']}" />
                    {/foreach}
                </datalist>
            </div>
        </div>
        <div class="form-group">
            <label class="required" for="map-address-input">{l s='Address' mod='npsmarketplace'}</label>
            <input id="map-address-input" class="is_required validate form-control" data-validate="isMessage" name="address" type="text" required="" placeholder="{l s='Search adress...' mod='npsmarketplace'}"
                value="{if isset($smarty.post.address)}{$smarty.post.address}{else}{if isset($product['address'])}{$product['address']|escape:'html':'UTF-8'}{/if}{/if}"/>
            <input id="map-lat-input" class="hide" name="lat" type="text"
                value="{if isset($smarty.post.lat)}{$smarty.post.lat}{else}{if isset($product['lat'])}{$product['lat']|escape:'html':'UTF-8'}{/if}{/if}">
            <input id="map-lng-input" class="hide" name="lng" type="text"
                value="{if isset($smarty.post.lng)}{$smarty.post.lng}{else}{if isset($product['lng'])}{$product['lng']|escape:'html':'UTF-8'}{/if}{/if}">
        </div>
        <div class="form-group">
            <div id="map-canvas"></div>
            <span class="form_info">{l s='Drag move and drop marker on your even location' mod='npsmarketplace'}</span>
        </div>
    </div>
    <div class="box">
        <h3 class="page-heading">{l s='Media' mod='npsmarketplace'}</h3>
        <div class="form-group">
            <label class="required">{l s='Pictures' mod='npsmarketplace'}</label>
            <div class="dropzone" id="dropzone-container">
                <div class="dropzone-previews"></div>
                <div class="fallback">
                    <input name="file[]" type="file" multiple />
                </div>
            </div>
            <span class="form_info">{l s='At least one picture is required. Max allowed size 8MB. Recommended min size 512px X 512px' mod='npsmarketplace'}</span>
        </div>
        <div class="row">
            <div class="form-group col-xs-12">
                <label for="video_url">{l s='Video embeded code/URL' mod='npsmarketplace'}</label>
               <textarea class="form-control textarea-autosize" id="video_url" name="video_url">{if isset($smarty.post.video_url)}{$smarty.post.video_url}{else}{if isset($product['video_url'])}{$product['video_url']|escape:'html':'UTF-8'}{/if}{/if}</textarea>

                <span class="form_info">{l s='Paste embeded video code (YouTube, Vimeo)' mod='npsmarketplace'} <a href="$vide_how_to_url">{l s='See how to add video' mod='npsmarketplace'}</a></span>
            </div>
        </div>
    </div>
    <div class="box">
        <h3 class="page-heading">{l s='Identification' mod='npsmarketplace'}</h3>
        <div class="form-group">
            <label class="required" for="product_category">{l s='Category' mod='npsmarketplace'}</label>
            <ul class="tree">
                {foreach from=$categories_tree.children item=child name=categories_tree}
                {if $smarty.foreach.categories_tree.last}
                {include file="$category_partial_tpl_path" node=$child last='true'}
                {else}
                {include file="$category_partial_tpl_path" node=$child}
                {/if}
                {/foreach}
                <!-- Home category -->
                {if in_array(2, $product['categories'])}
                <li class="category_2 unvisible">
                    <p class="checkbox">
                        <input type="checkbox" name="category[]" id="category_2" value="2" checked=""/>
                    </p>
                </li>
                {/if}
            </ul>
        </div>
    </div>
</fieldset>