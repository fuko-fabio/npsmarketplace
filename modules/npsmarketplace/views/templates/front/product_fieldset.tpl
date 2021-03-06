{*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2014 npsoftware
*}

<fieldset>
  <div class="box">
    <h3 class="page-heading">{l s='Informations' mod='npsmarketplace'}</h3>
    <ul class="nav nav-tabs {if count($languages) < 2}hidden{/if}" role="tablist">
      {foreach from=$languages item=lang}
      <li {if $lang.id_lang == $current_id_lang}class="active"{/if}>
        <a href="#lang{$lang.id_lang}" role="tab" data-toggle="tab">{$lang.iso_code}</a>
      </li>
      {/foreach}
    </ul>
    <div class="tab-content">
      {foreach from=$languages item=lang}
      <div class="tab-pane {if $lang.id_lang == $current_id_lang}active{/if}" id="lang{$lang.id_lang}">
        <div class="form-group" {if $lang.id_lang == $current_id_lang}data-step="1" data-intro="{l s='Provide your event name. Remember to give clear and short names this will help users to find your event.' mod='npsmarketplace'}"{/if}>
          <label class="required" for="product_name_{$lang.id_lang}">{l s='Event name' mod='npsmarketplace'}</label>
          <input class="validate form-control" data-validate="isGenericName" type="text" id="product_name_{$lang.id_lang}" name="name[{$lang.id_lang}]"
          value="{if isset($smarty.post.name[{$lang.id_lang}])}{$smarty.post.name[{$lang.id_lang}]}{else}{if isset($product['name'][{$lang.id_lang}])}{$product['name'][{$lang.id_lang}]}{/if}{/if}"/>
        </div>
        <div class="form-group" {if $lang.id_lang == $current_id_lang}data-step="2" data-intro="{l s='Short description is visible on events list, inside categories and calendar. Describe your event in couple words.' mod='npsmarketplace'}"{/if}>
          <label for="product_short_description_{$lang.id_lang}">{l s='Short Description' mod='npsmarketplace'}</label>
          <textarea class="tinymce form-control" id="product_short_description_{$lang.id_lang}" name="description_short[{$lang.id_lang}]">{if isset($smarty.post.description_short[{$lang.id_lang}])}{$smarty.post.description_short[{$lang.id_lang}]}{else}{if isset($product['description_short'][{$lang.id_lang}])}{$product['description_short'][{$lang.id_lang}]}{/if}{/if}</textarea>
          <span class="form_info">{l s='Add here key informations. Short description will be visible on list items' mod='npsmarketplace'}</span>
        </div>
        <div class="form-group" {if $lang.id_lang == $current_id_lang}data-step="3" data-intro="{l s='This description is visible on event details page. Provide here all important informations for customers about your event.' mod='npsmarketplace'}"{/if}>
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
      <div class="form-group col-md-6" data-step="4" data-intro="{l s='Select province where your event will be organized.' mod='npsmarketplace'}">
        <label class="required" for="product_province">{l s='Province' mod='npsmarketplace'}</label>
        <select class="form-control" id="product_province" name="province">
          {foreach from=$provinces item=province}
          <option value="{$province.id_feature_value}" {if isset($smarty.post.province) && $smarty.post.province == $province.id_feature_value}selected{else}{if $product.province eq $province.id_feature_value}selected{/if}{/if}>{$province.name}</option>
          {/foreach}
        </select>
      </div>
      <div class="form-group col-md-6" data-step="5" data-intro="{l s='Select town where your event will be organized. If your town is not available you can send information to us and we will add it to list.' mod='npsmarketplace'}">
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
      <div class="form-group col-md-6" data-step="6" data-intro="{l s='Based on province and town selection provide event address. You can also use map to specify location by dragging and dropping red marker on event location.' mod='npsmarketplace'}">
        <label class="required" for="map-address-input">{l s='Address' mod='npsmarketplace'}</label>
        <input id="map-address-input" class="is_required validate form-control" data-validate="isMessage" name="address" type="text" required="" placeholder="{l s='Search adress...' mod='npsmarketplace'}"
        value="{if isset($smarty.post.address)}{$smarty.post.address}{else}{if isset($product['address'])}{$product['address']|escape:'html':'UTF-8'}{/if}{/if}"/>
        <span class="form_info">{l s='After you specify address please check town and district. This inputs are not adjusted automatically.' mod='npsmarketplace'}</span>
        <input id="map-lat-input" class="hide" name="lat" type="text"
        value="{if isset($smarty.post.lat)}{$smarty.post.lat}{else}{if isset($product['lat'])}{$product['lat']|escape:'html':'UTF-8'}{/if}{/if}">
        <input id="map-lng-input" class="hide" name="lng" type="text"
        value="{if isset($smarty.post.lng)}{$smarty.post.lng}{else}{if isset($product['lng'])}{$product['lng']|escape:'html':'UTF-8'}{/if}{/if}">
      </div>
      <div class="form-group col-md-6" data-step="7" data-intro="{l s='Additionaly you can provide town district. This can be helpful for users when they want serach events by districts.' mod='npsmarketplace'}">
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

  <div class="box variants-box" data-step="8" data-intro="{l s='Here is list of your event tickets.' mod='npsmarketplace'}">
    <ul class="nav navbar-nav pull-right variants-dropdown">
      <li class="dropdown">
        <a href="#" data-toggle="dropdown" class="dropdown-toggle"><i class="icon-plus"></i> {l s='Add' mod='npsmarketplace'}<b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li>
            <a href="#ticket_combination" class="add-variant-btn add-ticket">{l s='Ticket' mod='npsmarketplace'}</a>
          </li>
          <li>
            <a href="#carnet_combination" class="add-variant-btn add-carnet">{l s='Carnet' mod='npsmarketplace'}</a>
          </li>
          <li>
            <a href="#ad_combination" class="add-variant-btn add-ad">{l s='Advertisment' mod='npsmarketplace'}</a>
          </li>
          {if $seller->outer_adds}
          <li>
            <a href="#outer_ad_combination" class="add-variant-btn add-ext-ad">{l s='Outer Advertisment' mod='npsmarketplace'}</a>
          </li>
          {/if}
        </ul>
      </li>
    </ul>
    <h3 class="page-heading">{l s='Variants' mod='npsmarketplace'}</h3>
    <div class="variants-container"></div>
    <p class="alert alert-warning no-variants">
      <span class="alert-content">{l s='No wariants' mod='npsmarketplace'}</span>
    </p>
  </div>

  <div class="box">
    <h3 class="page-heading">{l s='Media' mod='npsmarketplace'}</h3>
    <div class="form-group">
      <label class="required">{l s='Pictures' mod='npsmarketplace'}</label>
      <div class="dropzone" id="dropzone-container" data-step="9" data-intro="{l s='Here upload at least one picture. You can upload picture by clicking on this area or by dropping file inside it. Remeber to prepare beauty image! This will bring more interest on your event' mod='npsmarketplace'}">
        <div class="dropzone-previews"></div>
        <div class="fallback">
          <input name="file[]" type="file" multiple />
        </div>
      </div>
      <span class="form_info">{l s='First image will be general image.' mod='npsmarketplace'} <a href="{$images_how_to_url}">{l s='See how to add images.' mod='npsmarketplace'}</a></span>
      <p class="alert alert-info waitForImages">
        <span class="alert-content">{l s='Sending images in progress. Wait for the result.' mod='npsmarketplace'}</span>
      </p>
    </div>
    <div class="row">
      <div class="form-group col-xs-12" data-step="10" data-intro="{l s='Additionaly here you can add video about your event. Go to youtube open your video. Right click on it and select \'Copy embeded video code\'. Pase this code here!' mod='npsmarketplace'}">
        <label for="video_url">{l s='Video embeded code/URL' mod='npsmarketplace'}</label>
        <textarea class="form-control textarea-autosize" id="video_url" name="video_url">{if isset($smarty.post.video_url)}{$smarty.post.video_url}{else}{if isset($product['video_url'])}{$product['video_url']|escape:'html':'UTF-8'}{/if}{/if}</textarea>
        <span class="form_info">{l s='Paste embeded video code (YouTube, Vimeo)' mod='npsmarketplace'} <a href="{$vide_how_to_url}">{l s='See how to add video' mod='npsmarketplace'}</a></span>
      </div>
    </div>
  </div>

  <div class="box">
    <h3 class="page-heading">{l s='Identification' mod='npsmarketplace'}</h3>
    <p class="alert alert-info">
      <span class="alert-content">{l s='You can select more than one category.' mod='npsmarketplace'}</span>
    </p>
    <div class="row">
      <div class="form-group col-md-6" data-step="11" data-intro="{l s='Select category which best fits to your event.' mod='npsmarketplace'}">
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
      <div class="form-group col-md-6" data-step="12" data-intro="{l s='Here are special categories which are available depending on yoear events. If any category fits to your event select it!' mod='npsmarketplace'}">
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

  <div class="box questions-box" data-step="13" data-intro="{l s='Here is list of your giestions to customers' mod='npsmarketplace'}">
    <h3 class="page-heading with-button">{l s='Questions' mod='npsmarketplace'}<a href="#question_box" class="btn btn-default button button-small pull-right add-question-btn"><i class="icon-plus"></i> {l s='Add' mod='npsmarketplace'}</a></h3>

    <div class="questions-container"></div>
    <p class="alert alert-info no-questions">
      <span class="alert-content">{l s='No questions' mod='npsmarketplace'}</span>
    </p>
  </div>
</fieldset>