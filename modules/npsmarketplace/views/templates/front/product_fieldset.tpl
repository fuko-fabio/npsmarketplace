<fieldset>
    {if $edit_product == 1}
    <div class="alert alert-info">
        <a class="alert-link" href="{$new_tem_link}">{l s='Click here' mod='npsprzelewy24'}</a> {l s='to add new event term' mod='npsprzelewy24'}
    </div>
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
                    <label class="required" for="product_name_{$lang.id_lang}">{l s='Name' mod='npsmarketplace'}</label>
                    <input class="validate form-control" data-validate="isGenericName" type="text" id="product_name_{$lang.id_lang}" name="name[{$lang.id_lang}]"
                    value="{if isset($smarty.post.name[{$lang.id_lang}])}{$smarty.post.name[{$lang.id_lang}]}{else}{if isset($product['name'][{$lang.id_lang}])}{$product['name'][{$lang.id_lang}]}{/if}{/if}"/>
                </div>
                <div class="form-group">
                    <label for="product_short_description_{$lang.id_lang}">{l s='Short Description' mod='npsmarketplace'}</label>
                    <textarea class="tinymce form-control" id="product_short_description_{$lang.id_lang}" name="description_short[{$lang.id_lang}]">{if isset($smarty.post.description_short[{$lang.id_lang}])}{$smarty.post.description_short[{$lang.id_lang}]}{else}{if isset($product['description_short'][{$lang.id_lang}])}{$product['description_short'][{$lang.id_lang}]}{/if}{/if}</textarea>
                </div>
                <div class="form-group">
                    <label for="product_description_{$lang.id_lang}">{l s='Description' mod='npsmarketplace'}</label>
                    <textarea class="tinymce form-control" id="product_description_{$lang.id_lang}" name="description[{$lang.id_lang}]">{if isset($smarty.post.description[{$lang.id_lang}])}{$smarty.post.description[{$lang.id_lang}]}{else}{if isset($product['description'][{$lang.id_lang}])}{$product['description'][{$lang.id_lang}]}{/if}{/if}</textarea>
                </div>
            </div>
        {/foreach}
    </div>

    {if $edit_product == 0}
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
    <div class="row">
        <div class="form-group col-md-6">
            <label class="required" for="date_input">{l s='Date' mod='npsmarketplace'}</label>
            <div id="datePicker" class="input-append">
                <input class="is_required form-control" id="date_input" name="date" data-format="yyyy-MM-dd" type="text" readonly="" required=""
                    value="{if isset($smarty.post.date)}{$smarty.post.date}{else}{if isset($product['date'])}{$product['date']|escape:'html':'UTF-8'}{/if}{/if}"/>
                <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label class="required" for="time_input">{l s='Time' mod='npsmarketplace'}</label>
            <div id="timePicker" class="input-append">
                <input class="is_required form-control" id="time_input" name="time" data-format="hh:mm" type="text" readonly="" required=""
                    value="{if isset($smarty.post.time)}{$smarty.post.time}{else}{if isset($product['time'])}{$product['time']|escape:'html':'UTF-8'}{/if}{/if}"/>
                <span class="form_info">{l s='Format: HH:MM' mod='npsmarketplace'}</span>
                <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label class="required" for="date_input">{l s='Available Date' mod='npsmarketplace'}</label>
            <div id="availableDatePicker" class="input-append">
                <input class="is_required form-control" id="expiry_date_input" name="expiry_date" data-format="yyyy-MM-dd" type="text" readonly="" required=""
                    value="{if isset($smarty.post.expiry_date)}{$smarty.post.expiry_date}{else}{if isset($product['expiry_date'])}{$product['expiry_date']|escape:'html':'UTF-8'}{/if}{/if}"/>
                <span class="form_info">{l s='Format: YYYY-MM-DD' mod='npsmarketplace'}</span>
                <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="product_code">{l s='Reference' mod='npsmarketplace'}</label>
            <input class="validate form-control" data-validate="isMessage" type="text" id="product_code" name="reference"
                value="{if isset($smarty.post.reference)}{$smarty.post.reference}{else}{if isset($product['reference'])}{$product['reference']|escape:'html':'UTF-8'}{/if}{/if}"/>
        </div>
    </div>
    {else}
    <div class="row">
        <div class="form-group col-md-6">
            <label class="required" for="product_price">{l s='Price' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isPrice" type="text" id="product_price" name="price" required=""
                value="{if isset($smarty.post.price)}{$smarty.post.price}{else}{if isset($product['price'])}{$product['price']|escape:'html':'UTF-8'}{/if}{/if}"/>
            <span class="form_info">{l s='Example: 120.50' mod='npsmarketplace'}</span>
        </div>
        <div class="form-group col-md-6">
            <label for="product_code">{l s='Reference' mod='npsmarketplace'}</label>
            <input class="validate form-control" data-validate="isMessage" type="text" id="product_code" name="reference"
                value="{if isset($smarty.post.reference)}{$smarty.post.reference}{else}{if isset($product['reference'])}{$product['reference']|escape:'html':'UTF-8'}{/if}{/if}"/>
        </div>
    </div>
    {/if}
    <div class="row">
        <div class="form-group col-md-6">
            <label class="required" for="product_town">{l s='Town' mod='npsmarketplace'}</label>
            <select class="form-control" id="product_town" name="town">
                {foreach from=$towns item=town}
                    <option value="{$town['name']}" {if $product['town'] eq $town['name']}selected{/if}>{$town['name']}</option> 
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
        <input id="map-lat-input" class="hide" name="product_lat" type="text">
        <input id="map-lng-input" class="hide" name="product_lng" type="text">
    </div>
    <div class="form-group">
        <div id="map-canvas"></div>
        <span class="form_info">{l s='Drag move and drop marker on your even location' mod='npsmarketplace'}</span>
    </div>
    <div class="form-group">
        <label>{l s='Pictures' mod='npsmarketplace'}</label>
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
            <label for="video_url">{l s='Video URL' mod='npsmarketplace'}</label>
            <input class="form-control" type="text" id="video_url" name="video_url"
                value="{if isset($smarty.post.video_url)}{$smarty.post.video_url}{else}{if isset($product['video_url'])}{$product['video_url']|escape:'html':'UTF-8'}{/if}{/if}"/>
            <span class="form_info">{l s='Paste video URL (YouTube, Vimeo etc..) to display it on event overview page' mod='npsmarketplace'}</span>
        </div>
    </div>
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
</fieldset>
<script type="text/javascript">
    $(".textarea-autosize").autosize();
</script>