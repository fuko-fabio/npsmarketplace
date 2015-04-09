{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}
 <script type="text/x-tmpl" id="ticket-tmpl">
    <tr class="item variant-index-{literal}{%=o.index%}{/literal}">
        <td>
            {literal}
            {% if (o.type == 0) { %}
            {/literal}
            {l s='Ticket' mod='npsmarketplace'}
            {literal}
            {% } %}
            {% if (o.type == 1) { %}
            {/literal}
            {l s='Carnet' mod='npsmarketplace'}
            {literal}
            {% } %}
            {% if (o.type == 2) { %}
            {/literal}
            {l s='Advertisment' mod='npsmarketplace'}
            {literal}
            {% } %}
            {% if (o.type == 3) { %}
            {/literal}
            {l s='Outer advertisment' mod='npsmarketplace'}
            {literal}
            {% } %}
        </td>
        <td><div class="checkbox"><input type="checkbox" name="combinations[{%=o.index%}][default]" value="1" /></div></td>
        <td>{%=o.name%}</td>
        <td>{%=o.date%} {%=o.time%}</td>
        <td>{%=o.expiry_date%} {%=o.expiry_time%}</td>
        <td>{% if (o.type < 2) { %}<input type="text" name="combinations[{%=o.index%}][quantity]" value="{%=o.quantity%}" />{% } %}</td>
        <td>{% if (o.type < 2) { %}<input type="text" name="combinations[{%=o.index%}][price]" value="{%=o.price%}" />{% } %}</td>
        <td>
            <button type="button" class="btn btn-default button button-small pull-right" onclick="removeVariant({literal}{%=o.index%}{/literal});"><i class="icon-trash right"></i></button>
            <input type="hidden" name="combinations[{%=o.index%}][name]" value="{%=o.name%}" />
            <input type="hidden" name="combinations[{%=o.index%}][type]" value="{%=o.type%}" />
            {% if (o.type == 0) { %}
            <input type="hidden" name="combinations[{%=o.index%}][date]" value="{%=o.date%}" />
            <input type="hidden" name="combinations[{%=o.index%}][time]" value="{%=o.time%}" />
            {% } %}
            <input type="hidden" name="combinations[{%=o.index%}][expiry_date]" value="{%=o.expiry_date%}" />
            <input type="hidden" name="combinations[{%=o.index%}][expiry_time]" value="{%=o.expiry_time%}" />
            {/literal}
        </td>
    </tr>
</script>

