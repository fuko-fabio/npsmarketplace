<fieldset id="add_offer">
    <h3 class="page-heading bottom-indent">{l s='Add product' mod='npsmarketplace'}</h3>
    <div class="form-group">
        <label>{l s='Product images' mod='npsmarketplace'}</label>
        <input id="product_images" type="file" multiple="true" name="product[]">
    </div>
    <div class="form-group">
        <label class="required" for="product_name">{l s='Name' mod='npsmarketplace'}</label>
        <input class="is_required validate form-control" data-validate="isGenericName" type="text" id="product_name" name="product_name" required=""/>
    </div>
    <div class="form-group">
        <label class="required" for="product_short_description">{l s='Short Description' mod='npsmarketplace'}</label>
        <textarea class="is_required validate form-control" data-validate="isMessage" id="product_short_description" name="product_short_description" rows="2"></textarea>
    </div>
    <div class="form-group">
        <label class="required" for="product_description">{l s='Description' mod='npsmarketplace'}</label>
        <textarea class="is_required validate form-control" data-validate="isMessage" id="product_description" name="product_description" rows="10"></textarea>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label class="required" for="product_price">{l s='Price' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isPrice" type="text" id="product_price" name="product_price" required=""/>
        </div>
        <div class="form-group col-md-6">
            <label class="required" for="product_amount">{l s='Amount' mod='npsmarketplace'}</label>
            <input class="is_required validate form-control" data-validate="isNumber" type="number" id="product_amount" name="product_amount" required=""/>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-md-6">
            <label class="required" for="product_date">{l s='Date' mod='npsmarketplace'}</label>
            </br>
            <div id="datePicker" class="input-append">
                <input class="is_required form-control" id="product_date" name="product_date" data-format="yyyy-MM-dd" type="text" readonly="" required=""/>
                </input>
                <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label class="required" for="product_time">{l s='Time' mod='npsmarketplace'}</label>
            </br>
            <div id="timePicker" class="input-append">
                <input class="is_required form-control" id="product_time" name="product_time" data-format="hh:mm" type="text" readonly="" required=""/>
                </input>
                <span class="add-on"> <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i> </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="product_code">{l s='Product code' mod='npsmarketplace'}</label>
        <input class="is_required validate form-control" data-validate="isMessage" type="text" id="product_code" name="product_code" />
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
        </ul>
    </div>
</fieldset>