<fieldset id="add_offer">
    {if isset($product['id'])}
    <h3 class="page-heading bottom-indent">{l s='Edit product' mod='npsmarketplace'}</h3>
    {else}
    <h3 class="page-heading bottom-indent">{l s='Add product' mod='npsmarketplace'}</h3>
    {/if}
    <div class="form-group">
        <label>{l s='Product images' mod='npsmarketplace'}</label>
        <input id="product_images" type="file" multiple="true" name="product[]">
    </div>
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
                    <label class="required" for="product_name">{l s='Name' mod='npsmarketplace'}</label>
                    <input class="validate form-control" data-validate="isGenericName" type="text" id="product_name" name="product_name[{$lang.id_lang}]" value="{if isset($product['name'][$lang.id_lang])}{$product['name'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}"/>
                </div>
                <div class="form-group">
                    <label for="product_short_description">{l s='Short Description' mod='npsmarketplace'}</label>
                    <textarea class="validate form-control" data-validate="isMessage" id="product_short_description" name="product_short_description[{$lang.id_lang}]" rows="2">{if isset($product['description_short'][$lang.id_lang])}{$product['description_short'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}</textarea>
                </div>
                <div class="form-group">
                    <label for="product_description">{l s='Description' mod='npsmarketplace'}</label>
                    <textarea class="validate form-control" data-validate="isMessage" id="product_description" name="product_description[{$lang.id_lang}]" rows="10">{if isset($product['description'][$lang.id_lang])}{$product['description'][$lang.id_lang]|escape:'html':'UTF-8'}{/if}</textarea>
                </div>
            </div>
        {/foreach}
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            <label class="required" for="product_price">{l s='Price' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isPrice" type="text" id="product_price" name="product_price" required="" value="{if isset($product['price'])}{$product['price']|escape:'html':'UTF-8'}{/if}"/>
        </div>
        <div class="form-group col-md-6">
            <label class="required" for="product_amount">{l s='Amount' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isNumber" type="number" id="product_amount" name="product_amount" required="" value="{if isset($product['quantity'])}{$product['quantity']|escape:'html':'UTF-8'}{/if}"/>
        </div>
    </div>
    <div class="form-group">
        <label for="product_code">{l s='Product code' mod='npsmarketplace'}</label>
        <input class="validate form-control" data-validate="isMessage" type="text" id="product_code" name="product_code" value="{if isset($product['reference'])}{$product['reference']|escape:'html':'UTF-8'}{/if}"/>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label class="required" for="product_town">{l s='Town' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isGenericName" type="text" id="product_town" name="product_town" required="" value="{if isset($product['town'])}{$product['town']|escape:'html':'UTF-8'}{/if}"/>
        </div>
        <div class="form-group col-md-6">
            <label class="required" for="product_date_time">{l s='Date & Time' mod='npsmarketplace'}</label>
            </br>
            <div id="datePicker" class="input-append">
                <input class="is_required form-control" id="product_date_time" name="product_date_time" data-format="yyyy-MM-dd hh:mm" type="text" readonly="" required="" value="{if isset($product['date_time'])}{$product['date_time']|escape:'html':'UTF-8'}{/if}"/>
                <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            <label class="required" for="product_address">{l s='Address' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isGenericName" type="text" id="product_address" name="product_address" required="" value="{if isset($product['address'])}{$product['address']|escape:'html':'UTF-8'}{/if}"/>
        </div>
    </div>
    <input id="pac-input" class="controls" type="text" placeholder="Search...">
    <div id="map-canvas" style="min-height: 250px;"></div>
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
        </ul>
    </div>
</fieldset>