<fieldset>
    <div class="box">
        <h3 class="page-heading">{l s='Informations' mod='npsmarketplace'}</h3>
        <ul class="nav nav-tabs {if count($languages) < 2}hidden{/if}" role="tablist">
          {foreach from=$languages item=lang}
              <li {if $lang.id_lang == $current_id_lang}class="active"{/if}><a href="#lang{$lang.id_lang}" role="tab" data-toggle="tab">{$lang.iso_code}</a></li>
          {/foreach}
        </ul>
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
                        <span class="form_info">{l s='Add here key informations. Short description will be visible on list items' mod='npsmarketplace'}</span>
                    </div>
                    <div class="form-group">
                        <label for="product_description_{$lang.id_lang}">{l s='Description' mod='npsmarketplace'}</label>
                        <textarea class="tinymce form-control" id="product_description_{$lang.id_lang}" name="description[{$lang.id_lang}]">{if isset($smarty.post.description[{$lang.id_lang}])}{$smarty.post.description[{$lang.id_lang}]}{else}{if isset($product['description'][{$lang.id_lang}])}{$product['description'][{$lang.id_lang}]}{/if}{/if}</textarea>
                        <span class="form_info"><a href="{$description_how_to_url}">{l s='How to write interesting description?' mod='npsmarketplace'}</a></span>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>

    <div class="box">
        <h3 class="page-heading">{l s='Event location' mod='npsmarketplace'}</h3>
        <div class="row">
            <div class="form-group col-md-6">
                <label class="required" for="product_province">{l s='Province' mod='npsmarketplace'}</label>
                <select class="form-control" id="product_province" name="province">
                    {foreach from=$provinces item=province}
                        <option value="{$province.id_feature_value}" {if isset($smarty.post.province) && $smarty.post.province == $province.id_feature_value}selected{else}{if $product.province eq $province.id_feature_value}selected{/if}{/if}>{$province.name}</option> 
                    {/foreach}
                </select>
            </div>
            <div class="form-group col-md-6">
                <label class="required" for="product_town">{l s='Town' mod='npsmarketplace'}</label>
                <select class="form-control" id="product_town" name="town">
                    {if isset($smarty.post.province)}
                        {foreach from=$provinces item=province}
                            {if $smarty.post.province eq $province.id_feature_value}
                                {foreach from=$province.towns item=town}
                                    <option value="{$town.id_feature_value}" {if isset($smarty.post.town) && $smarty.post.town eq $town.id_feature_value}selected{else}{if $product.town eq $town.id_feature_value}selected{/if}{/if}>{$town.name}</option>
                                {/foreach}
                            {/if}
                        {/foreach}
                    {else}
                        {foreach from=$towns item=town}
                            <option value="{$town.id_feature_value}" {if isset($smarty.post.town) && $smarty.post.town eq $town.id_feature_value}selected{else}{if $product.town eq $town.id_feature_value}selected{/if}{/if}>{$town.name}</option>
                        {/foreach}
                    {/if}
                    <option value="0" {if isset($smarty.post.town) && $smarty.post.town eq 0}selected{else}{if !isset($smarty.post.town) && $product.town eq 0}selected{/if}{/if}>{l s='--Other--' mod='npsmarketplace'}</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label class="required" for="map-address-input">{l s='Address' mod='npsmarketplace'}</label>
                <input id="map-address-input" class="is_required validate form-control" data-validate="isMessage" name="address" type="text" required="" placeholder="{l s='Search adress...' mod='npsmarketplace'}"
                    value="{if isset($smarty.post.address)}{$smarty.post.address}{else}{if isset($product['address'])}{$product['address']|escape:'html':'UTF-8'}{/if}{/if}"/>
                <span class="form_info">{l s='After you specify address please check town and district. This inputs are not adjusted automatically.' mod='npsmarketplace'}</span>
                <input id="map-lat-input" class="hide" name="lat" type="text"
                    value="{if isset($smarty.post.lat)}{$smarty.post.lat}{else}{if isset($product['lat'])}{$product['lat']|escape:'html':'UTF-8'}{/if}{/if}">
                <input id="map-lng-input" class="hide" name="lng" type="text"
                    value="{if isset($smarty.post.lng)}{$smarty.post.lng}{else}{if isset($product['lng'])}{$product['lng']|escape:'html':'UTF-8'}{/if}{/if}">
            </div>
            <div class="form-group col-md-6">
                <label for="product_district">{l s='District' mod='npsmarketplace'}</label>
                <input list="districts_list" class="validate form-control" data-validate="isGenericName" type="text" id="product_district" name="district"
                    value="{if isset($smarty.post.district)}{$smarty.post.district}{else}{if isset($product['district'])}{$product['district']|escape:'html':'UTF-8'}{/if}{/if}"/>
                <datalist id="districts_list">
                    {foreach from=$districts item=district}
                        <option value="{$district['name']}" />
                    {/foreach}
                </datalist>
            </div>
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
            <span class="form_info">{l s='First image will be general image.' mod='npsmarketplace'} <a href="{$images_how_to_url}">{l s='See how to add images.' mod='npsmarketplace'}</a></span>
            <p class="alert alert-info waitForImages"><span class="alert-content">{l s='Sending images in progress. Wait for the result.' mod='npsmarketplace'}</span></p>
        </div>
        <div class="row">
            <div class="form-group col-xs-12">
                <label for="video_url">{l s='Video embeded code/URL' mod='npsmarketplace'}</label>
               <textarea class="form-control textarea-autosize" id="video_url" name="video_url">{if isset($smarty.post.video_url)}{$smarty.post.video_url}{else}{if isset($product['video_url'])}{$product['video_url']|escape:'html':'UTF-8'}{/if}{/if}</textarea>
                <span class="form_info">{l s='Paste embeded video code (YouTube, Vimeo)' mod='npsmarketplace'} <a href="{$vide_how_to_url}">{l s='See how to add video' mod='npsmarketplace'}</a></span>
            </div>
        </div>
    </div>

    <div class="box">
        <h3 class="page-heading">{l s='Identification' mod='npsmarketplace'}</h3>
        <p class="alert alert-info"><span class="alert-content">{l s='You can select more than one category.' mod='npsmarketplace'}</span></p>
        <div class="row">
            <div class="form-group col-md-6">
                <div class="form-group">
                    <label class="required" for="product_category">{l s='Category' mod='npsmarketplace'}</label>
                    <ul class="categories-tree tree collapsibleList">
                        {foreach from=$categories_tree.children item=child name=categories_tree}
                        {if $smarty.foreach.categories_tree.last}
                        {include file="$category_partial_tpl_path" node=$child last='true' merge_parent='true'}
                        {else}
                        {include file="$category_partial_tpl_path" node=$child merge_parent='true'}
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
                        {if isset($free_category_id) && !empty($free_category_id) && in_array($free_category_id, $product['categories'])}
                        <li class="category_{$free_category_id} unvisible">
                            <p class="checkbox">
                                <input type="checkbox" name="category[]" id="category_{$free_category_id}" value="{$free_category_id}" checked=""/>
                            </p>
                        </li>
                        {/if}
                    </ul>
                </div>
            </div>
            <div class="form-group col-md-6">
                {if $special_categories_tree|@count > 0}
                <div class="form-group">
                    <label for="product_category">{l s='Special category' mod='npsmarketplace'}</label>
                    <ul class="categories-tree tree">
                        {foreach from=$special_categories_tree item=child name=special_categories_tree}
                        {if $smarty.foreach.special_categories_tree.last}
                        {include file="$category_partial_tpl_path" node=$child last='true' merge_parent='false'}
                        {else}
                        {include file="$category_partial_tpl_path" node=$child merge_parent='false'}
                        {/if}
                        {/foreach}
                    </ul>
                </div>
                {/if}
            </div>
        </div>
    </div>
    <div class="box variants-box">
      <ul class="nav navbar-nav pull-right">
        <li class="dropdown">
          <a href="#" data-toggle="dropdown" class="dropdown-toggle"><i class="icon-plus"></i> {l s='Add' mod='npsmarketplace'}<b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li>
              <a href="#ticket_combination" class="add-variant-btn">{l s='Ticket' mod='npsmarketplace'}</a>
            </li>
            <li>
              <a href="#carnet_combination" class="add-variant-btn">{l s='Carnet' mod='npsmarketplace'}</a>
            </li>
            <li>
              <a href="#ad_combination" class="add-variant-btn">{l s='Advertisment' mod='npsmarketplace'}</a>
            </li>
            {if $seller->outer_adds}
            <li>
              <a href="#outer_ad_combination" class="add-variant-btn">{l s='Outer Advertisment' mod='npsmarketplace'}</a>
            </li>
            {/if}
          </ul>
        </li>
      </ul>
      <h3 class="page-heading">{l s='Variants' mod='npsmarketplace'}</h3>
      <table class="table table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th>{l s='Default' mod='npsmarketplace'}</th>
                    <th>{l s='Name' mod='npsmarketplace'}</th>
                    <th>{l s='Event date' mod='npsmarketplace'}</th>
                    <th>{l s='Expiry date' mod='npsmarketplace'}</th>
                    <th>{l s='Quantity' mod='npsmarketplace'}</th>
                    <th>{l s='Price' mod='npsmarketplace'}</th>
                </tr>
            </thead>
            <tbody class="variants-container">
                {if isset($product.combinations)}
                {foreach from=$product.combinations item=comb key=id}
                <tr class="item variant-index-{$id}">
                    <td>
                        {if $comb.type == 0}
                        {l s='Ticket' mod='npsmarketplace'}
                        {elseif $comb.type == 1}
                        {l s='Carnet' mod='npsmarketplace'}
                        {elseif $comb.type == 2}
                        {l s='Advertisment' mod='npsmarketplace'}
                        {elseif $comb.type == 3}
                        {l s='Outer advertisment' mod='npsmarketplace'}
                        {/if}
                    </td>
                    <td><div class="checkbox"><input type="checkbox" name="combinations[{$id}][default]" value="1" {if $comb.default}checked="checked"{/if}/></div></td>
                    <td>{$comb.name}</td>
                    <td>{if isset($comb.date) && isset($comb.time)}{$comb.date} {$comb.time}{/if}</td>
                    <td>{$comb.expiry_date} {$comb.expiry_time}</td>
                    <td>{if $comb.type < 2}<input type="number" name="combinations[{$id}][quantity]" value="{$comb.quantity}" />{/if}</td>
                    <td>{if $comb.type < 2}<input type="text" name="combinations[{$id}][price]" value="{round($comb.price, 2)}" />{/if}</td>
                    <td>
                        <button type="button" class="btn btn-default button button-small pull-right" onclick="removeVariant({$id});"><i class="icon-trash right"></i></button>
                        <input type="hidden" name="combinations[{$id}][id_product_attribute]" value="{$comb.id_product_attribute}" />
                        <input type="hidden" name="combinations[{$id}][name]" value="{$comb.name}" />
                        <input type="hidden" name="combinations[{$id}][type]" value="{$comb.type}" />
                        {if $comb.type == 0}
                        <input type="hidden" name="combinations[{$id}][date]" value="{$comb.date}" />
                        <input type="hidden" name="combinations[{$id}][time]" value="{$comb.time}" />
                        {/if}
                        <input type="hidden" name="combinations[{$id}][expiry_date]" value="{$comb.expiry_date}" />
                        <input type="hidden" name="combinations[{$id}][expiry_time]" value="{$comb.expiry_time}" />
                    </td>
                </tr>
                {/foreach}
                {/if}
            </tbody>
        </table>
    </div>
</fieldset>